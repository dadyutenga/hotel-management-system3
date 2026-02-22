<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasUuid;

    protected $fillable = [
        'order_id', 'menu_item_id', 'quantity', 'unit_price', 'subtotal', 'notes', 'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'quantity'   => 'integer',
    ];

    public function order()    { return $this->belongsTo(Order::class); }
    public function menuItem() { return $this->belongsTo(MenuItem::class); }
}
