<?php

namespace App\Services;

use App\Helpers\CurrencyHelper;
use App\Models\FinancialTransaction;
use App\Models\GoodsReceivedNote;
use App\Models\SupplierPayable;
use App\Models\SupplierPayment;
use App\Models\SupplierPaymentAllocation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class SupplierPayablesService
{
    public function syncApprovedGrnPayables(string $actorId, ?string $supplierId = null): int
    {
        $created = 0;

        GoodsReceivedNote::query()
            ->where('status', GoodsReceivedNote::STATUS_APPROVED)
            ->whereNotNull('supplier_id')
            ->when($supplierId, fn ($query) => $query->where('supplier_id', $supplierId))
            ->whereNotExists(function (Builder $query): void {
                $query->select(DB::raw(1))
                    ->from('supplier_payables')
                    ->whereColumn('supplier_payables.source_reference_id', 'goods_received_notes.id')
                    ->where('supplier_payables.source_module', 'procurement');
            })
            ->with('supplier')
            ->chunk(50, function ($grns) use (&$created, $actorId): void {
                foreach ($grns as $grn) {
                    $this->ensurePayableFromGrn($grn, $actorId);
                    $created++;
                }
            });

        return $created;
    }

    public function ensurePayableFromGrn(GoodsReceivedNote $grn, string $actorId): SupplierPayable
    {
        return DB::transaction(function () use ($grn, $actorId) {
            $payable = SupplierPayable::firstOrNew([
                'source_module' => 'procurement',
                'source_reference_id' => $grn->id,
            ]);

            if (! $payable->exists) {
                $payable->created_by = $actorId;
            }

            $payable->fill([
                'supplier_id' => $grn->supplier_id,
                'reference' => $grn->grn_number,
                'payable_date' => $grn->received_date ?? now()->toDateString(),
                'currency' => CurrencyHelper::getDefaultCurrency(),
                'amount_total' => (float) $grn->grand_total,
                'journal_entry_id' => $grn->accounting_journal_entry_id,
                'source_reference_type' => 'grn',
                'notes' => $grn->notes,
            ]);

            if (! $payable->exists) {
                $payable->amount_paid = 0;
            }

            $payable->balance = max(round((float) $payable->amount_total - (float) $payable->amount_paid, 2), 0);
            $payable->status = $payable->balance <= 0 ? 'paid' : ((float) $payable->amount_paid > 0 ? 'partial' : 'unpaid');
            $payable->save();

            return $payable->fresh(['supplier', 'journalEntry']);
        });
    }

    public function allocatePayment(SupplierPayment $payment, array $allocations, string $actorId): SupplierPayment
    {
        try {
            return DB::transaction(function () use ($payment, $allocations, $actorId) {
                $payment = SupplierPayment::with('allocations')->lockForUpdate()->findOrFail($payment->id);

                if (in_array($payment->status, ['posted', 'cancelled'], true)) {
                    throw ValidationException::withMessages([
                        'payment' => 'Posted or cancelled supplier payments cannot be changed.',
                    ]);
                }

                foreach ($payment->allocations as $existingAllocation) {
                    $payable = SupplierPayable::lockForUpdate()->findOrFail($existingAllocation->supplier_payable_id);
                    $payable->amount_paid = max(round((float) $payable->amount_paid - (float) $existingAllocation->allocated_amount, 2), 0);
                    $payable->recalculateStatus();
                }

                $payment->allocations()->delete();

                $normalizedAllocations = collect($allocations)
                    ->map(fn ($value, $payableId) => [
                        'payable_id' => $payableId,
                        'amount' => round((float) $value, 2),
                    ])
                    ->filter(fn (array $row) => $row['amount'] > 0)
                    ->values();

                if ($normalizedAllocations->isEmpty()) {
                    throw ValidationException::withMessages([
                        'allocations' => __('accountant.ap.allocation_required'),
                    ]);
                }

                $totalAllocated = round((float) $normalizedAllocations->sum('amount'), 2);

                if ($totalAllocated - (float) $payment->amount > 0.01) {
                    throw ValidationException::withMessages([
                        'allocations' => 'Allocated total cannot exceed the payment amount.',
                    ]);
                }

                foreach ($normalizedAllocations as $row) {
                    $payable = SupplierPayable::lockForUpdate()->findOrFail($row['payable_id']);

                    if ($payable->supplier_id !== $payment->supplier_id) {
                        throw ValidationException::withMessages([
                            'allocations' => 'All allocations must belong to the same supplier payment.',
                        ]);
                    }

                    if ($row['amount'] - (float) $payable->balance > 0.01) {
                        throw ValidationException::withMessages([
                            'allocations' => "Allocation exceeds balance for payable {$payable->reference}.",
                        ]);
                    }

                    SupplierPaymentAllocation::create([
                        'supplier_payment_id' => $payment->id,
                        'supplier_payable_id' => $payable->id,
                        'allocated_amount' => $row['amount'],
                        'created_by' => $actorId,
                    ]);

                    $payable->amount_paid = round((float) $payable->amount_paid + $row['amount'], 2);
                    $payable->recalculateStatus();
                }

                // Move to pending approval only when the payment is fully allocated.
                $payment->update([
                    'status' => abs($totalAllocated - (float) $payment->amount) <= 0.01
                        ? 'pending_approval'
                        : 'draft',
                ]);

                return $payment->fresh(['supplier', 'allocations.payable']);
            }, 3);
        } catch (ValidationException $e) {
            Log::warning('Supplier payment allocation failed validation', [
                'supplier_payment_id' => $payment->id,
                'actor_id' => $actorId,
                'error' => collect($e->errors())->flatten()->first(),
            ]);
            throw $e;
        } catch (Throwable $e) {
            Log::error('Supplier payment allocation failed', [
                'supplier_payment_id' => $payment->id,
                'actor_id' => $actorId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function postPayment(SupplierPayment $payment, string $actorId): SupplierPayment
    {
        try {
            return DB::transaction(function () use ($payment, $actorId) {
                $payment = SupplierPayment::with(['allocations.payable', 'supplier'])->lockForUpdate()->findOrFail($payment->id);

                if (in_array($payment->status, ['posted', 'cancelled'], true)) {
                    throw ValidationException::withMessages([
                        'payment' => 'This supplier payment can no longer be posted.',
                    ]);
                }

                if (! in_array($payment->status, ['draft', 'pending_approval'], true)) {
                    throw ValidationException::withMessages([
                        'payment' => 'Only draft or pending-approval supplier payments can be posted.',
                    ]);
                }

                $allocated = round((float) $payment->allocations->sum('allocated_amount'), 2);

                if ($allocated - (float) $payment->amount > 0.01) {
                    throw ValidationException::withMessages([
                        'allocations' => 'Allocated total cannot exceed the payment amount.',
                    ]);
                }

                $journalEntry = app(AccountingService::class)->postSupplierPayment(
                    reference: $payment->reference ?: ('SUPPAY-' . $payment->id),
                    sourceId: $payment->id,
                    supplierId: $payment->supplier_id,
                    amount: (float) $payment->amount,
                    paymentMethod: $payment->method,
                    actorId: $actorId,
                    paymentDate: $payment->payment_date?->toDateString() ?? now()->toDateString(),
                );

                $exchangeRate = CurrencyHelper::getExchangeRate();
                $amount = (float) $payment->amount;
                $amountUsd = $payment->currency === 'TZS'
                    ? round(CurrencyHelper::convert($amount, 'TZS', 'USD'), 2)
                    : $amount;

                $financialTransaction = FinancialTransaction::record([
                    'type' => 'adjustment',
                    'source_module' => 'other',
                    'currency' => $payment->currency,
                    'amount' => $amount,
                    'amount_usd' => $amountUsd,
                    'exchange_rate' => $payment->currency === 'TZS' ? $exchangeRate : 1,
                    'payment_method' => $this->mapFinancialMethod($payment->method),
                    'description' => 'Supplier payment posted - ' . ($payment->reference ?: $payment->id),
                ], $actorId);

                $payment->update([
                    'status' => 'posted',
                    'journal_entry_id' => $journalEntry->id,
                    'financial_transaction_id' => $financialTransaction->id,
                    'posted_by' => $actorId,
                    'posted_at' => now(),
                ]);

                return $payment->fresh(['supplier', 'allocations.payable', 'journalEntry', 'financialTransaction']);
            }, 3);
        } catch (ValidationException $e) {
            Log::warning('Supplier payment posting failed validation', [
                'supplier_payment_id' => $payment->id,
                'actor_id' => $actorId,
                'error' => collect($e->errors())->flatten()->first(),
            ]);
            throw $e;
        } catch (Throwable $e) {
            Log::error('Supplier payment posting failed', [
                'supplier_payment_id' => $payment->id,
                'actor_id' => $actorId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function cancelPayment(SupplierPayment $payment, string $actorId, string $reason): SupplierPayment
    {
        try {
            return DB::transaction(function () use ($payment, $actorId, $reason) {
                $payment = SupplierPayment::with('allocations.payable')->lockForUpdate()->findOrFail($payment->id);

                if ($payment->status === 'cancelled') {
                    throw ValidationException::withMessages([
                        'payment' => 'This supplier payment is already cancelled.',
                    ]);
                }

                if ($payment->status !== 'posted') {
                    throw ValidationException::withMessages([
                        'payment' => 'Only posted supplier payments can be cancelled.',
                    ]);
                }

                foreach ($payment->allocations as $allocation) {
                    $payable = SupplierPayable::lockForUpdate()->findOrFail($allocation->supplier_payable_id);
                    $payable->amount_paid = max(round((float) $payable->amount_paid - (float) $allocation->allocated_amount, 2), 0);
                    $payable->recalculateStatus();
                }

                $reversalReference = ($payment->reference ?: ('SUPPAY-' . $payment->id)) . '-REV';
                $journalEntry = app(AccountingService::class)->reverseSupplierPayment(
                    reference: $reversalReference,
                    sourceId: $payment->id,
                    supplierId: $payment->supplier_id,
                    amount: (float) $payment->amount,
                    paymentMethod: $payment->method,
                    actorId: $actorId,
                    paymentDate: now()->toDateString(),
                );

                $payment->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => $reason,
                    'cancelled_by' => $actorId,
                    'cancelled_at' => now(),
                ]);

                return $payment->fresh(['supplier', 'allocations.payable', 'canceller']);
            }, 3);
        } catch (ValidationException $e) {
            Log::warning('Supplier payment cancellation validation failed', [
                'supplier_payment_id' => $payment->id,
                'actor_id' => $actorId,
                'error' => collect($e->errors())->flatten()->first(),
            ]);
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Supplier payment cancellation failed', [
                'supplier_payment_id' => $payment->id,
                'actor_id' => $actorId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function deletePayment(SupplierPayment $payment, string $actorId): void
    {
        try {
            DB::transaction(function () use ($payment, $actorId): void {
                $payment = SupplierPayment::with('allocations')->lockForUpdate()->findOrFail($payment->id);

                if ($payment->status !== 'draft') {
                    throw ValidationException::withMessages([
                        'payment' => __('accountant.ap.delete_only_draft_allowed'),
                    ]);
                }

                foreach ($payment->allocations as $allocation) {
                    $payable = SupplierPayable::lockForUpdate()->findOrFail($allocation->supplier_payable_id);
                    $payable->amount_paid = max(round((float) $payable->amount_paid - (float) $allocation->allocated_amount, 2), 0);
                    $payable->recalculateStatus();
                }

                $payment->allocations()->delete();
                $payment->delete();

                Log::info('Supplier payment deleted', [
                    'supplier_payment_id' => $payment->id,
                    'actor_id' => $actorId,
                ]);
            }, 3);
        } catch (ValidationException $e) {
            Log::warning('Supplier payment delete failed validation', [
                'supplier_payment_id' => $payment->id,
                'actor_id' => $actorId,
                'error' => collect($e->errors())->flatten()->first(),
            ]);
            throw $e;
        } catch (Throwable $e) {
            Log::error('Supplier payment delete failed', [
                'supplier_payment_id' => $payment->id,
                'actor_id' => $actorId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function mapFinancialMethod(string $method): string
    {
        return match ($method) {
            'cash' => 'cash',
            'card' => 'card',
            'mobile' => 'mobile_money',
            default => 'bank_transfer',
        };
    }
}
