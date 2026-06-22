<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuildingModule extends Model
{
    use HasSoftDelete, HasUuid;

    public const TYPE_RESTAURANT = 'restaurant';

    public const TYPE_BAR = 'bar';

    protected $fillable = [
        'building_id',
        'type',
        'name',
        'code',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function scopeRestaurants($query)
    {
        return $query->where('type', self::TYPE_RESTAURANT);
    }

    public function scopeBars($query)
    {
        return $query->where('type', self::TYPE_BAR);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('is_active', true);
    }

    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForBuilding($query, ?string $buildingId)
    {
        if ($buildingId) {
            return $query->where('building_id', $buildingId);
        }

        return $query;
    }

    public function isRestaurant(): bool
    {
        return $this->type === self::TYPE_RESTAURANT;
    }

    public function isBar(): bool
    {
        return $this->type === self::TYPE_BAR;
    }
}
