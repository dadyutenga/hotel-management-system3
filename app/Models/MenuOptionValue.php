<?php

namespace App\Models;

use App\Traits\BuildingScoped;
use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class MenuOptionValue extends Model
{
    use BuildingScoped, HasSoftDelete, HasUuid;

    protected $fillable = [
        'building_id',
        'menu_option_group_id',
        'label',
        'price_delta',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price_delta' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(MenuOptionGroup::class, 'menu_option_group_id');
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
}
