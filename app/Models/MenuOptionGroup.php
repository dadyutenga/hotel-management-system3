<?php

namespace App\Models;

use App\Traits\BuildingScoped;
use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class MenuOptionGroup extends Model
{
    use BuildingScoped, HasSoftDelete, HasUuid;

    protected $fillable = [
        'building_id',
        'name',
        'selection_type',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function values()
    {
        return $this->hasMany(MenuOptionValue::class)->orderBy('sort_order')->orderBy('label');
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_option_group')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}
