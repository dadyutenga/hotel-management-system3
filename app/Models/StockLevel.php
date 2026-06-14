<?php

namespace App\Models;

use App\Traits\BuildingScoped;
use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class StockLevel extends Model
{
    use BuildingScoped, HasSoftDelete, HasUuid;

    public $timestamps = false;

    protected $fillable = ['product_id', 'location_id', 'quantity', 'reserved_qty', 'last_counted_at', 'building_id'];

    protected $casts = [
        'quantity' => 'decimal:3',
        'reserved_qty' => 'decimal:3',
        'deleted_at' => 'datetime',
    ];

    /**
     * Available = total minus anything reserved for pending orders.
     */
    public function getAvailableQtyAttribute(): float
    {
        return (float) $this->quantity - (float) $this->reserved_qty;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(StockLocation::class, 'location_id');
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
