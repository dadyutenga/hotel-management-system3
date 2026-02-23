# 💳 Checkout, Payment & Financial System — Laravel + Blade
### Hotel Checkout · Multi-Currency (USD & TZS) · All Modules Feed Into This · Receipts · Financial Basement

> This is the financial layer that sits under every module.
> Store, Bar, Restaurant, Laundry — all charges flow into `booking_charges`.
> Walk-in sales (cash/digital) flow into `payments`.
> Everything is visible in the Financial Dashboard.

---

## 📋 Table of Contents

1. [System Architecture](#1-system-architecture)
2. [File Map](#2-file-map)
3. [Migrations](#3-migrations)
4. [Updated BookingCharge Model](#4-updated-bookingcharge-model)
5. [New Models](#5-new-models)
6. [Controllers](#6-controllers)
7. [Blade Views](#7-blade-views)
8. [Routes](#8-routes)
9. [Business Rules](#9-business-rules)
10. [Build Order](#10-build-order)

---

## 1. System Architecture

```
EVERY MODULE
    │
    ├── Bar/Restaurant sale (walk-in)    ──→  payments table          (cash or digital, settled immediately)
    │                                          └── payment_items
    │
    ├── Bar/Restaurant sale (guest)      ──→  booking_charges table   (unpaid, settled at checkout)
    ├── Laundry charge (guest)           ──→  booking_charges table
    ├── Store/minibar charge (guest)     ──→  booking_charges table
    └── Any other hotel service (guest)  ──→  booking_charges table
                                               │
                                               ▼
                                        CHECKOUT PROCESS
                                               │
                                        ┌──────┴──────┐
                                        │             │
                                   currency        folio
                                  (USD / TZS)   (grouped receipt
                                  conversion     by charge_type)
                                        │
                                        ▼
                                   checkouts table        ← one record per guest checkout
                                        │
                                        ▼
                                   receipt generated      ← printable Blade view
                                        │
                                        ▼
                              financial_transactions      ← immutable ledger for all money in
```

### Currency Rules
- System stores all amounts in **USD** internally
- **TZS** conversion applied on display and receipt using exchange rate from `system_settings`
- Payments can be received in USD or TZS — stored with `currency` and `exchange_rate_used`

### charge_type values (from your existing model, kept as-is)
```
laundry | restaurant | room_service | damage | minibar | extra_bed | conference | store
```

---

## 2. File Map

```
app/
├── Http/
│   └── Controllers/
│       └── Finance/                              ← new subfolder inside Controllers/
│           ├── CheckoutController.php
│           ├── PaymentController.php
│           ├── ReceiptController.php
│           └── FinancialDashboardController.php
│
└── Models/
    ├── BookingCharge.php                         ← update existing model
    ├── Checkout.php                              ← new
    ├── Payment.php                               ← new
    ├── PaymentItem.php                           ← new
    └── FinancialTransaction.php                  ← new (immutable ledger)

database/
└── migrations/
    ├── xxxx_update_booking_charges_table.php     ← add source column, keep existing
    ├── xxxx_create_checkouts_table.php
    ├── xxxx_create_payments_table.php
    ├── xxxx_create_payment_items_table.php
    └── xxxx_create_financial_transactions_table.php

resources/
└── views/
    └── finance/                                  ← new subfolder inside views/
        ├── layout.blade.php
        ├── checkout/
        │   ├── show.blade.php                    ← guest folio / bill review
        │   └── confirm.blade.php                 ← payment confirmation
        ├── receipts/
        │   ├── guest.blade.php                   ← printable guest receipt
        │   └── walkin.blade.php                  ← printable walk-in receipt
        ├── payments/
        │   └── index.blade.php                   ← walk-in payments log
        └── dashboard/
            └── index.blade.php                   ← financial overview

routes/
└── web.php                                       ← add finance routes here
```

---

## 3. Migrations

---

**File:** `database/migrations/xxxx_update_booking_charges_table.php`

> Adds `source` and `currency` to your existing table. Safe to run — uses `->after()`.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_charges', function (Blueprint $table) {
            // Add source column if not already present
            if (!Schema::hasColumn('booking_charges', 'source')) {
                $table->string('source', 50)->default('hotel')->after('charge_type');
            }
            // Currency support
            if (!Schema::hasColumn('booking_charges', 'currency')) {
                $table->enum('currency', ['USD', 'TZS'])->default('USD')->after('amount');
            }
            if (!Schema::hasColumn('booking_charges', 'amount_tzs')) {
                $table->decimal('amount_tzs', 12, 2)->default(0)->after('currency');
            }
            // Checkout FK — set when guest pays at checkout
            if (!Schema::hasColumn('booking_charges', 'checkout_id')) {
                $table->uuid('checkout_id')->nullable()->after('booking_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_charges', function (Blueprint $table) {
            $table->dropColumn(['source', 'currency', 'amount_tzs', 'checkout_id']);
        });
    }
};
```

---

**File:** `database/migrations/xxxx_create_checkouts_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // One checkout record per guest stay — created when FRONT_DESK initiates checkout
        Schema::create('checkouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id')->unique();           // one checkout per booking
            $table->string('receipt_number', 30)->unique(); // RCPT-20240222-0042
            $table->enum('status', [
                'pending',      // folio being reviewed
                'processing',   // payment being taken
                'completed',    // fully paid and checked out
                'cancelled',    // checkout cancelled (guest staying on)
            ])->default('pending');

            // Totals
            $table->decimal('total_charges_usd', 12, 2)->default(0); // sum of all booking_charges
            $table->decimal('discount_usd', 12, 2)->default(0);
            $table->decimal('grand_total_usd', 12, 2)->default(0);
            $table->decimal('exchange_rate', 12, 4)->default(1);      // TZS per 1 USD at time of checkout
            $table->decimal('grand_total_tzs', 14, 2)->default(0);    // grand_total_usd * exchange_rate

            // Payment split — guest can pay part cash, part card
            $table->decimal('paid_cash_usd', 12, 2)->default(0);
            $table->decimal('paid_card_usd', 12, 2)->default(0);
            $table->decimal('paid_cash_tzs', 14, 2)->default(0);
            $table->decimal('paid_card_tzs', 14, 2)->default(0);
            $table->decimal('total_paid_usd', 12, 2)->default(0);
            $table->decimal('change_due_usd', 12, 2)->default(0);     // overpayment returned

            $table->enum('payment_method', [
                'cash_usd',
                'cash_tzs',
                'card_usd',
                'card_tzs',
                'split',        // multiple methods
            ])->nullable();

            $table->text('notes')->nullable();
            $table->uuid('initiated_by');                              // FRONT_DESK
            $table->uuid('completed_by')->nullable();                  // CASHIER
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings');
            $table->foreign('initiated_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
};
```

---

**File:** `database/migrations/xxxx_create_payments_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Walk-in sales payments AND checkout payments both live here
        // Every money movement in = one payment record
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_number', 30)->unique();  // PAY-20240222-0042
            $table->enum('payment_type', [
                'checkout',     // hotel guest checkout
                'walkin',       // walk-in bar/restaurant customer
                'advance',      // advance payment / deposit
            ]);
            $table->uuid('checkout_id')->nullable();         // FK if payment_type = checkout
            $table->uuid('order_id')->nullable();            // FK if payment_type = walkin
            $table->uuid('booking_id')->nullable();          // FK if related to a booking

            $table->enum('currency', ['USD', 'TZS']);
            $table->decimal('amount', 14, 2);                // amount in chosen currency
            $table->decimal('amount_usd', 12, 2);            // always stored in USD too
            $table->decimal('exchange_rate', 12, 4)->default(1);

            $table->enum('method', [
                'cash',
                'card',
                'mobile_money',  // e.g. M-Pesa, Tigo Pesa
                'bank_transfer',
            ]);

            $table->enum('status', [
                'pending',
                'completed',
                'failed',
                'refunded',
            ])->default('pending');

            $table->string('reference', 100)->nullable();    // card/mobile transaction ref
            $table->text('notes')->nullable();
            $table->uuid('created_by');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('checkout_id')->references('id')->on('checkouts')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');

            $table->index(['payment_type', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
```

---

**File:** `database/migrations/xxxx_create_payment_items_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Line items breakdown on a walk-in payment
        Schema::create('payment_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payment_id');
            $table->uuid('order_item_id')->nullable();       // from order_items
            $table->string('description', 255);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->enum('currency', ['USD', 'TZS'])->default('USD');
            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('payments')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_items');
    }
};
```

---

**File:** `database/migrations/xxxx_create_financial_transactions_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Immutable financial ledger — one row per money-in event.
        // Never updated. Never deleted. Basement of the financial system.
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_number', 30)->unique();

            $table->enum('type', [
                'checkout_payment',  // guest checkout
                'walkin_sale',       // walk-in bar/restaurant
                'advance_payment',   // deposit received
                'refund',            // money returned
                'adjustment',        // manual finance correction
            ]);

            // Module source
            $table->enum('source_module', [
                'restaurant',
                'bar',
                'store',
                'laundry',
                'accommodation',
                'other',
            ]);

            $table->uuid('payment_id')->nullable();          // FK to payments
            $table->uuid('booking_id')->nullable();
            $table->uuid('order_id')->nullable();

            $table->enum('currency', ['USD', 'TZS']);
            $table->decimal('amount', 14, 2);
            $table->decimal('amount_usd', 12, 2);
            $table->decimal('exchange_rate', 12, 4)->default(1);

            $table->enum('payment_method', [
                'cash', 'card', 'mobile_money', 'bank_transfer'
            ]);

            $table->text('description');
            $table->uuid('created_by');
            $table->timestamp('created_at');                 // no updated_at — immutable

            $table->foreign('payment_id')->references('id')->on('payments')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');

            $table->index(['type', 'created_at']);
            $table->index(['source_module', 'created_at']);
            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
```

---

## 4. Updated BookingCharge Model

**File:** `app/Models/BookingCharge.php` — your existing model updated with new relationships and currency support.

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingCharge extends Model
{
    use HasUuid;

    protected $fillable = [
        'booking_id',
        'checkout_id',
        'order_id',
        'charge_type',
        'source',
        'reference_id',
        'description',
        'amount',
        'currency',
        'amount_tzs',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'amount_tzs' => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function checkout(): BelongsTo
    {
        return $this->belongsTo(Checkout::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function laundryOrder(): BelongsTo
    {
        return $this->belongsTo(LaundryOrder::class, 'reference_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('charge_type', $type);
    }

    public function scopeForBooking($query, string $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    // ── Methods ──────────────────────────────────────────────────────────────

    public function markAsPaid(): bool
    {
        return $this->update(['status' => 'paid']);
    }

    public function isUnpaid(): bool
    {
        return $this->status === 'unpaid';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    // Apply TZS amount using current exchange rate from system settings
    public function applyTzsAmount(float $exchangeRate): void
    {
        $this->update(['amount_tzs' => round($this->amount * $exchangeRate, 2)]);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getChargeTypeLabelAttribute(): string
    {
        return match ($this->charge_type) {
            'laundry'      => 'Laundry Service',
            'restaurant'   => 'Restaurant / Bar',
            'room_service' => 'Room Service',
            'damage'       => 'Damage',
            'minibar'      => 'Mini Bar',
            'extra_bed'    => 'Extra Bed',
            'conference'   => 'Conference',
            'store'        => 'Store Items',
            default        => ucfirst(str_replace('_', ' ', $this->charge_type)),
        };
    }

    public function getAmountInTzsAttribute(): float
    {
        return (float) $this->amount_tzs;
    }
}
```

---

## 5. New Models

**File:** `app/Models/Checkout.php`

```php
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
        'exchange_rate'     => 'decimal:4',
        'grand_total_tzs'   => 'decimal:2',
        'paid_cash_usd'     => 'decimal:2',
        'paid_card_usd'     => 'decimal:2',
        'total_paid_usd'    => 'decimal:2',
        'change_due_usd'    => 'decimal:2',
        'completed_at'      => 'datetime',
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

    public function booking()     { return $this->belongsTo(Booking::class); }
    public function initiator()   { return $this->belongsTo(User::class, 'initiated_by'); }
    public function completer()   { return $this->belongsTo(User::class, 'completed_by'); }
    public function charges()     { return $this->hasMany(BookingCharge::class); }
    public function payments()    { return $this->hasMany(Payment::class); }

    // ── Helpers ──────────────────────────────────────────────────────────────

    // Pull all unpaid charges for this booking and calculate totals
    public function calculateTotals(): void
    {
        $exchangeRate = (float) DB::table('system_settings')
            ->where('key', 'tzs_exchange_rate')
            ->value('value') ?? 2500;

        $totalUsd = BookingCharge::where('booking_id', $this->booking_id)
            ->where('status', 'unpaid')
            ->sum('amount');

        $grandTotal = $totalUsd - $this->discount_usd;

        $this->update([
            'total_charges_usd' => $totalUsd,
            'grand_total_usd'   => $grandTotal,
            'exchange_rate'     => $exchangeRate,
            'grand_total_tzs'   => round($grandTotal * $exchangeRate, 2),
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
```

---

**File:** `app/Models/Payment.php`

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuid;

    public $timestamps = false;

    protected $fillable = [
        'payment_number', 'payment_type', 'checkout_id', 'order_id', 'booking_id',
        'currency', 'amount', 'amount_usd', 'exchange_rate',
        'method', 'status', 'reference', 'notes', 'created_by', 'paid_at',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'amount_usd'    => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'paid_at'       => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            $count = self::whereDate('created_at', today())->count() + 1;
            $payment->payment_number = 'PAY-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            $payment->created_at = now();
        });
    }

    public function checkout()    { return $this->belongsTo(Checkout::class); }
    public function order()       { return $this->belongsTo(Order::class); }
    public function items()       { return $this->hasMany(PaymentItem::class); }
    public function createdBy()   { return $this->belongsTo(User::class, 'created_by'); }
    public function transaction() { return $this->hasOne(FinancialTransaction::class); }

    // Convert amount to USD regardless of currency received
    public static function toUsd(float $amount, string $currency, float $exchangeRate): float
    {
        if ($currency === 'USD') return $amount;
        return round($amount / $exchangeRate, 2); // TZS → USD
    }
}
```

---

**File:** `app/Models/FinancialTransaction.php`

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FinancialTransaction extends Model
{
    use HasUuid;

    public $timestamps = false;

    protected $fillable = [
        'transaction_number', 'type', 'source_module',
        'payment_id', 'booking_id', 'order_id',
        'currency', 'amount', 'amount_usd', 'exchange_rate',
        'payment_method', 'description', 'created_by', 'created_at',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'amount_usd'    => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'created_at'    => 'datetime',
    ];

    /**
     * THE CORE METHOD — every financial event goes through here.
     * Immutable. No updates. No deletes.
     */
    public static function record(array $params, string $actorId): self
    {
        $count  = self::whereDate('created_at', today())->count() + 1;
        $txnNum = 'TXN-' . date('Ymd') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

        return self::create([
            'transaction_number' => $txnNum,
            'type'               => $params['type'],
            'source_module'      => $params['source_module'],
            'payment_id'         => $params['payment_id']  ?? null,
            'booking_id'         => $params['booking_id']  ?? null,
            'order_id'           => $params['order_id']    ?? null,
            'currency'           => $params['currency'],
            'amount'             => $params['amount'],
            'amount_usd'         => $params['amount_usd'],
            'exchange_rate'      => $params['exchange_rate'] ?? 1,
            'payment_method'     => $params['payment_method'],
            'description'        => $params['description'],
            'created_by'         => $actorId,
            'created_at'         => now(),
        ]);
    }

    public function payment() { return $this->belongsTo(Payment::class); }
    public function actor()   { return $this->belongsTo(User::class, 'created_by'); }
}
```

---

**File:** `app/Models/PaymentItem.php`

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    use HasUuid;

    protected $fillable = [
        'payment_id', 'order_item_id', 'description',
        'quantity', 'unit_price', 'subtotal', 'currency',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    public function payment()   { return $this->belongsTo(Payment::class); }
}
```

---

## 6. Controllers

---

**File:** `app/Http/Controllers/Finance/CheckoutController.php`

```php
<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingCharge;
use App\Models\Checkout;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\StoreNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    // GET /finance/checkout/{booking}
    // Show the guest folio — all charges grouped by type
    public function show(Booking $booking): View
    {
        // Get or create a pending checkout for this booking
        $checkout = Checkout::firstOrCreate(
            ['booking_id' => $booking->id, 'status' => 'pending'],
            ['initiated_by' => auth()->id()]
        );

        // Get exchange rate
        $exchangeRate = (float) DB::table('system_settings')
            ->where('key', 'tzs_exchange_rate')->value('value') ?? 2500;

        // Fetch all unpaid charges grouped by type
        $charges = BookingCharge::where('booking_id', $booking->id)
            ->where('status', 'unpaid')
            ->with(['order', 'laundryOrder', 'createdBy'])
            ->orderBy('charge_type')
            ->orderBy('created_at')
            ->get();

        $chargesByType = $charges->groupBy('charge_type');

        // Recalculate totals fresh
        $checkout->calculateTotals();
        $checkout->refresh();

        return view('finance.checkout.show', compact(
            'booking', 'checkout', 'charges', 'chargesByType', 'exchangeRate'
        ));
    }

    // POST /finance/checkout/{checkout}/process
    // Process the actual payment and complete checkout
    public function process(Request $request, Checkout $checkout): RedirectResponse
    {
        abort_if($checkout->status === 'completed', 422, 'This checkout is already completed.');

        $data = $request->validate([
            'payment_method'    => 'required|in:cash_usd,cash_tzs,card_usd,card_tzs,split',
            'discount_usd'      => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string|max:500',
            // Split payment fields
            'cash_usd_amount'   => 'required_if:payment_method,split|nullable|numeric|min:0',
            'card_usd_amount'   => 'required_if:payment_method,split|nullable|numeric|min:0',
            'cash_tzs_amount'   => 'nullable|numeric|min:0',
            'card_tzs_amount'   => 'nullable|numeric|min:0',
        ]);

        $exchangeRate = $checkout->exchange_rate;

        DB::transaction(function () use ($data, $checkout, $exchangeRate) {

            // Apply discount if given
            if (!empty($data['discount_usd'])) {
                $checkout->update(['discount_usd' => $data['discount_usd']]);
                $checkout->calculateTotals();
                $checkout->refresh();
            }

            $grandTotalUsd = (float) $checkout->grand_total_usd;

            // Calculate how much was paid in USD equivalent
            $paidCashUsd = 0;
            $paidCardUsd = 0;

            switch ($data['payment_method']) {
                case 'cash_usd':
                    $paidCashUsd = $grandTotalUsd;
                    break;
                case 'card_usd':
                    $paidCardUsd = $grandTotalUsd;
                    break;
                case 'cash_tzs':
                    $paidCashUsd = round($checkout->grand_total_tzs / $exchangeRate, 2);
                    break;
                case 'card_tzs':
                    $paidCardUsd = round($checkout->grand_total_tzs / $exchangeRate, 2);
                    break;
                case 'split':
                    $paidCashUsd = (float) ($data['cash_usd_amount'] ?? 0);
                    $paidCardUsd = (float) ($data['card_usd_amount'] ?? 0);
                    // Add TZS amounts converted
                    $paidCashUsd += round((float) ($data['cash_tzs_amount'] ?? 0) / $exchangeRate, 2);
                    $paidCardUsd += round((float) ($data['card_tzs_amount'] ?? 0) / $exchangeRate, 2);
                    break;
            }

            $totalPaidUsd = $paidCashUsd + $paidCardUsd;
            $changeDueUsd = max(0, $totalPaidUsd - $grandTotalUsd);

            // Update checkout record
            $checkout->update([
                'status'          => 'completed',
                'payment_method'  => $data['payment_method'],
                'paid_cash_usd'   => $paidCashUsd,
                'paid_card_usd'   => $paidCardUsd,
                'paid_cash_tzs'   => round($paidCashUsd * $exchangeRate, 2),
                'paid_card_tzs'   => round($paidCardUsd * $exchangeRate, 2),
                'total_paid_usd'  => $totalPaidUsd,
                'change_due_usd'  => $changeDueUsd,
                'notes'           => $data['notes'] ?? null,
                'completed_by'    => auth()->id(),
                'completed_at'    => now(),
            ]);

            // Mark all booking charges as paid
            BookingCharge::where('booking_id', $checkout->booking_id)
                ->where('status', 'unpaid')
                ->update([
                    'status'      => 'paid',
                    'checkout_id' => $checkout->id,
                ]);

            // Create the payment record
            $payment = Payment::create([
                'payment_type'  => 'checkout',
                'checkout_id'   => $checkout->id,
                'booking_id'    => $checkout->booking_id,
                'currency'      => str_contains($data['payment_method'], 'tzs') ? 'TZS' : 'USD',
                'amount'        => $grandTotalUsd,
                'amount_usd'    => $grandTotalUsd,
                'exchange_rate' => $exchangeRate,
                'method'        => str_contains($data['payment_method'], 'cash') ? 'cash' : 'card',
                'status'        => 'completed',
                'created_by'    => auth()->id(),
                'paid_at'       => now(),
            ]);

            // Write immutable financial transaction record
            FinancialTransaction::record([
                'type'           => 'checkout_payment',
                'source_module'  => 'accommodation',
                'payment_id'     => $payment->id,
                'booking_id'     => $checkout->booking_id,
                'currency'       => $payment->currency,
                'amount'         => $grandTotalUsd,
                'amount_usd'     => $grandTotalUsd,
                'exchange_rate'  => $exchangeRate,
                'payment_method' => $payment->method,
                'description'    => "Guest checkout — Receipt {$checkout->receipt_number}",
            ], auth()->id());
        });

        return redirect()
            ->route('finance.receipt.guest', $checkout)
            ->with('success', 'Checkout completed. Receipt ready.');
    }

    // POST /finance/checkout/{checkout}/add-charge
    // Manually add a charge to a checkout that is still pending
    public function addCharge(Request $request, Checkout $checkout): RedirectResponse
    {
        abort_if($checkout->status !== 'pending', 422, 'Cannot add charges to a completed checkout.');

        $data = $request->validate([
            'charge_type'  => 'required|string|max:50',
            'description'  => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0.01',
        ]);

        $exchangeRate = (float) DB::table('system_settings')
            ->where('key', 'tzs_exchange_rate')->value('value') ?? 2500;

        BookingCharge::create([
            'booking_id'   => $checkout->booking_id,
            'checkout_id'  => $checkout->id,
            'charge_type'  => $data['charge_type'],
            'description'  => $data['description'],
            'amount'       => $data['amount'],
            'currency'     => 'USD',
            'amount_tzs'   => round($data['amount'] * $exchangeRate, 2),
            'status'       => 'unpaid',
            'created_by'   => auth()->id(),
        ]);

        return redirect()
            ->route('finance.checkout.show', $checkout->booking)
            ->with('success', 'Charge added to folio.');
    }
}
```

---

**File:** `app/Http/Controllers/Finance/PaymentController.php`

```php
<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    // GET /finance/payments
    // Walk-in payments log
    public function index(Request $request): View
    {
        $payments = Payment::with(['order', 'createdBy', 'transaction'])
            ->when($request->type,      fn($q) => $q->where('payment_type', $request->type))
            ->when($request->method,    fn($q) => $q->where('method', $request->method))
            ->when($request->currency,  fn($q) => $q->where('currency', $request->currency))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest('created_at')
            ->paginate(30);

        $summary = [
            'total_usd'  => Payment::where('status', 'completed')
                ->whereDate('created_at', today())->sum('amount_usd'),
            'cash_usd'   => Payment::where('status', 'completed')->where('method', 'cash')
                ->whereDate('created_at', today())->sum('amount_usd'),
            'card_usd'   => Payment::where('status', 'completed')->where('method', 'card')
                ->whereDate('created_at', today())->sum('amount_usd'),
        ];

        return view('finance.payments.index', compact('payments', 'summary'));
    }

    // POST /finance/payments/walkin
    // Record a walk-in cash/card/mobile payment for a bar or restaurant order
    public function storeWalkin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_id'     => 'required|uuid|exists:orders,id',
            'currency'     => 'required|in:USD,TZS',
            'amount'       => 'required|numeric|min:0.01',
            'method'       => 'required|in:cash,card,mobile_money,bank_transfer',
            'reference'    => 'nullable|string|max:100',
            'notes'        => 'nullable|string|max:500',
        ]);

        $exchangeRate = (float) DB::table('system_settings')
            ->where('key', 'tzs_exchange_rate')->value('value') ?? 2500;

        $amountUsd = Payment::toUsd(
            (float) $data['amount'],
            $data['currency'],
            $exchangeRate
        );

        DB::transaction(function () use ($data, $amountUsd, $exchangeRate) {

            $order = Order::with('items.menuItem')->findOrFail($data['order_id']);

            $payment = Payment::create([
                'payment_type'  => 'walkin',
                'order_id'      => $order->id,
                'currency'      => $data['currency'],
                'amount'        => $data['amount'],
                'amount_usd'    => $amountUsd,
                'exchange_rate' => $exchangeRate,
                'method'        => $data['method'],
                'status'        => 'completed',
                'reference'     => $data['reference'] ?? null,
                'notes'         => $data['notes'] ?? null,
                'created_by'    => auth()->id(),
                'paid_at'       => now(),
            ]);

            // Create payment items from order items
            foreach ($order->items()->where('status', '!=', 'cancelled')->get() as $item) {
                PaymentItem::create([
                    'payment_id'    => $payment->id,
                    'order_item_id' => $item->id,
                    'description'   => $item->menuItem->name,
                    'quantity'      => $item->quantity,
                    'unit_price'    => $item->unit_price,
                    'subtotal'      => $item->subtotal,
                    'currency'      => 'USD',
                ]);
            }

            // Write to financial ledger
            FinancialTransaction::record([
                'type'           => 'walkin_sale',
                'source_module'  => $order->location->code === 'bar' ? 'bar' : 'restaurant',
                'payment_id'     => $payment->id,
                'order_id'       => $order->id,
                'currency'       => $data['currency'],
                'amount'         => $data['amount'],
                'amount_usd'     => $amountUsd,
                'exchange_rate'  => $exchangeRate,
                'payment_method' => $data['method'],
                'description'    => "Walk-in sale — Order {$order->order_number}",
            ], auth()->id());
        });

        return redirect()
            ->route('finance.receipt.walkin', ['order_id' => $data['order_id']])
            ->with('success', 'Payment recorded. Receipt ready.');
    }
}
```

---

**File:** `app/Http/Controllers/Finance/ReceiptController.php`

```php
<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\BookingCharge;
use App\Models\Checkout;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    // GET /finance/receipts/guest/{checkout}
    // Printable guest checkout receipt
    public function guest(Checkout $checkout): View
    {
        $checkout->load(['booking', 'charges', 'initiator', 'completer', 'payments']);

        $chargesByType = $checkout->charges->groupBy('charge_type');

        $exchangeRate = $checkout->exchange_rate;

        return view('finance.receipts.guest', compact('checkout', 'chargesByType', 'exchangeRate'));
    }

    // GET /finance/receipts/walkin
    // Printable walk-in sale receipt
    public function walkin(Request $request): View
    {
        $orderId = $request->order_id;
        $order   = Order::with(['items.menuItem', 'table', 'location'])->findOrFail($orderId);

        $payment = Payment::where('order_id', $orderId)
            ->where('status', 'completed')
            ->latest('paid_at')
            ->first();

        $exchangeRate = (float) DB::table('system_settings')
            ->where('key', 'tzs_exchange_rate')->value('value') ?? 2500;

        return view('finance.receipts.walkin', compact('order', 'payment', 'exchangeRate'));
    }
}
```

---

**File:** `app/Http/Controllers/Finance/FinancialDashboardController.php`

```php
<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\BookingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinancialDashboardController extends Controller
{
    // GET /finance/dashboard
    public function index(Request $request): View
    {
        $dateFrom = $request->date_from ?? today()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to   ?? today()->toDateString();

        // Revenue by source module
        $revenueByModule = FinancialTransaction::where('type', '!=', 'refund')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->select('source_module', DB::raw('SUM(amount_usd) as total_usd'))
            ->groupBy('source_module')
            ->get();

        // Revenue by payment method
        $revenueByMethod = FinancialTransaction::where('type', '!=', 'refund')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->select('payment_method', DB::raw('SUM(amount_usd) as total_usd'))
            ->groupBy('payment_method')
            ->get();

        // Daily revenue trend (last 30 days)
        $dailyRevenue = FinancialTransaction::where('type', '!=', 'refund')
            ->whereDate('created_at', '>=', today()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount_usd) as total_usd')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Today's summary
        $todaySummary = [
            'total_revenue'   => FinancialTransaction::whereDate('created_at', today())
                ->where('type', '!=', 'refund')->sum('amount_usd'),
            'checkout_revenue'=> FinancialTransaction::whereDate('created_at', today())
                ->where('type', 'checkout_payment')->sum('amount_usd'),
            'walkin_revenue'  => FinancialTransaction::whereDate('created_at', today())
                ->where('type', 'walkin_sale')->sum('amount_usd'),
            'cash_total'      => FinancialTransaction::whereDate('created_at', today())
                ->where('payment_method', 'cash')->sum('amount_usd'),
            'card_total'      => FinancialTransaction::whereDate('created_at', today())
                ->where('payment_method', 'card')->sum('amount_usd'),
        ];

        // Outstanding charges (unpaid booking charges)
        $outstandingTotal = BookingCharge::where('status', 'unpaid')->sum('amount');

        // Recent transactions
        $recentTransactions = FinancialTransaction::with(['payment', 'actor'])
            ->latest('created_at')
            ->take(15)
            ->get();

        return view('finance.dashboard.index', compact(
            'revenueByModule', 'revenueByMethod', 'dailyRevenue',
            'todaySummary', 'outstandingTotal', 'recentTransactions',
            'dateFrom', 'dateTo'
        ));
    }
}
```

---

## 7. Blade Views

---

**File:** `resources/views/finance/layout.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Finance') — Hotel Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
    <div class="font-bold text-lg text-gray-800">💳 Finance</div>
    <div class="flex gap-4 text-sm">
        <a href="{{ route('finance.dashboard') }}"        class="text-gray-600 hover:text-blue-600">Dashboard</a>
        <a href="{{ route('finance.payments.index') }}"   class="text-gray-600 hover:text-blue-600">Payments</a>
    </div>
    <div class="text-sm text-gray-500">{{ auth()->user()->name }} — {{ auth()->user()->role->name }}</div>
