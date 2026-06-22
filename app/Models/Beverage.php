<?php

namespace App\Models;

use App\Traits\BuildingScoped;
use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Beverage extends Model
{
    use BuildingScoped, HasSoftDelete, HasUuid;

    protected $fillable = [
        'barcode', 'name', 'category_id', 'unit', 'buying_price',
        'selling_price', 'reorder_level', 'description', 'image',
        'is_active', 'created_by', 'building_id',
    ];

    protected $casts = [
        'buying_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'reorder_level' => 'integer',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BeverageCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(BeverageInventory::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(BeverageStockMovement::class);
    }

    public function receivingItems(): HasMany
    {
        return $this->hasMany(StockReceivingItem::class);
    }

    public function stockTakeItems(): HasMany
    {
        return $this->hasMany(StockTakeItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByBarcode($query, string $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    public function getQuantityOnHandAttribute(): int
    {
        return $this->inventory?->quantity_on_hand ?? 0;
    }
}
