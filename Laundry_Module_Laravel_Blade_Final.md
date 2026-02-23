# 🧺 Laundry Module — Laravel + Blade Implementation
### Starting Fresh · House Help Handles Operations · Cash & Card for Walk-ins

> Built on the existing hotel system.
> No separate laundry staff — **HOUSE_HELP** handles everything on the ground.
> **SUPERVISOR** approves and oversees.
> **LAUNDRY_MANAGER** (new role) manages pricing and reports.
> **CASHIER** settles walk-in cash and card payments.
> **FRONT_DESK** creates guest orders and charges to booking.

---

## 📋 Table of Contents

1. [How This Module Fits](#1-how-this-module-fits)
2. [Role Responsibilities](#2-role-responsibilities)
3. [File Map](#3-file-map)
4. [Migrations](#4-migrations)
5. [Seeders](#5-seeders)
6. [Models](#6-models)
7. [Controllers](#7-controllers)
8. [Blade Views](#8-blade-views)
9. [Routes](#9-routes)
10. [Business Rules](#10-business-rules)
11. [Build Order](#11-build-order)

---

## 1. How This Module Fits

```
EXISTING ROLES USED
    └── HOUSE_HELP     ← picks up, processes, delivers laundry
    └── SUPERVISOR     ← oversees and can approve/manage
    └── FRONT_DESK     ← creates guest laundry orders, charges to booking
    └── CASHIER        ← settles walk-in cash and card payments

NEW ROLE ADDED
    └── LAUNDRY_MANAGER ← manages pricing list, views reports, full oversight

NEW TABLES
    └── laundry_services      ← service types (Wash Only, Wash & Iron, etc.)
    └── laundry_service_items ← items + prices per service (Shirt = 3500, etc.)
    └── laundry_orders        ← the laundry job
    └── laundry_order_items   ← line items on the job
```

### Order Lifecycle

```
HOTEL GUEST:
received → processing → ready → delivered to room → charged to booking

WALK-IN CUSTOMER:
received → processing → ready → collected by customer → cash or card payment
```

---

## 2. Role Responsibilities

| Who | What They Do in Laundry |
|---|---|
| `HOUSE_HELP` | Receive items, process laundry, mark ready, deliver to rooms, mark collected |
| `SUPERVISOR` | Full visibility, can update any order status, cancel orders |
| `LAUNDRY_MANAGER` | Manage price list, view all reports, full order management |
| `FRONT_DESK` | Create guest laundry orders, charge to booking at checkout |
| `CASHIER` | Settle walk-in cash and card payments |

> No separate laundry staff role needed. HOUSE_HELP does the physical work.
> SUPERVISOR and LAUNDRY_MANAGER handle oversight.

---

## 3. File Map

```
app/
├── Http/
│   └── Controllers/
│       └── Laundry/                              ← inside existing Controllers/
│           ├── LaundryServiceController.php
│           ├── LaundryOrderController.php
│           └── LaundryReportController.php
│
└── Models/
    ├── LaundryService.php
    ├── LaundryServiceItem.php
    ├── LaundryOrder.php
    └── LaundryOrderItem.php

database/
├── migrations/
│   ├── xxxx_add_laundry_manager_role.php
│   ├── xxxx_create_laundry_services_table.php
│   ├── xxxx_create_laundry_service_items_table.php
│   ├── xxxx_create_laundry_orders_table.php
│   └── xxxx_create_laundry_order_items_table.php
│
└── seeders/
    └── LaundryServiceSeeder.php

resources/
└── views/
    └── laundry/                                  ← inside existing views/
        ├── layout.blade.php
        ├── services/
        │   └── index.blade.php
        ├── orders/
        │   ├── index.blade.php
        │   ├── create.blade.php
        │   └── show.blade.php
        └── reports/
            └── daily.blade.php

routes/
└── web.php
```

---

## 4. Migrations

---

**File:** `database/migrations/xxxx_add_laundry_manager_role.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Role;

return new class extends Migration {
    public function up(): void
    {
        Role::updateOrCreate(
            ['name' => 'LAUNDRY_MANAGER'],
            ['description' => 'Manages laundry pricing, reports, and full order oversight']
        );
    }

    public function down(): void
    {
        Role::where('name', 'LAUNDRY_MANAGER')->delete();
    }
};
```

---

**File:** `database/migrations/xxxx_create_laundry_services_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laundry_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);                      // Wash Only, Wash & Iron, etc.
            $table->text('description')->nullable();
            $table->integer('turnaround_hours')->default(24); // expected completion time
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_services');
    }
};
```

---

**File:** `database/migrations/xxxx_create_laundry_service_items_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Price per clothing item per service type
        // e.g. Shirt + Wash & Iron = 3,500
        Schema::create('laundry_service_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('laundry_service_id');
            $table->string('item_name', 100);                 // Shirt, Trouser, Suit, etc.
            $table->decimal('price', 10, 2);                  // price per piece
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('laundry_service_id')
                  ->references('id')
                  ->on('laundry_services')
                  ->cascadeOnDelete();

            $table->unique(['laundry_service_id', 'item_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_service_items');
    }
};
```

---

**File:** `database/migrations/xxxx_create_laundry_orders_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number', 30)->unique();     // LND-20240222-0012

            $table->enum('customer_type', [
                'guest',   // hotel guest — charge to booking at checkout
                'walkin',  // outside customer — pay cash or card now
            ]);

            // Guest fields
            $table->uuid('booking_id')->nullable();
            $table->string('room_number', 20)->nullable();

            // Walk-in fields
            $table->string('customer_name', 150)->nullable();
            $table->string('customer_phone', 30)->nullable();

            $table->enum('status', [
                'received',    // items dropped off or collected from room by HOUSE_HELP
                'processing',  // HOUSE_HELP is working on it
                'ready',       // done — waiting for delivery or collection
                'delivered',   // returned to guest room
                'collected',   // walk-in customer has collected
                'settled',     // payment complete
                'cancelled',
            ])->default('received');

            $table->text('special_instructions')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->enum('payment_method', [
                'cash',
                'card',
                'charge_to_booking',
            ])->nullable();

            // Timestamps for each step
            $table->timestamp('expected_ready_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('settled_at')->nullable();

            // Who did what
            $table->uuid('received_by');      // HOUSE_HELP or FRONT_DESK
            $table->uuid('processed_by')->nullable();  // HOUSE_HELP
            $table->uuid('delivered_by')->nullable();  // HOUSE_HELP
            $table->uuid('settled_by')->nullable();    // CASHIER or FRONT_DESK

            $table->timestamps();

            $table->foreign('received_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_orders');
    }
};
```

---

**File:** `database/migrations/xxxx_create_laundry_order_items_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laundry_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('laundry_order_id');
            $table->uuid('laundry_service_item_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);    // price snapshot at time of order
            $table->decimal('subtotal', 10, 2);
            $table->text('notes')->nullable();        // stain on collar, handle with care, etc.
            $table->timestamps();

            $table->foreign('laundry_order_id')
                  ->references('id')
                  ->on('laundry_orders')
                  ->cascadeOnDelete();

            $table->foreign('laundry_service_item_id')
                  ->references('id')
                  ->on('laundry_service_items');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_order_items');
    }
};
```

---

## 5. Seeders

**File:** `database/seeders/LaundryServiceSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\LaundryService;
use App\Models\LaundryServiceItem;
use Illuminate\Database\Seeder;

class LaundryServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name'             => 'Wash Only',
                'description'      => 'Machine or hand wash, no ironing',
                'turnaround_hours' => 12,
                'items' => [
                    ['item_name' => 'Shirt',          'price' => 2000],
                    ['item_name' => 'T-Shirt',        'price' => 1500],
                    ['item_name' => 'Trouser',        'price' => 2500],
                    ['item_name' => 'Shorts',         'price' => 1500],
                    ['item_name' => 'Underwear',      'price' => 1000],
                    ['item_name' => 'Socks (pair)',   'price' => 1000],
                    ['item_name' => 'Dress',          'price' => 3000],
                    ['item_name' => 'Skirt',          'price' => 2000],
                    ['item_name' => 'Bedsheet',       'price' => 4000],
                    ['item_name' => 'Towel',          'price' => 2000],
                    ['item_name' => 'Pillowcase',     'price' => 1500],
                ],
            ],
            [
                'name'             => 'Wash & Iron',
                'description'      => 'Full wash and professional ironing',
                'turnaround_hours' => 24,
                'items' => [
                    ['item_name' => 'Shirt',          'price' => 3500],
                    ['item_name' => 'T-Shirt',        'price' => 2500],
                    ['item_name' => 'Trouser',        'price' => 4000],
                    ['item_name' => 'Shorts',         'price' => 2500],
                    ['item_name' => 'Dress',          'price' => 5000],
                    ['item_name' => 'Skirt',          'price' => 3500],
                    ['item_name' => 'Suit (full)',    'price' => 10000],
                    ['item_name' => 'Jacket',         'price' => 6000],
                    ['item_name' => 'Bedsheet',       'price' => 6000],
                    ['item_name' => 'Towel',          'price' => 3000],
                ],
            ],
            [
                'name'             => 'Iron Only',
                'description'      => 'Ironing of clean items only',
                'turnaround_hours' => 6,
                'items' => [
                    ['item_name' => 'Shirt',          'price' => 1500],
                    ['item_name' => 'T-Shirt',        'price' => 1000],
                    ['item_name' => 'Trouser',        'price' => 2000],
                    ['item_name' => 'Dress',          'price' => 2500],
                    ['item_name' => 'Suit (full)',    'price' => 5000],
                    ['item_name' => 'Jacket',         'price' => 3000],
                ],
            ],
            [
                'name'             => 'Dry Cleaning',
                'description'      => 'Professional dry cleaning for delicate items',
                'turnaround_hours' => 48,
                'items' => [
                    ['item_name' => 'Suit (full)',    'price' => 20000],
                    ['item_name' => 'Jacket',         'price' => 12000],
                    ['item_name' => 'Dress',          'price' => 15000],
                    ['item_name' => 'Coat',           'price' => 18000],
                    ['item_name' => 'Trouser',        'price' => 8000],
                    ['item_name' => 'Shirt',          'price' => 7000],
                    ['item_name' => 'Tie',            'price' => 5000],
                    ['item_name' => 'Skirt',          'price' => 10000],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            $items = $serviceData['items'];
            unset($serviceData['items']);

            $service = LaundryService::updateOrCreate(
                ['name' => $serviceData['name']],
                $serviceData
            );

            foreach ($items as $item) {
                LaundryServiceItem::updateOrCreate(
                    [
                        'laundry_service_id' => $service->id,
                        'item_name'          => $item['item_name'],
                    ],
                    ['price' => $item['price']]
                );
            }
        }
    }
}
```

**Update `database/seeders/DatabaseSeeder.php`:**

```php
$this->call([
    RoleSeeder::class,
    StockLocationSeeder::class,
    SystemSettingsSeeder::class,
    MenuCategorySeeder::class,
    LaundryServiceSeeder::class,  // ← add
]);
```

**Also update `RoleSeeder.php` to include LAUNDRY_MANAGER:**

```php
['name' => 'LAUNDRY_MANAGER', 'description' => 'Manages laundry pricing, reports, full order oversight'],
```

---

## 6. Models

**File:** `app/Models/LaundryService.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LaundryService extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'description', 'turnaround_hours', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function serviceItems()
    {
        return $this->hasMany(LaundryServiceItem::class);
    }
}
```

---

**File:** `app/Models/LaundryServiceItem.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LaundryServiceItem extends Model
{
    use HasUuids;

    protected $fillable = ['laundry_service_id', 'item_name', 'price', 'is_active'];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(LaundryService::class, 'laundry_service_id');
    }
}
```

---

**File:** `app/Models/LaundryOrder.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LaundryOrder extends Model
{
    use HasUuids;

    protected $fillable = [
        'order_number', 'customer_type',
        'booking_id', 'room_number',
        'customer_name', 'customer_phone',
        'status', 'special_instructions',
        'subtotal', 'discount', 'total', 'payment_method',
        'expected_ready_at', 'ready_at',
        'delivered_at', 'collected_at', 'settled_at',
        'received_by', 'processed_by', 'delivered_by', 'settled_by',
    ];

    protected $casts = [
        'subtotal'          => 'decimal:2',
        'discount'          => 'decimal:2',
        'total'             => 'decimal:2',
        'expected_ready_at' => 'datetime',
        'ready_at'          => 'datetime',
        'delivered_at'      => 'datetime',
        'collected_at'      => 'datetime',
        'settled_at'        => 'datetime',
    ];

    // Auto-generate order number
    protected static function booted(): void
    {
        static::creating(function (LaundryOrder $order) {
            $count = self::whereDate('created_at', today())->count() + 1;
            $order->order_number = 'LND-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    // Recalculate totals from items
    public function recalculate(): void
    {
        $subtotal = $this->items->sum('subtotal');
        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal - $this->discount,
        ]);
    }

    // Is this order past its expected ready time and not yet done?
    public function isOverdue(): bool
    {
        return $this->expected_ready_at
            && now()->isAfter($this->expected_ready_at)
            && !in_array($this->status, ['ready', 'delivered', 'collected', 'settled', 'cancelled']);
    }

    public function items()     { return $this->hasMany(LaundryOrderItem::class); }
    public function receiver()  { return $this->belongsTo(User::class, 'received_by'); }
    public function processor() { return $this->belongsTo(User::class, 'processed_by'); }
    public function deliverer() { return $this->belongsTo(User::class, 'delivered_by'); }
    public function settler()   { return $this->belongsTo(User::class, 'settled_by'); }
}
```

---

**File:** `app/Models/LaundryOrderItem.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LaundryOrderItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'laundry_order_id', 'laundry_service_item_id',
        'quantity', 'unit_price', 'subtotal', 'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'quantity'   => 'integer',
    ];

    public function order()       { return $this->belongsTo(LaundryOrder::class, 'laundry_order_id'); }
    public function serviceItem() { return $this->belongsTo(LaundryServiceItem::class, 'laundry_service_item_id'); }
}
```

---

## 7. Controllers

---

**File:** `app/Http/Controllers/Laundry/LaundryServiceController.php`

```php
<?php

namespace App\Http\Controllers\Laundry;

use App\Http\Controllers\Controller;
use App\Models\LaundryService;
use App\Models\LaundryServiceItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaundryServiceController extends Controller
{
    // GET /laundry/services
    public function index(): View
    {
        $services = LaundryService::with(['serviceItems' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->get();

        return view('laundry.services.index', compact('services'));
    }

    // POST /laundry/services/{service}/items
    public function addItem(Request $request, LaundryService $service): RedirectResponse
    {
        $request->validate([
            'item_name' => 'required|string|max:100',
            'price'     => 'required|numeric|min:1',
        ]);

        LaundryServiceItem::updateOrCreate(
            ['laundry_service_id' => $service->id, 'item_name' => $request->item_name],
            ['price' => $request->price, 'is_active' => true]
        );

        return redirect()
            ->route('laundry.services.index')
            ->with('success', "{$request->item_name} added to {$service->name}.");
    }

    // PUT /laundry/services/{service}/items/{item}
    public function updateItem(Request $request, LaundryService $service, LaundryServiceItem $item): RedirectResponse
    {
        $request->validate(['price' => 'required|numeric|min:1']);

        $item->update(['price' => $request->price]);

        return redirect()
            ->route('laundry.services.index')
            ->with('success', "Price updated for {$item->item_name}.");
    }

    // DELETE /laundry/services/{service}/items/{item}
    public function removeItem(LaundryService $service, LaundryServiceItem $item): RedirectResponse
    {
        $item->update(['is_active' => false]);

        return redirect()
            ->route('laundry.services.index')
            ->with('success', "{$item->item_name} removed from {$service->name}.");
    }
}
```

---

**File:** `app/Http/Controllers/Laundry/LaundryOrderController.php`

```php
<?php

namespace App\Http\Controllers\Laundry;

use App\Http\Controllers\Controller;
use App\Models\BookingCharge;
use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use App\Models\LaundryService;
use App\Models\LaundryServiceItem;
use App\Models\StoreNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaundryOrderController extends Controller
{
    // GET /laundry/orders
    public function index(Request $request): View
    {
        $orders = LaundryOrder::with(['items.serviceItem.service', 'receiver'])
            ->when($request->status,        fn($q) => $q->where('status', $request->status))
            ->when($request->customer_type, fn($q) => $q->where('customer_type', $request->customer_type))
            ->when($request->date,          fn($q) => $q->whereDate('created_at', $request->date))
            ->when($request->search, fn($q) => $q->where(function ($q2) use ($request) {
                $q2->where('order_number',    'like', '%' . $request->search . '%')
                   ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                   ->orWhere('room_number',   'like', '%' . $request->search . '%')
                   ->orWhere('customer_phone','like', '%' . $request->search . '%');
            }))
            ->latest()
            ->paginate(25);

        $statusCounts = LaundryOrder::selectRaw('status, count(*) as count')
            ->whereNotIn('status', ['settled', 'cancelled'])
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('laundry.orders.index', compact('orders', 'statusCounts'));
    }

    // GET /laundry/orders/create
    public function create(): View
    {
        $services = LaundryService::with(['serviceItems' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->get();

        return view('laundry.orders.create', compact('services'));
    }

    // POST /laundry/orders
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_type'           => 'required|in:guest,walkin',
            'booking_id'              => 'required_if:customer_type,guest|nullable|uuid',
            'room_number'             => 'nullable|string|max:20',
            'customer_name'           => 'required_if:customer_type,walkin|nullable|string|max:150',
            'customer_phone'          => 'nullable|string|max:30',
            'special_instructions'    => 'nullable|string|max:500',
            'items'                   => 'required|array|min:1',
            'items.*.service_item_id' => 'required|uuid|exists:laundry_service_items,id',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.notes'           => 'nullable|string|max:255',
        ]);

        $order = DB::transaction(function () use ($data) {

            // Find the longest turnaround among selected services
            $maxTurnaround = 0;
            foreach ($data['items'] as $item) {
                $serviceItem = LaundryServiceItem::with('service')->findOrFail($item['service_item_id']);
                if ($serviceItem->service->turnaround_hours > $maxTurnaround) {
                    $maxTurnaround = $serviceItem->service->turnaround_hours;
                }
            }

            $order = LaundryOrder::create([
                'customer_type'       => $data['customer_type'],
                'booking_id'          => $data['booking_id'] ?? null,
                'room_number'         => $data['room_number'] ?? null,
                'customer_name'       => $data['customer_name'] ?? null,
                'customer_phone'      => $data['customer_phone'] ?? null,
                'special_instructions'=> $data['special_instructions'] ?? null,
                'status'              => 'received',
                'expected_ready_at'   => now()->addHours($maxTurnaround),
                'received_by'         => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $serviceItem = LaundryServiceItem::findOrFail($item['service_item_id']);

                LaundryOrderItem::create([
                    'laundry_order_id'        => $order->id,
                    'laundry_service_item_id' => $serviceItem->id,
                    'quantity'                => $item['quantity'],
                    'unit_price'              => $serviceItem->price,
                    'subtotal'                => $serviceItem->price * $item['quantity'],
                    'notes'                   => $item['notes'] ?? null,
                ]);
            }

            $order->load('items');
            $order->recalculate();

            return $order;
        });

        // Notify SUPERVISOR and LAUNDRY_MANAGER
        User::whereHas('role', fn($q) => $q->whereIn('name', ['SUPERVISOR', 'LAUNDRY_MANAGER']))
            ->get()
            ->each(fn($u) => StoreNotification::create([
                'user_id'        => $u->id,
                'type'           => 'new_laundry_order',
                'title'          => 'New Laundry Order Received',
                'body'           => "Order {$order->order_number} — " .
                                    ($order->customer_type === 'guest'
                                        ? "Room {$order->room_number}"
                                        : $order->customer_name) .
                                    " — {$order->items->count()} item(s). Ready by: " .
                                    $order->expected_ready_at->format('d M H:i'),
                'reference_type' => 'laundry_order',
                'reference_id'   => $order->id,
                'action_url'     => route('laundry.orders.show', $order->id),
                'created_at'     => now(),
            ]));

        return redirect()
            ->route('laundry.orders.show', $order)
            ->with('success', "Order {$order->order_number} created. Ready by: " .
                              $order->expected_ready_at->format('d M Y H:i'));
    }

    // GET /laundry/orders/{laundryOrder}
    public function show(LaundryOrder $laundryOrder): View
    {
        $laundryOrder->load([
            'items.serviceItem.service',
            'receiver', 'processor', 'deliverer', 'settler',
        ]);

        return view('laundry.orders.show', compact('laundryOrder'));
    }

    // POST /laundry/orders/{laundryOrder}/process
    // HOUSE_HELP starts processing
    public function process(LaundryOrder $laundryOrder): RedirectResponse
    {
        abort_if($laundryOrder->status !== 'received', 422, 'Only received orders can be started.');

        $laundryOrder->update([
            'status'       => 'processing',
            'processed_by' => auth()->id(),
        ]);

        return redirect()
            ->route('laundry.orders.show', $laundryOrder)
            ->with('success', "Order {$laundryOrder->order_number} is now processing.");
    }

    // POST /laundry/orders/{laundryOrder}/ready
    // HOUSE_HELP marks as done
    public function markReady(LaundryOrder $laundryOrder): RedirectResponse
    {
        abort_if($laundryOrder->status !== 'processing', 422, 'Order must be processing before marking ready.');

        $laundryOrder->update([
            'status'   => 'ready',
            'ready_at' => now(),
        ]);

        // Notify FRONT_DESK for guest orders so they can arrange delivery
        if ($laundryOrder->customer_type === 'guest') {
            User::whereHas('role', fn($q) => $q->where('name', 'FRONT_DESK'))
                ->get()
                ->each(fn($u) => StoreNotification::create([
                    'user_id'        => $u->id,
                    'type'           => 'laundry_ready',
                    'title'          => 'Laundry Ready for Delivery',
                    'body'           => "Order {$laundryOrder->order_number} — Room {$laundryOrder->room_number} is ready.",
                    'reference_type' => 'laundry_order',
                    'reference_id'   => $laundryOrder->id,
                    'action_url'     => route('laundry.orders.show', $laundryOrder->id),
                    'created_at'     => now(),
                ]));
        }

        return redirect()
            ->route('laundry.orders.show', $laundryOrder)
            ->with('success', "Order {$laundryOrder->order_number} is ready.");
    }

    // POST /laundry/orders/{laundryOrder}/deliver
    // HOUSE_HELP delivers to guest room
    public function deliver(LaundryOrder $laundryOrder): RedirectResponse
    {
        abort_if($laundryOrder->status !== 'ready', 422, 'Order must be ready before delivery.');
        abort_if($laundryOrder->customer_type !== 'guest', 422, 'Delivery is only for hotel guest orders.');

        $laundryOrder->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
            'delivered_by' => auth()->id(),
        ]);

        return redirect()
            ->route('laundry.orders.show', $laundryOrder)
            ->with('success', "Order {$laundryOrder->order_number} delivered to Room {$laundryOrder->room_number}.");
    }

    // POST /laundry/orders/{laundryOrder}/collected
    // Walk-in customer collected their items
    public function collected(LaundryOrder $laundryOrder): RedirectResponse
    {
        abort_if($laundryOrder->status !== 'ready', 422, 'Order must be ready before collection.');
        abort_if($laundryOrder->customer_type !== 'walkin', 422, 'Collected is only for walk-in orders.');

        $laundryOrder->update([
            'status'       => 'collected',
            'collected_at' => now(),
        ]);

        return redirect()
            ->route('laundry.orders.show', $laundryOrder)
            ->with('success', "Order {$laundryOrder->order_number} collected by {$laundryOrder->customer_name}.");
    }

    // POST /laundry/orders/{laundryOrder}/settle
    public function settle(Request $request, LaundryOrder $laundryOrder): RedirectResponse
    {
        abort_if($laundryOrder->status === 'settled',   422, 'Order already settled.');
        abort_if($laundryOrder->status === 'cancelled', 422, 'Cannot settle a cancelled order.');

        $request->validate([
            'payment_method' => 'required|in:cash,card,charge_to_booking',
            'discount'       => 'nullable|numeric|min:0',
            'booking_id'     => 'required_if:payment_method,charge_to_booking|nullable|uuid',
        ]);

        DB::transaction(function () use ($request, $laundryOrder) {

            // Apply discount if provided
            if ($request->filled('discount') && $request->discount > 0) {
                $laundryOrder->update(['discount' => $request->discount]);
                $laundryOrder->load('items');
                $laundryOrder->recalculate();
                $laundryOrder->refresh();
            }

            $laundryOrder->update([
                'status'         => 'settled',
                'payment_method' => $request->payment_method,
                'settled_by'     => auth()->id(),
                'settled_at'     => now(),
                'booking_id'     => $request->booking_id ?? $laundryOrder->booking_id,
            ]);

            // Guest charge to booking
            if ($request->payment_method === 'charge_to_booking') {
                $bookingId = $request->booking_id ?? $laundryOrder->booking_id;
                abort_if(!$bookingId, 422, 'Booking ID is required to charge to booking.');

                BookingCharge::create([
                    'booking_id'  => $bookingId,
                    'order_id'    => null,
                    'source'      => 'store',
                    'description' => "Laundry Order {$laundryOrder->order_number} — " .
                                     $laundryOrder->items->count() . " item(s)",
                    'amount'      => $laundryOrder->total,
                    'is_settled'  => false,
                    'created_by'  => auth()->id(),
                ]);
            }
        });

        return redirect()
            ->route('laundry.orders.show', $laundryOrder)
            ->with('success', "Order {$laundryOrder->order_number} settled. Total: " .
                              number_format($laundryOrder->fresh()->total, 2));
    }

    // POST /laundry/orders/{laundryOrder}/cancel
    public function cancel(LaundryOrder $laundryOrder): RedirectResponse
    {
        abort_if($laundryOrder->status === 'settled', 422, 'Cannot cancel a settled order.');

        $laundryOrder->update(['status' => 'cancelled']);

        return redirect()
            ->route('laundry.orders.index')
            ->with('success', "Order {$laundryOrder->order_number} cancelled.");
    }
}
```

---

**File:** `app/Http/Controllers/Laundry/LaundryReportController.php`

```php
<?php

namespace App\Http\Controllers\Laundry;

use App\Http\Controllers\Controller;
use App\Models\LaundryOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaundryReportController extends Controller
{
    // GET /laundry/reports/daily
    public function daily(Request $request): View
    {
        $date = $request->date ?? today()->toDateString();

        $orders = LaundryOrder::with(['items.serviceItem.service', 'receiver', 'settler'])
            ->where('status', 'settled')
            ->whereDate('settled_at', $date)
            ->get();

        $summary = [
            'total_orders'   => $orders->count(),
            'total_revenue'  => $orders->sum('total'),
            'guest_revenue'  => $orders->where('customer_type', 'guest')->sum('total'),
            'walkin_revenue' => $orders->where('customer_type', 'walkin')->sum('total'),
            'cash'           => $orders->where('payment_method', 'cash')->sum('total'),
            'card'           => $orders->where('payment_method', 'card')->sum('total'),
            'charged'        => $orders->where('payment_method', 'charge_to_booking')->sum('total'),
        ];

        $overdueOrders = LaundryOrder::whereNotIn('status', ['settled', 'cancelled', 'ready', 'delivered', 'collected'])
            ->where('expected_ready_at', '<', now())
            ->with('items')
            ->get();

        return view('laundry.reports.daily', compact('orders', 'summary', 'date', 'overdueOrders'));
    }
}
```

---

## 8. Blade Views

---

**File:** `resources/views/laundry/layout.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laundry') — Hotel Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
    <div class="font-bold text-lg text-gray-800">🧺 Laundry</div>
    <div class="flex gap-4 text-sm">
        <a href="{{ route('laundry.orders.index') }}"   class="text-gray-600 hover:text-blue-600">Orders</a>
        <a href="{{ route('laundry.orders.create') }}"  class="text-gray-600 hover:text-blue-600">New Order</a>
        @if(auth()->user()->hasAnyRole(['LAUNDRY_MANAGER', 'SUPERVISOR', 'STORE_MANAGER']))
        <a href="{{ route('laundry.services.index') }}" class="text-gray-600 hover:text-blue-600">Price List</a>
        <a href="{{ route('laundry.reports.daily') }}"  class="text-gray-600 hover:text-blue-600">Reports</a>
        @endif
    </div>
    <div class="text-sm text-gray-500">
        {{ auth()->user()->name }} — {{ auth()->user()->role->name }}
    </div>
</nav>

<div class="max-w-7xl mx-auto px-6 mt-4">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-800 px-4 py-3 rounded mb-4">
            {{ session('info') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
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

**File:** `resources/views/laundry/orders/index.blade.php`

```blade
@extends('laundry.layout')

@section('title', 'Laundry Orders')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Laundry Orders</h1>
    <a href="{{ route('laundry.orders.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
        + New Order
    </a>
</div>

{{-- Status summary tiles --}}
<div class="grid grid-cols-5 gap-3 mb-6">
    @foreach([
        'received'   => ['label' => 'Received',   'color' => 'yellow'],
        'processing' => ['label' => 'Processing', 'color' => 'blue'],
        'ready'      => ['label' => 'Ready',      'color' => 'orange'],
        'delivered'  => ['label' => 'Delivered',  'color' => 'indigo'],
        'collected'  => ['label' => 'Collected',  'color' => 'teal'],
    ] as $status => $meta)
    <a href="{{ route('laundry.orders.index', ['status' => $status]) }}"
       class="bg-white rounded shadow p-4 text-center hover:shadow-md transition">
        <div class="text-2xl font-bold text-{{ $meta['color'] }}-600">
            {{ $statusCounts[$status] ?? 0 }}
        </div>
        <div class="text-xs text-gray-500 mt-1">{{ $meta['label'] }}</div>
    </a>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" class="flex gap-3 mb-6 flex-wrap">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Order #, name, room, phone..."
           class="border rounded px-3 py-2 text-sm flex-1 min-w-48">
    <select name="customer_type" class="border rounded px-3 py-2 text-sm">
        <option value="">All Customers</option>
        <option value="guest"  {{ request('customer_type') === 'guest'  ? 'selected' : '' }}>Hotel Guests</option>
        <option value="walkin" {{ request('customer_type') === 'walkin' ? 'selected' : '' }}>Walk-in</option>
    </select>
    <select name="status" class="border rounded px-3 py-2 text-sm">
        <option value="">All Statuses</option>
        @foreach(['received','processing','ready','delivered','collected','settled','cancelled'] as $s)
        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
            {{ ucfirst($s) }}
        </option>
        @endforeach
    </select>
    <input type="date" name="date" value="{{ request('date') }}"
           class="border rounded px-3 py-2 text-sm">
    <button class="bg-gray-200 px-4 py-2 rounded text-sm hover:bg-gray-300">Filter</button>
    <a href="{{ route('laundry.orders.index') }}"
       class="text-gray-400 px-3 py-2 text-sm hover:text-gray-600">Clear</a>
</form>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600">Order #</th>
                <th class="px-4 py-3 text-left text-gray-600">Customer</th>
                <th class="px-4 py-3 text-left text-gray-600">Type</th>
                <th class="px-4 py-3 text-right text-gray-600">Items</th>
                <th class="px-4 py-3 text-right text-gray-600">Total</th>
                <th class="px-4 py-3 text-center text-gray-600">Status</th>
                <th class="px-4 py-3 text-left text-gray-600">Expected Ready</th>
                <th class="px-4 py-3 text-center text-gray-600">View</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50 {{ $order->isOverdue() ? 'bg-red-50' : '' }}">
                <td class="px-4 py-3 font-mono text-xs font-medium">
                    {{ $order->order_number }}
                    @if($order->isOverdue())
                        <span class="ml-1 text-red-500 text-xs">⚠ Overdue</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($order->customer_type === 'guest')
                        <div class="font-medium">Room {{ $order->room_number }}</div>
                        <div class="text-xs text-gray-400">Hotel Guest</div>
                    @else
                        <div class="font-medium">{{ $order->customer_name }}</div>
                        <div class="text-xs text-gray-400">{{ $order->customer_phone }}</div>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs
                        {{ $order->customer_type === 'guest'
                           ? 'bg-purple-100 text-purple-700'
                           : 'bg-gray-100 text-gray-600' }}">
                        {{ $order->customer_type === 'guest' ? 'Guest' : 'Walk-in' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">{{ $order->items->count() }}</td>
                <td class="px-4 py-3 text-right font-medium">
                    {{ number_format($order->total, 2) }}
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        @if($order->status === 'received')    bg-yellow-100 text-yellow-700
                        @elseif($order->status === 'processing') bg-blue-100 text-blue-700
                        @elseif($order->status === 'ready')   bg-orange-100 text-orange-700
                        @elseif($order->status === 'delivered') bg-indigo-100 text-indigo-700
                        @elseif($order->status === 'collected') bg-teal-100 text-teal-700
                        @elseif($order->status === 'settled') bg-green-100 text-green-700
                        @else bg-gray-100 text-gray-500 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-xs
                    {{ $order->isOverdue() ? 'text-red-500 font-medium' : 'text-gray-400' }}">
                    {{ $order->expected_ready_at?->format('d M H:i') }}
                </td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('laundry.orders.show', $order) }}"
                       class="text-blue-600 hover:underline text-xs">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-400">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection
```

---

**File:** `resources/views/laundry/orders/create.blade.php`

```blade
@extends('laundry.layout')

@section('title', 'New Laundry Order')

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">New Laundry Order</h1>

    <form method="POST" action="{{ route('laundry.orders.store') }}">
        @csrf

        {{-- Customer details --}}
        <div class="bg-white rounded shadow p-6 mb-4">
            <h2 class="font-semibold text-gray-700 mb-4">Customer Details</h2>

            <div class="flex gap-4 mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="customer_type" value="guest"
                           {{ old('customer_type', 'guest') === 'guest' ? 'checked' : '' }}
                           onchange="toggleCustomerType('guest')">
                    <span class="text-sm font-medium text-gray-700">Hotel Guest</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="customer_type" value="walkin"
                           {{ old('customer_type') === 'walkin' ? 'checked' : '' }}
                           onchange="toggleCustomerType('walkin')">
                    <span class="text-sm font-medium text-gray-700">Walk-in Customer</span>
                </label>
            </div>

            {{-- Guest fields --}}
            <div id="guest-fields" class="{{ old('customer_type') === 'walkin' ? 'hidden' : '' }}">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Booking ID *</label>
                        <input type="text" name="booking_id" value="{{ old('booking_id') }}"
                               class="w-full border rounded px-3 py-2 text-sm @error('booking_id') border-red-400 @enderror">
                        @error('booking_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
                        <input type="text" name="room_number" value="{{ old('room_number') }}"
                               placeholder="e.g. 201"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            {{-- Walk-in fields --}}
            <div id="walkin-fields" class="{{ old('customer_type') !== 'walkin' ? 'hidden' : '' }}">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name *</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                               class="w-full border rounded px-3 py-2 text-sm @error('customer_name') border-red-400 @enderror">
                        @error('customer_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}"
                               placeholder="e.g. 0712 345 678"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Special Instructions</label>
                <textarea name="special_instructions" rows="2"
                          placeholder="Stains, delicate items, handle with care..."
                          class="w-full border rounded px-3 py-2 text-sm">{{ old('special_instructions') }}</textarea>
            </div>
        </div>

        {{-- Items --}}
        <div class="bg-white rounded shadow p-6 mb-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold text-gray-700">Items</h2>
                <button type="button" onclick="addRow()"
                        class="text-sm bg-blue-50 text-blue-600 px-3 py-1.5 rounded hover:bg-blue-100">
                    + Add Item
                </button>
            </div>

            <div id="items-container" class="space-y-3"></div>

            <div class="mt-4 pt-4 border-t flex justify-end">
                <p class="text-sm text-gray-600 font-semibold">
                    Estimated Total:
                    <span id="order-total" class="text-blue-600 text-lg ml-2">0.00</span>
                </p>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 text-sm font-medium">
                Create Order
            </button>
            <a href="{{ route('laundry.orders.index') }}"
               class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
const services = @json($services);
let rowIndex   = 0;

function toggleCustomerType(type) {
    document.getElementById('guest-fields').classList.toggle('hidden', type === 'walkin');
    document.getElementById('walkin-fields').classList.toggle('hidden', type === 'guest');
}

function addRow() {
    const container = document.getElementById('items-container');
    const row       = document.createElement('div');
    row.id          = `row-${rowIndex}`;
    row.className   = 'grid grid-cols-12 gap-2 items-end p-3 border rounded bg-gray-50';

    let options = '<option value="">Select service & item...</option>';
    services.forEach(service => {
        options += `<optgroup label="${service.name} (${service.turnaround_hours}h)">`;
        service.service_items.forEach(item => {
            options += `<option value="${item.id}" data-price="${item.price}">
                ${item.item_name} — ${parseFloat(item.price).toLocaleString()}
            </option>`;
        });
        options += '</optgroup>';
    });

    row.innerHTML = `
        <div class="col-span-6">
            <label class="block text-xs text-gray-500 mb-1">Service & Item</label>
            <select name="items[${rowIndex}][service_item_id]"
                    onchange="recalculate()"
                    class="w-full border rounded px-2 py-1.5 text-sm" required>
                ${options}
            </select>
        </div>
        <div class="col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Quantity</label>
            <input type="number" name="items[${rowIndex}][quantity]"
                   value="1" min="1" onchange="recalculate()"
                   class="w-full border rounded px-2 py-1.5 text-sm" required>
        </div>
        <div class="col-span-3">
            <label class="block text-xs text-gray-500 mb-1">Notes</label>
            <input type="text" name="items[${rowIndex}][notes]"
                   placeholder="Stain, delicate..."
                   class="w-full border rounded px-2 py-1.5 text-sm">
        </div>
        <div class="col-span-1 flex justify-end">
            <button type="button" onclick="removeRow('row-${rowIndex}')"
                    class="text-red-400 hover:text-red-600 text-xl font-bold leading-none">×</button>
        </div>
    `;

    container.appendChild(row);
    rowIndex++;
    recalculate();
}

function removeRow(id) {
    document.getElementById(id)?.remove();
    recalculate();
}

function recalculate() {
    let total = 0;
    document.querySelectorAll('#items-container > div').forEach(row => {
        const select = row.querySelector('select');
        const qty    = row.querySelector('input[type=number]');
        if (select?.value && qty) {
            const price = parseFloat(select.selectedOptions[0]?.dataset?.price || 0);
            total += price * parseInt(qty.value || 1);
        }
    });
    document.getElementById('order-total').textContent =
        total.toLocaleString('en-US', { minimumFractionDigits: 2 });
}

// Start with one row
addRow();
</script>
@endsection
```

---

**File:** `resources/views/laundry/orders/show.blade.php`

```blade
@extends('laundry.layout')

@section('title', 'Order ' . $laundryOrder->order_number)

@section('content')

{{-- Header --}}
<div class="flex justify-between items-start mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $laundryOrder->order_number }}</h1>
        <p class="text-sm text-gray-400 mt-1">
            @if($laundryOrder->customer_type === 'guest')
                Hotel Guest · Room {{ $laundryOrder->room_number }}
            @else
                Walk-in · {{ $laundryOrder->customer_name }}
                @if($laundryOrder->customer_phone) · {{ $laundryOrder->customer_phone }} @endif
            @endif
        </p>
    </div>
    <span class="px-3 py-1.5 rounded-full text-sm font-medium
        @if($laundryOrder->status === 'received')    bg-yellow-100 text-yellow-700
        @elseif($laundryOrder->status === 'processing') bg-blue-100 text-blue-700
        @elseif($laundryOrder->status === 'ready')   bg-orange-100 text-orange-700
        @elseif($laundryOrder->status === 'delivered') bg-indigo-100 text-indigo-700
        @elseif($laundryOrder->status === 'collected') bg-teal-100 text-teal-700
        @elseif($laundryOrder->status === 'settled') bg-green-100 text-green-700
        @else bg-gray-100 text-gray-500 @endif">
        {{ ucfirst($laundryOrder->status) }}
    </span>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Items --}}
    <div class="col-span-2 space-y-4">
        <div class="bg-white rounded shadow overflow-hidden">
            <div class="px-5 py-4 border-b">
                <h2 class="font-semibold text-gray-700">Items</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-500">Service</th>
                        <th class="px-4 py-2 text-left text-gray-500">Item</th>
                        <th class="px-4 py-2 text-right text-gray-500">Qty</th>
                        <th class="px-4 py-2 text-right text-gray-500">Unit Price</th>
                        <th class="px-4 py-2 text-right text-gray-500">Subtotal</th>
                        <th class="px-4 py-2 text-left text-gray-500">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($laundryOrder->items as $item)
                    <tr>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $item->serviceItem->service->name }}</td>
                        <td class="px-4 py-3 font-medium">{{ $item->serviceItem->item_name }}</td>
                        <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ number_format($item->subtotal, 2) }}</td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $item->notes ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t bg-gray-50">
                    @if($laundryOrder->discount > 0)
                    <tr>
                        <td colspan="3"></td>
                        <td class="px-4 py-2 text-right text-gray-500 text-sm">Discount</td>
                        <td class="px-4 py-2 text-right text-red-500">
                            - {{ number_format($laundryOrder->discount, 2) }}
                        </td>
                        <td></td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3"></td>
                        <td class="px-4 py-2 text-right font-bold text-gray-800">Total</td>
                        <td class="px-4 py-2 text-right font-bold text-gray-800">
                            {{ number_format($laundryOrder->total, 2) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Action buttons --}}
        @if(!in_array($laundryOrder->status, ['settled', 'cancelled']))
        <div class="flex gap-3 flex-wrap">

            {{-- HOUSE_HELP: Start processing --}}
            @if($laundryOrder->status === 'received' && auth()->user()->hasAnyRole(['HOUSE_HELP','SUPERVISOR','LAUNDRY_MANAGER']))
            <form method="POST" action="{{ route('laundry.orders.process', $laundryOrder) }}">
                @csrf
                <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Start Processing
                </button>
            </form>
            @endif

            {{-- HOUSE_HELP: Mark ready --}}
            @if($laundryOrder->status === 'processing' && auth()->user()->hasAnyRole(['HOUSE_HELP','SUPERVISOR','LAUNDRY_MANAGER']))
            <form method="POST" action="{{ route('laundry.orders.ready', $laundryOrder) }}">
                @csrf
                <button class="bg-orange-500 text-white px-4 py-2 rounded text-sm hover:bg-orange-600">
                    Mark Ready
                </button>
            </form>
            @endif

            {{-- Guest: deliver to room --}}
            @if($laundryOrder->status === 'ready' && $laundryOrder->customer_type === 'guest'
                && auth()->user()->hasAnyRole(['HOUSE_HELP','SUPERVISOR','LAUNDRY_MANAGER']))
            <form method="POST" action="{{ route('laundry.orders.deliver', $laundryOrder) }}">
                @csrf
                <button class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                    Deliver to Room {{ $laundryOrder->room_number }}
                </button>
            </form>
            @endif

            {{-- Walk-in: mark collected --}}
            @if($laundryOrder->status === 'ready' && $laundryOrder->customer_type === 'walkin'
                && auth()->user()->hasAnyRole(['HOUSE_HELP','CASHIER','SUPERVISOR','LAUNDRY_MANAGER']))
            <form method="POST" action="{{ route('laundry.orders.collected', $laundryOrder) }}">
                @csrf
                <button class="bg-teal-600 text-white px-4 py-2 rounded text-sm hover:bg-teal-700">
                    Mark Collected
                </button>
            </form>
            @endif

            {{-- Settle payment --}}
            @if(auth()->user()->hasAnyRole(['CASHIER','FRONT_DESK','LAUNDRY_MANAGER','SUPERVISOR']))
            <button onclick="document.getElementById('settle-panel').classList.toggle('hidden')"
                    class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                Settle Payment
            </button>
            @endif

            {{-- Cancel --}}
            @if(auth()->user()->hasAnyRole(['SUPERVISOR','LAUNDRY_MANAGER']))
            <form method="POST" action="{{ route('laundry.orders.cancel', $laundryOrder) }}"
                  onsubmit="return confirm('Cancel this order?')">
                @csrf
                <button class="bg-red-500 text-white px-4 py-2 rounded text-sm hover:bg-red-600">
                    Cancel Order
                </button>
            </form>
            @endif

        </div>

        {{-- Settle panel --}}
        <div id="settle-panel" class="hidden bg-white rounded shadow p-5 mt-2">
            <h3 class="font-semibold text-gray-700 mb-4">Settle Payment</h3>
            <form method="POST" action="{{ route('laundry.orders.settle', $laundryOrder) }}">
                @csrf
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Payment Method *
                        </label>
                        <select name="payment_method"
                                class="w-full border rounded px-3 py-2 text-sm"
                                onchange="document.getElementById('booking-field').classList.toggle(
                                    'hidden', this.value !== 'charge_to_booking')">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            @if($laundryOrder->customer_type === 'guest')
                            <option value="charge_to_booking" selected>Charge to Booking</option>
                            @endif
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Discount (optional)
                        </label>
                        <input type="number" name="discount" value="0" min="0" step="0.01"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div id="booking-field"
                         class="{{ $laundryOrder->customer_type === 'guest' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Booking ID</label>
                        <input type="text" name="booking_id"
                               value="{{ $laundryOrder->booking_id }}"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                </div>
                <button type="submit"
                        class="mt-4 bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700 text-sm">
                    Confirm — {{ number_format($laundryOrder->total, 2) }}
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        <div class="bg-white rounded shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-3">Order Info</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Received by</dt>
                    <dd>{{ $laundryOrder->receiver->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Received at</dt>
                    <dd>{{ $laundryOrder->created_at->format('d M Y H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Expected ready</dt>
                    <dd class="{{ $laundryOrder->isOverdue() ? 'text-red-500 font-medium' : '' }}">
                        {{ $laundryOrder->expected_ready_at?->format('d M Y H:i') }}
                        @if($laundryOrder->isOverdue()) ⚠ @endif
                    </dd>
                </div>
                @if($laundryOrder->processed_by)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Processed by</dt>
                    <dd>{{ $laundryOrder->processor->name }}</dd>
                </div>
                @endif
                @if($laundryOrder->ready_at)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Ready at</dt>
                    <dd>{{ $laundryOrder->ready_at->format('d M H:i') }}</dd>
                </div>
                @endif
                @if($laundryOrder->delivered_at)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Delivered at</dt>
                    <dd>{{ $laundryOrder->delivered_at->format('d M H:i') }}</dd>
                </div>
                @endif
                @if($laundryOrder->collected_at)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Collected at</dt>
                    <dd>{{ $laundryOrder->collected_at->format('d M H:i') }}</dd>
                </div>
                @endif
                @if($laundryOrder->status === 'settled')
                <div class="flex justify-between border-t pt-2">
                    <dt class="text-gray-500">Payment</dt>
                    <dd class="text-green-700 font-medium">
                        {{ ucwords(str_replace('_', ' ', $laundryOrder->payment_method)) }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Settled by</dt>
                    <dd>{{ $laundryOrder->settler->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Settled at</dt>
                    <dd>{{ $laundryOrder->settled_at->format('d M H:i') }}</dd>
                </div>
                @endif
                @if($laundryOrder->special_instructions)
                <div class="border-t pt-2">
                    <dt class="text-gray-500 text-xs mb-1">Special Instructions</dt>
                    <dd class="text-gray-700 text-xs">{{ $laundryOrder->special_instructions }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

</div>
@endsection
```

---

## 9. Routes

**File:** `routes/web.php` — add inside your `auth` middleware group.

```php
use App\Http\Controllers\Laundry\LaundryServiceController;
use App\Http\Controllers\Laundry\LaundryOrderController;
use App\Http\Controllers\Laundry\LaundryReportController;

Route::middleware(['auth'])->prefix('laundry')->name('laundry.')->group(function () {

    // ── Price List ────────────────────────────────────────────────────────────
    Route::get('services', [LaundryServiceController::class, 'index'])
         ->name('services.index')
         ->middleware('role:LAUNDRY_MANAGER,SUPERVISOR,STORE_MANAGER');
    Route::post('services/{service}/items', [LaundryServiceController::class, 'addItem'])
         ->name('services.add-item')
         ->middleware('role:LAUNDRY_MANAGER,STORE_MANAGER');
    Route::put('services/{service}/items/{item}', [LaundryServiceController::class, 'updateItem'])
         ->name('services.update-item')
         ->middleware('role:LAUNDRY_MANAGER,STORE_MANAGER');
    Route::delete('services/{service}/items/{item}', [LaundryServiceController::class, 'removeItem'])
         ->name('services.remove-item')
         ->middleware('role:LAUNDRY_MANAGER,STORE_MANAGER');

    // ── Orders ────────────────────────────────────────────────────────────────
    Route::get('orders', [LaundryOrderController::class, 'index'])
         ->name('orders.index');
    Route::get('orders/create', [LaundryOrderController::class, 'create'])
         ->name('orders.create')
         ->middleware('role:HOUSE_HELP,FRONT_DESK,SUPERVISOR,LAUNDRY_MANAGER');
    Route::post('orders', [LaundryOrderController::class, 'store'])
         ->name('orders.store')
         ->middleware('role:HOUSE_HELP,FRONT_DESK,SUPERVISOR,LAUNDRY_MANAGER');
    Route::get('orders/{laundryOrder}', [LaundryOrderController::class, 'show'])
         ->name('orders.show');
    Route::post('orders/{laundryOrder}/process', [LaundryOrderController::class, 'process'])
         ->name('orders.process')
         ->middleware('role:HOUSE_HELP,SUPERVISOR,LAUNDRY_MANAGER');
    Route::post('orders/{laundryOrder}/ready', [LaundryOrderController::class, 'markReady'])
         ->name('orders.ready')
         ->middleware('role:HOUSE_HELP,SUPERVISOR,LAUNDRY_MANAGER');
    Route::post('orders/{laundryOrder}/deliver', [LaundryOrderController::class, 'deliver'])
         ->name('orders.deliver')
         ->middleware('role:HOUSE_HELP,SUPERVISOR,LAUNDRY_MANAGER');
    Route::post('orders/{laundryOrder}/collected', [LaundryOrderController::class, 'collected'])
         ->name('orders.collected')
         ->middleware('role:HOUSE_HELP,CASHIER,SUPERVISOR,LAUNDRY_MANAGER');
    Route::post('orders/{laundryOrder}/settle', [LaundryOrderController::class, 'settle'])
         ->name('orders.settle')
         ->middleware('role:CASHIER,FRONT_DESK,LAUNDRY_MANAGER,SUPERVISOR');
    Route::post('orders/{laundryOrder}/cancel', [LaundryOrderController::class, 'cancel'])
         ->name('orders.cancel')
         ->middleware('role:SUPERVISOR,LAUNDRY_MANAGER');

    // ── Reports ───────────────────────────────────────────────────────────────
    Route::get('reports/daily', [LaundryReportController::class, 'daily'])
         ->name('reports.daily')
         ->middleware('role:LAUNDRY_MANAGER,SUPERVISOR,STORE_MANAGER');
});
```

---

## 10. Business Rules

| # | Rule | Where Enforced |
|---|---|---|
| 001 | Price snapshot on order creation — price changes don't affect open orders | `unit_price` stored on `laundry_order_items` at creation time |
| 002 | Expected ready time = longest turnaround service in the order | `store()` finds `max(turnaround_hours)` across selected items |
| 003 | Deliver only works for guest orders | `deliver()` aborts if `customer_type !== guest` |
| 004 | Collected only works for walk-in orders | `collected()` aborts if `customer_type !== walkin` |
| 005 | Guest orders create a `booking_charge` at settlement | `settle()` creates `BookingCharge` when `payment_method = charge_to_booking` |
| 006 | Walk-in customers pay cash or card — no booking charge | Payment method options in settle form match `customer_type` |
| 007 | Cannot settle a cancelled order | `settle()` checks status |
| 008 | Cannot cancel a settled order | `cancel()` checks status |
| 009 | Discount applied at settlement, not creation | `settle()` updates discount and recalculates total |
| 010 | Overdue orders are highlighted in the index | `isOverdue()` on the model — red highlight in table |

---

## 11. Build Order

```bash
# 1. Migrations
php artisan make:migration add_laundry_manager_role
php artisan make:migration create_laundry_services_table
php artisan make:migration create_laundry_service_items_table
php artisan make:migration create_laundry_orders_table
php artisan make:migration create_laundry_order_items_table
php artisan migrate

# 2. Seed
php artisan db:seed --class=LaundryServiceSeeder

# 3. Build in this order — test each before next
# LaundryServiceController → price list view  → routes
# LaundryOrderController   → orders (create, index, show, all actions) → routes
# LaundryReportController  → daily report → routes

# 4. Critical test cases
# ✓ HOUSE_HELP creates guest order → mark processing → mark ready → deliver → CASHIER charges to booking
# ✓ HOUSE_HELP creates walk-in order → mark ready → mark collected → CASHIER settles cash
# ✓ CASHIER settles walk-in with card — no booking_charge created
# ✓ Try to deliver a walk-in order → blocked (422)
# ✓ Try to mark collected on guest order → blocked (422)
# ✓ Cancel a settled order → blocked
# ✓ Dry cleaning + Wash Only in same order → expected_ready_at = 48h (dry cleaning wins)
# ✓ Discount applied at settle → total updates correctly
```