</nav>

<div class="max-w-7xl mx-auto px-6 mt-4">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif
</div>

<main class="max-w-7xl mx-auto px-6 py-6">
    @yield('content')
</main>

</body>
</html>
```

---

**File:** `resources/views/finance/checkout/show.blade.php`
Guest folio — all charges before payment.

```blade
@extends('finance.layout')
@section('title', 'Guest Folio — ' . $booking->guest_name ?? $booking->id)

@section('content')
<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Guest Folio</h1>
        <p class="text-sm text-gray-400 mt-1">
            {{ $booking->guest_name ?? 'Guest' }}
            · Room {{ $booking->room_number ?? '—' }}
            · Receipt: {{ $checkout->receipt_number }}
        </p>
    </div>
    <span class="px-3 py-1 rounded-full text-sm font-medium
        {{ $checkout->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
        {{ ucfirst($checkout->status) }}
    </span>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Charges by type --}}
    <div class="col-span-2 space-y-4">
        @foreach($chargesByType as $type => $items)
        <div class="bg-white rounded shadow overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b">
                <h2 class="font-semibold text-gray-700">
                    {{ $items->first()->charge_type_label }}
                    <span class="text-gray-400 font-normal text-sm ml-2">({{ $items->count() }} item(s))</span>
                </h2>
            </div>
            <table class="w-full text-sm">
                <thead><tr class="border-b">
                    <th class="px-4 py-2 text-left text-gray-500">Date</th>
                    <th class="px-4 py-2 text-left text-gray-500">Description</th>
                    <th class="px-4 py-2 text-right text-gray-500">USD</th>
                    <th class="px-4 py-2 text-right text-gray-500">TZS</th>
                    <th class="px-4 py-2 text-center text-gray-500">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($items as $charge)
                    <tr>
                        <td class="px-4 py-2 text-gray-400 text-xs">{{ $charge->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-2">{{ $charge->description }}</td>
                        <td class="px-4 py-2 text-right font-medium">{{ number_format($charge->amount, 2) }}</td>
                        <td class="px-4 py-2 text-right text-gray-500">
                            {{ number_format($charge->amount * $exchangeRate, 0) }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $charge->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ ucfirst($charge->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-50 font-medium">
                        <td colspan="2" class="px-4 py-2 text-right text-gray-600">Subtotal</td>
                        <td class="px-4 py-2 text-right">{{ number_format($items->sum('amount'), 2) }}</td>
                        <td class="px-4 py-2 text-right text-gray-500">
                            {{ number_format($items->sum('amount') * $exchangeRate, 0) }}
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endforeach

        {{-- Manually add charge --}}
        @if($checkout->status === 'pending')
        <div class="bg-white rounded shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Add Charge to Folio</h3>
            <form method="POST" action="{{ route('finance.checkout.add-charge', $checkout) }}">
                @csrf
                <div class="grid grid-cols-3 gap-3">
                    <select name="charge_type" required class="border rounded px-3 py-2 text-sm">
                        @foreach(['laundry','restaurant','room_service','damage','minibar','extra_bed','conference','store'] as $ct)
                        <option value="{{ $ct }}">{{ ucwords(str_replace('_', ' ', $ct)) }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="description" placeholder="Description" required
                           class="border rounded px-3 py-2 text-sm">
                    <div class="flex gap-2">
                        <input type="number" name="amount" placeholder="Amount (USD)" step="0.01" min="0.01" required
                               class="border rounded px-3 py-2 text-sm flex-1">
                        <button class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">Add</button>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>

    {{-- Totals + payment --}}
    <div class="space-y-4">
        <div class="bg-white rounded shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3">Bill Summary</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Total Charges</dt>
                    <dd>USD {{ number_format($checkout->total_charges_usd, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Discount</dt>
                    <dd class="text-red-500">- USD {{ number_format($checkout->discount_usd, 2) }}</dd>
                </div>
                <div class="flex justify-between font-bold text-gray-800 border-t pt-2">
                    <dt>Grand Total</dt>
                    <dd>USD {{ number_format($checkout->grand_total_usd, 2) }}</dd>
                </div>
                <div class="flex justify-between text-gray-500">
                    <dt>In TZS (rate: {{ number_format($exchangeRate, 0) }})</dt>
                    <dd>TZS {{ number_format($checkout->grand_total_tzs, 0) }}</dd>
                </div>
            </dl>
        </div>

        @if($checkout->status === 'pending')
        <div class="bg-white rounded shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3">Process Payment</h2>
            <form method="POST" action="{{ route('finance.checkout.process', $checkout) }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                        <select name="payment_method" id="pmMethod" required
                                class="w-full border rounded px-3 py-2 text-sm"
                                onchange="toggleSplitFields(this.value)">
                            <option value="cash_usd">Cash — USD</option>
                            <option value="cash_tzs">Cash — TZS</option>
                            <option value="card_usd">Card — USD</option>
                            <option value="card_tzs">Card — TZS</option>
                            <option value="split">Split Payment</option>
                        </select>
                    </div>

                    <div id="split-fields" class="hidden space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs text-gray-500">Cash USD</label>
                                <input type="number" name="cash_usd_amount" step="0.01" min="0"
                                       class="w-full border rounded px-2 py-1.5 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Card USD</label>
                                <input type="number" name="card_usd_amount" step="0.01" min="0"
                                       class="w-full border rounded px-2 py-1.5 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Cash TZS</label>
                                <input type="number" name="cash_tzs_amount" step="1" min="0"
                                       class="w-full border rounded px-2 py-1.5 text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Card TZS</label>
                                <input type="number" name="card_tzs_amount" step="1" min="0"
                                       class="w-full border rounded px-2 py-1.5 text-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount (USD)</label>
                        <input type="number" name="discount_usd" value="0" step="0.01" min="0"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full border rounded px-3 py-2 text-sm"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full bg-green-600 text-white py-2.5 rounded hover:bg-green-700 font-medium">
                        Complete Checkout
                    </button>
                </div>
            </form>
        </div>
        @endif

        @if($checkout->status === 'completed')
        <a href="{{ route('finance.receipt.guest', $checkout) }}"
           class="block text-center bg-blue-600 text-white py-2.5 rounded hover:bg-blue-700 font-medium">
            🖨️ Print Receipt
        </a>
        @endif
    </div>
</div>

<script>
function toggleSplitFields(val) {
    document.getElementById('split-fields').classList.toggle('hidden', val !== 'split');
}
</script>
@endsection
```

---

**File:** `resources/views/finance/receipts/guest.blade.php`
Printable guest receipt.

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $checkout->receipt_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; max-width: 700px; margin: auto; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        .hotel-name { font-size: 22px; font-weight: bold; text-align: center; margin-bottom: 4px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 12px; margin-bottom: 16px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 16px; }
        .meta-block p { margin-bottom: 3px; }
        .label { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f5f5f5; border-bottom: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        td { padding: 5px 8px; border-bottom: 1px solid #f0f0f0; }
        .text-right { text-align: right; }
        .section-header { background: #eee; font-weight: bold; padding: 6px 8px; }
        .totals { margin-left: auto; width: 280px; }
        .totals td { padding: 4px 8px; }
        .grand-total { font-weight: bold; font-size: 14px; border-top: 2px solid #333; }
        .footer { text-align: center; margin-top: 24px; border-top: 1px solid #ddd; padding-top: 12px; color: #666; }
        .payment-badge { background: #e6f4ea; color: #2e7d32; padding: 3px 8px; border-radius: 12px; font-size: 11px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="header">
    <div class="hotel-name">🏨 HOTEL NAME</div>
    <p>123 Hotel Street, Dar es Salaam, Tanzania</p>
    <p>Tel: +255 XXX XXX XXX | info@hotel.com</p>
    <h1 style="margin-top: 10px;">OFFICIAL RECEIPT</h1>
    <p style="font-size: 14px; font-weight: bold;">{{ $checkout->receipt_number }}</p>
</div>

<div class="meta">
    <div class="meta-block">
        <p><span class="label">Guest:</span> {{ $checkout->booking->guest_name ?? '—' }}</p>
        <p><span class="label">Room:</span> {{ $checkout->booking->room_number ?? '—' }}</p>
        <p><span class="label">Check-in:</span> {{ $checkout->booking->check_in?->format('d M Y') ?? '—' }}</p>
        <p><span class="label">Check-out:</span> {{ $checkout->completed_at?->format('d M Y') ?? now()->format('d M Y') }}</p>
    </div>
    <div class="meta-block" style="text-align: right;">
        <p><span class="label">Receipt No:</span> <strong>{{ $checkout->receipt_number }}</strong></p>
        <p><span class="label">Date:</span> {{ $checkout->completed_at?->format('d M Y H:i') }}</p>
        <p><span class="label">Cashier:</span> {{ $checkout->completer?->name ?? '—' }}</p>
        <p><span class="label">Rate:</span> 1 USD = {{ number_format($exchangeRate, 0) }} TZS</p>
    </div>
</div>

{{-- Charges grouped by type --}}
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th class="text-right">USD</th>
            <th class="text-right">TZS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($chargesByType as $type => $items)
        <tr>
            <td colspan="4" class="section-header">
                {{ $items->first()->charge_type_label }}
            </td>
        </tr>
        @foreach($items as $charge)
        <tr>
            <td>{{ $charge->created_at->format('d M') }}</td>
            <td>{{ $charge->description }}</td>
            <td class="text-right">{{ number_format($charge->amount, 2) }}</td>
            <td class="text-right">{{ number_format($charge->amount * $exchangeRate, 0) }}</td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>

{{-- Totals --}}
<table class="totals">
    <tr>
        <td class="label">Total Charges:</td>
        <td class="text-right">USD {{ number_format($checkout->total_charges_usd, 2) }}</td>
    </tr>
    @if($checkout->discount_usd > 0)
    <tr>
        <td class="label">Discount:</td>
        <td class="text-right" style="color: red;">- USD {{ number_format($checkout->discount_usd, 2) }}</td>
    </tr>
    @endif
    <tr class="grand-total">
        <td>GRAND TOTAL:</td>
        <td class="text-right">USD {{ number_format($checkout->grand_total_usd, 2) }}</td>
    </tr>
    <tr>
        <td class="label">In TZS:</td>
        <td class="text-right"><strong>TZS {{ number_format($checkout->grand_total_tzs, 0) }}</strong></td>
    </tr>
    <tr style="height: 8px;"></tr>
    <tr>
        <td class="label">Payment Method:</td>
        <td class="text-right">
            <span class="payment-badge">{{ ucwords(str_replace('_', ' ', $checkout->payment_method)) }}</span>
        </td>
    </tr>
    <tr>
        <td class="label">Amount Paid:</td>
        <td class="text-right">USD {{ number_format($checkout->total_paid_usd, 2) }}</td>
    </tr>
    @if($checkout->change_due_usd > 0)
    <tr>
        <td class="label">Change:</td>
        <td class="text-right">USD {{ number_format($checkout->change_due_usd, 2) }}</td>
    </tr>
    @endif
</table>

<div class="footer">
    <p><strong>Thank you for staying with us!</strong></p>
    <p style="margin-top: 4px;">We hope to see you again. Safe travels.</p>
    <p style="margin-top: 8px; font-size: 10px; color: #999;">
        This is an official receipt. For queries contact: info@hotel.com
    </p>
</div>

<div class="no-print" style="margin-top: 20px; text-align: center;">
    <button onclick="window.print()"
            style="background: #2563eb; color: white; padding: 10px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
        🖨️ Print Receipt
    </button>
</div>

</body>
</html>
```

---

**File:** `resources/views/finance/receipts/walkin.blade.php`
Walk-in customer receipt.

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt — {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; max-width: 400px; margin: auto; }
        .hotel-name { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 4px; }
        .header { text-align: center; border-bottom: 1px solid #333; padding-bottom: 10px; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        td { padding: 4px 0; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #ccc; margin: 8px 0; }
        .total-row td { font-weight: bold; font-size: 13px; border-top: 1px solid #333; padding-top: 6px; }
        .footer { text-align: center; margin-top: 16px; border-top: 1px dashed #ddd; padding-top: 10px; font-size: 11px; color: #666; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="header">
    <div class="hotel-name">🏨 HOTEL NAME</div>
    <p style="font-size: 11px;">Dar es Salaam, Tanzania</p>
    <p style="font-size: 13px; font-weight: bold; margin-top: 6px;">
        {{ strtoupper($order->location->name) }} RECEIPT
    </p>
    <p>Order: {{ $order->order_number }}</p>
    <p>{{ $order->created_at->format('d M Y H:i') }}</p>
    @if($order->table) <p>Table: {{ $order->table->table_number }}</p> @endif
</div>

<table>
    <thead>
        <tr>
            <td><strong>Item</strong></td>
            <td class="text-right"><strong>Qty</strong></td>
            <td class="text-right"><strong>Price</strong></td>
            <td class="text-right"><strong>Total</strong></td>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items->where('status', '!=', 'cancelled') as $item)
        <tr>
            <td>{{ $item->menuItem->name }}</td>
            <td class="text-right">{{ $item->quantity }}</td>
            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
            <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @if($item->notes)
        <tr><td colspan="4" style="font-size: 10px; color: #888; padding-left: 8px;">↳ {{ $item->notes }}</td></tr>
        @endif
        @endforeach
    </tbody>
</table>

<div class="divider"></div>

<table>
    <tr class="total-row">
        <td>TOTAL</td>
        <td class="text-right">USD {{ number_format($order->total, 2) }}</td>
    </tr>
    <tr>
        <td style="color: #666;">In TZS</td>
        <td class="text-right" style="color: #666;">TZS {{ number_format($order->total * $exchangeRate, 0) }}</td>
    </tr>
    @if($payment)
    <tr style="height:6px;"></tr>
    <tr>
        <td style="color: #666;">Paid ({{ ucfirst($payment->method) }})</td>
        <td class="text-right">
            {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
        </td>
    </tr>
    @endif
</table>

<div class="footer">
    <p>Thank you for your visit!</p>
    <p style="margin-top: 4px;">Rate: 1 USD = {{ number_format($exchangeRate, 0) }} TZS</p>
    <p style="margin-top: 4px; font-size: 10px;">Served by: {{ auth()->user()->name }}</p>
</div>

<div class="no-print" style="margin-top: 16px; text-align: center;">
    <button onclick="window.print()"
            style="background: #2563eb; color: white; padding: 8px 20px; border: none; border-radius: 5px; cursor: pointer;">
        🖨️ Print
    </button>
</div>

</body>
</html>
```

---

**File:** `resources/views/finance/dashboard/index.blade.php`

```blade
@extends('finance.layout')
@section('title', 'Financial Dashboard')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Financial Dashboard</h1>
    <form method="GET" class="flex gap-2">
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="border rounded px-3 py-1.5 text-sm">
        <input type="date" name="date_to"   value="{{ $dateTo }}"   class="border rounded px-3 py-1.5 text-sm">
        <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Apply</button>
    </form>
</div>

{{-- Today's summary cards --}}
<div class="grid grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Today Revenue</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">
            ${{ number_format($todaySummary['total_revenue'], 2) }}
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Checkout</p>
        <p class="text-2xl font-bold text-green-600 mt-1">
            ${{ number_format($todaySummary['checkout_revenue'], 2) }}
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Walk-in Sales</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">
            ${{ number_format($todaySummary['walkin_revenue'], 2) }}
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Cash</p>
        <p class="text-2xl font-bold text-gray-700 mt-1">
            ${{ number_format($todaySummary['cash_total'], 2) }}
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Card</p>
        <p class="text-2xl font-bold text-gray-700 mt-1">
            ${{ number_format($todaySummary['card_total'], 2) }}
        </p>
    </div>
</div>

<div class="grid grid-cols-3 gap-6 mb-6">
    {{-- Revenue by module --}}
    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">Revenue by Module</h2>
        @foreach($revenueByModule as $row)
        <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
            <span class="text-gray-600 capitalize">{{ str_replace('_', ' ', $row->source_module) }}</span>
            <span class="font-medium">${{ number_format($row->total_usd, 2) }}</span>
        </div>
        @endforeach
    </div>

    {{-- Revenue by payment method --}}
    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">By Payment Method</h2>
        @foreach($revenueByMethod as $row)
        <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
            <span class="text-gray-600 capitalize">{{ ucfirst($row->payment_method) }}</span>
            <span class="font-medium">${{ number_format($row->total_usd, 2) }}</span>
        </div>
        @endforeach
    </div>

    {{-- Outstanding --}}
    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">Outstanding Balance</h2>
        <p class="text-3xl font-bold text-red-500">${{ number_format($outstandingTotal, 2) }}</p>
        <p class="text-sm text-gray-400 mt-2">Unpaid guest charges pending checkout</p>
    </div>
</div>

{{-- Recent transactions --}}
<div class="bg-white rounded shadow overflow-hidden">
    <div class="px-5 py-4 border-b">
        <h2 class="font-semibold text-gray-700">Recent Transactions</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-500">TXN #</th>
                <th class="px-4 py-3 text-left text-gray-500">Type</th>
                <th class="px-4 py-3 text-left text-gray-500">Module</th>
                <th class="px-4 py-3 text-left text-gray-500">Method</th>
                <th class="px-4 py-3 text-right text-gray-500">USD</th>
                <th class="px-4 py-3 text-left text-gray-500">By</th>
                <th class="px-4 py-3 text-left text-gray-500">Time</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($recentTransactions as $txn)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs">{{ $txn->transaction_number }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs
                        {{ $txn->type === 'checkout_payment' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucwords(str_replace('_', ' ', $txn->type)) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 capitalize">{{ $txn->source_module }}</td>
                <td class="px-4 py-3 text-gray-500 capitalize">{{ $txn->payment_method }}</td>
                <td class="px-4 py-3 text-right font-medium">${{ number_format($txn->amount_usd, 2) }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $txn->actor?->name }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $txn->created_at->format('H:i d M') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No transactions yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
```

---

## 8. Routes

**File:** `routes/web.php` — add inside your existing `auth` middleware group.

```php
use App\Http\Controllers\Finance\CheckoutController;
use App\Http\Controllers\Finance\PaymentController;
use App\Http\Controllers\Finance\ReceiptController;
use App\Http\Controllers\Finance\FinancialDashboardController;

Route::middleware(['auth'])->prefix('finance')->name('finance.')->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────────────────
    Route::get('dashboard', [FinancialDashboardController::class, 'index'])->name('dashboard')
         ->middleware('role:STORE_MANAGER,CASHIER,FRONT_DESK');

    // ── Checkout ──────────────────────────────────────────────────────────────
    Route::get('checkout/{booking}',                  [CheckoutController::class, 'show'])->name('checkout.show')
         ->middleware('role:FRONT_DESK,CASHIER');
    Route::post('checkout/{checkout}/process',        [CheckoutController::class, 'process'])->name('checkout.process')
         ->middleware('role:CASHIER,FRONT_DESK');
    Route::post('checkout/{checkout}/add-charge',     [CheckoutController::class, 'addCharge'])->name('checkout.add-charge')
         ->middleware('role:FRONT_DESK,CASHIER');

    // ── Walk-in Payments ──────────────────────────────────────────────────────
    Route::get('payments',                [PaymentController::class, 'index'])->name('payments.index')
         ->middleware('role:CASHIER,STORE_MANAGER');
    Route::post('payments/walkin',        [PaymentController::class, 'storeWalkin'])->name('payments.walkin')
         ->middleware('role:CASHIER,BAR_TENDER,RESTAURANT_MANAGER');

    // ── Receipts ──────────────────────────────────────────────────────────────
    Route::get('receipts/guest/{checkout}', [ReceiptController::class, 'guest'])->name('receipt.guest');
    Route::get('receipts/walkin',           [ReceiptController::class, 'walkin'])->name('receipt.walkin');

});
```

---

## 9. Business Rules

| # | Rule | Where Enforced |
|---|---|---|
| 001 | Every money movement writes to `financial_transactions` — immutable | `FinancialTransaction::record()` called in all payment flows |
| 002 | All amounts stored in USD internally | `amount_usd` on every money table |
| 003 | TZS conversion uses rate from `system_settings.tzs_exchange_rate` | `calculateTotals()`, `PaymentController`, `ReceiptController` |
| 004 | Walk-in sales (cash/digital) go to `payments` table — not `booking_charges` | `PaymentController::storeWalkin()` |
| 005 | Guest charges go to `booking_charges` — settled at checkout | All modules create `BookingCharge` with `status = unpaid` |
| 006 | Checkout marks all `booking_charges` as paid atomically | `CheckoutController::process()` inside `DB::transaction()` |
| 007 | Receipt is generated only after checkout is `completed` | Route to receipt only from completed checkout |
| 008 | Split payments supported — USD + TZS, cash + card | `payment_method = split` with 4 optional amount fields |
| 009 | Change due calculated and shown on receipt | `change_due_usd` on checkout record |
| 010 | Financial dashboard reads only from `financial_transactions` — no raw order/charge queries | `FinancialDashboardController` |

---

## 10. Add Exchange Rate to System Settings

Add this to `SystemSettingsSeeder.php`:

```php
[
    'key'         => 'tzs_exchange_rate',
    'value'       => '2500',
    'description' => 'Current TZS per 1 USD — update daily',
],
```

Then seed:
```bash
php artisan db:seed --class=SystemSettingsSeeder
```

## 11. Build Order

```bash
# Migrations
php artisan make:migration update_booking_charges_table
php artisan make:migration create_checkouts_table
php artisan make:migration create_payments_table
php artisan make:migration create_payment_items_table
php artisan make:migration create_financial_transactions_table
php artisan migrate

# Seed exchange rate
php artisan db:seed --class=SystemSettingsSeeder

# Build order
# 1. FinancialTransaction model + FinancialTransaction::record() — test in isolation first
# 2. Checkout flow — CheckoutController + views/finance/checkout/
# 3. Receipt views — no controller logic, pure display
# 4. Walk-in payment — PaymentController::storeWalkin()
# 5. Financial Dashboard — last, reads from everything above

# Critical test cases
# ✓ Guest with laundry + restaurant + store charges → checkout → all marked paid in one transaction
# ✓ Walk-in cash USD order → payment recorded → TXN written → receipt generated
# ✓ Walk-in card TZS order → amount_usd correctly calculated using exchange rate
# ✓ Split payment (cash TZS + card USD) → both halves recorded → correct totals
# ✓ Receipt shows correct TZS conversion
# ✓ Financial dashboard totals match sum of financial_transactions
# ✓ Cannot checkout an already completed checkout
```
