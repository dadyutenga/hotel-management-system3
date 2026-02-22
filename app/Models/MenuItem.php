<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasUuid;

    protected $fillable = [
        'category_id', 'name', 'description',
        'selling_price', 'is_available', 'is_active', 'created_by',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'is_available'  => 'boolean',
        'is_active'     => 'boolean',
    ];

    public function category()    { return $this->belongsTo(MenuCategory::class, 'category_id'); }
    public function ingredients() { return $this->hasMany(MenuItemIngredient::class); }
    public function createdBy()   { return $this->belongsTo(User::class, 'created_by'); }

    /**
     * Check if all ingredients have enough stock in their location.
     */
    public function hasEnoughStock(int $qty = 1): bool
    {
        foreach ($this->ingredients as $ingredient) {
            $locationId = $this->category->location_id;
            $level = StockLevel::where('product_id', $ingredient->product_id)
                               ->where('location_id', $locationId)
                               ->first();

            if (!$level || $level->available_qty < ($ingredient->quantity * $qty)) {
                return false;
            }
        }
        return true;
    }
}
