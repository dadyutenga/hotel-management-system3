<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use LogicException;

class SupplierPayment extends Model
{
    use HasUuid;

    protected $fillable = [
        'supplier_id',
        'payment_date',
        'currency',
        'amount',
        'method',
        'reference',
        'status',
        'journal_entry_id',
        'financial_transaction_id',
        'created_by',
        'posted_by',
        'posted_at',
        'cancelled_by',
        'cancelled_at',
        'notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'posted_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $payment): void {
            if (blank($payment->reference)) {
                $payment->reference = self::generateReference();
            }
        });

        static::deleting(function (self $payment): void {
            if ($payment->status === 'posted') {
                throw new LogicException('Posted supplier payments cannot be deleted. Cancel the payment instead.');
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function financialTransaction(): BelongsTo
    {
        return $this->belongsTo(FinancialTransaction::class, 'financial_transaction_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(SupplierPaymentAllocation::class);
    }

    public function allocatedAmount(): float
    {
        return (float) $this->allocations()->sum('allocated_amount');
    }

    public static function generateReference(): string
    {
        $prefix = 'SUPPAY-' . now()->format('Ymd') . '-';
        $baseCount = self::query()->where('reference', 'like', $prefix . '%')->count();

        for ($attempt = 1; $attempt <= 50; $attempt++) {
            $reference = $prefix . str_pad((string) ($baseCount + $attempt), 4, '0', STR_PAD_LEFT);

            if (! self::query()->where('reference', $reference)->exists()) {
                return $reference;
            }
        }

        return $prefix . strtoupper(Str::random(6));
    }
}
