<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasUuid;

    protected $fillable = ['name', 'location_id', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function location()  { return $this->belongsTo(StockLocation::class, 'location_id'); }
    public function menuItems() { return $this->hasMany(MenuItem::class, 'category_id'); }
}
