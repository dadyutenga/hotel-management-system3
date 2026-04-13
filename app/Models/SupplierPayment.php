<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'posted_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

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
}
