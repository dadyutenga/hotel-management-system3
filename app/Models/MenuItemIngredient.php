<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class MenuItemIngredient extends Model
{
    use HasUuid;

    protected $fillable = ['menu_item_id', 'product_id', 'quantity', 'unit'];

    protected $casts = ['quantity' => 'decimal:4'];

    public function menuItem() { return $this->belongsTo(MenuItem::class); }
    public function product()  { return $this->belongsTo(Product::class); }
}
