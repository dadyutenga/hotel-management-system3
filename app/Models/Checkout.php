<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Checkout extends Model
{
    use HasUuid;

    protected $fillable = [
        'booking_id', 'receipt_number', 'status',
        'total_charges_usd', 'discount_usd', 'grand_total_usd',
        'exchange_rate', 'grand_total_tzs',
        'paid_cash_usd', 'paid_card_usd', 'paid_cash_tzs', 'paid_card_tzs',
        'total_paid_usd', 'change_due_usd',
        'payment_method', 'notes', 'initiated_by', 'completed_by', 'completed_at',
    ];

    protected $casts = [
        'total_charges_usd' => 'decimal:2',
        'discount_usd'      => 'decimal:2',
        'grand_total_usd'   => 'decimal:2',
        'exchange_rate'      => 'decimal:4',
        'grand_total_tzs'    => 'decimal:2',
        'paid_cash_usd'      => 'decimal:2',
        'paid_card_usd'      => 'decimal:2',
        'total_paid_usd'     => 'decimal:2',
        'change_due_usd'     => 'decimal:2',
        'completed_at'       => 'datetime',
    ];

    // Auto-generate receipt number
    protected static function booted(): void
    {
        static::creating(function (Checkout $checkout) {
            $count = self::whereDate('created_at', today())->count() + 1;
            $checkout->receipt_number = 'RCPT-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function booking()   { return $this->belongsTo(Booking::class); }
    public function initiator() { return $this->belongsTo(User::class, 'initiated_by'); }
    public function completer() { return $this->belongsTo(User::class, 'completed_by'); }
    public function charges()   { return $this->hasMany(BookingCharge::class); }
    public function payments()  { return $this->hasMany(FinancePayment::class); }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Pull all unpaid charges for this booking and calculate totals.
     */
    public function calculateTotals(): void
    {
        $exchangeRate = (float) (DB::table('system_settings')
            ->where('key', 'tzs_exchange_rate')
            ->value('value') ?? 2500);

        $totalUsd = BookingCharge::where('booking_id', $this->booking_id)
            ->where('status', 'unpaid')
            ->sum('amount');

        $grandTotal = $totalUsd - $this->discount_usd;

        $this->update([
            'total_charges_usd' => $totalUsd,
            'grand_total_usd'   => $grandTotal,
            'exchange_rate'      => $exchangeRate,
            'grand_total_tzs'    => round($grandTotal * $exchangeRate, 2),
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getBalanceDueAttribute(): float
    {
        return max(0, (float) $this->grand_total_usd - (float) $this->total_paid_usd);
    }
}
