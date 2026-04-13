<?php

namespace App\Services;

use App\Helpers\CurrencyHelper;
use App\Models\FinancialTransaction;
use App\Models\GoodsReceivedNote;
use App\Models\SupplierPayable;
use App\Models\SupplierPayment;
use App\Models\SupplierPaymentAllocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierPayablesService
{
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

            return $payment->fresh(['supplier', 'allocations.payable']);
        });
    }

    public function postPayment(SupplierPayment $payment, string $actorId): SupplierPayment
    {
        return DB::transaction(function () use ($payment, $actorId) {
            $payment = SupplierPayment::with(['allocations.payable', 'supplier'])->lockForUpdate()->findOrFail($payment->id);

            if (in_array($payment->status, ['posted', 'cancelled'], true)) {
                throw ValidationException::withMessages([
                    'payment' => 'This supplier payment can no longer be posted.',
                ]);
            }

            $allocated = round((float) $payment->allocations->sum('allocated_amount'), 2);

            if ($allocated <= 0) {
                throw ValidationException::withMessages([
                    'allocations' => 'Allocate this payment before posting it.',
                ]);
            }

            if (abs($allocated - (float) $payment->amount) > 0.01) {
                throw ValidationException::withMessages([
                    'allocations' => 'Allocated amount must equal the supplier payment amount before posting.',
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
        });
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
