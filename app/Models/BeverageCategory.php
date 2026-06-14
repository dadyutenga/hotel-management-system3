<?php

namespace App\Models;

use App\Traits\BuildingScoped;
use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BeverageCategory extends Model
{
    use BuildingScoped, HasSoftDelete, HasUuid;

    protected $fillable = ['name', 'description', 'is_active', 'building_id'];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function beverages(): HasMany
    {
        return $this->hasMany(Beverage::class, 'category_id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
