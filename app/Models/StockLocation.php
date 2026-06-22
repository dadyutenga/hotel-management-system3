<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class StockLocation extends Model
{
    use HasSoftDelete, HasUuid;

    protected $fillable = ['building_id', 'name', 'code', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public static function forBuilding(string $buildingId, string $code): self
    {
        return static::where('building_id', $buildingId)
            ->where('code', $code)
            ->firstOrFail();
    }

    public static function mainStore(?string $buildingId = null): self
    {
        return static::forBuilding($buildingId ?? self::defaultBuildingId(), 'main_store');
    }

    public static function bar(?string $buildingId = null): self
    {
        return static::forBuilding($buildingId ?? self::defaultBuildingId(), 'bar');
    }

    public static function kitchen(?string $buildingId = null): self
    {
        return static::forBuilding($buildingId ?? self::defaultBuildingId(), 'kitchen');
    }

    public static function defaultBuildingId(): string
    {
        $user = auth()->user();

        if ($user && ! $user->isAdmin() && $user->building_id) {
            return $user->building_id;
        }

        $first = Building::active()->first();

        return $first?->id ?? throw new \RuntimeException('No building configured.');
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class, 'location_id');
    }
}
