<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuid;

    protected $fillable = [
        'order_number', 'location_id', 'table_id', 'order_type',
        'booking_id', 'customer_name', 'status',
        'subtotal', 'discount', 'tax', 'total',
        'payment_method', 'notes', 'created_by', 'settled_by', 'settled_at',
    ];

    protected $casts = [
        'subtotal'   => 'decimal:2',
        'discount'   => 'decimal:2',
        'tax'        => 'decimal:2',
        'total'      => 'decimal:2',
        'settled_at' => 'datetime',
    ];

    /**
     * Auto-generate order number before creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $location = StockLocation::find($order->location_id);
            $prefix   = strtoupper(substr($location->code ?? 'ORD', 0, 3));
            $count    = self::whereDate('created_at', today())->count() + 1;
            $order->order_number = $prefix . '-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Recalculate totals from order items.
     */
    public function recalculate(): void
    {
        $this->load('items');
        $subtotal = $this->items->where('status', '!=', 'cancelled')->sum('subtotal');
        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal - $this->discount + $this->tax,
        ]);
    }

    public function location()  { return $this->belongsTo(StockLocation::class, 'location_id'); }
    public function table()     { return $this->belongsTo(Table::class); }
    public function items()     { return $this->hasMany(OrderItem::class); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
    public function settler()   { return $this->belongsTo(User::class, 'settled_by'); }
    public function charge()    { return $this->hasOne(BookingCharge::class); }
}
