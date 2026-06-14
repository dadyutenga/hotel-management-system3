<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Building extends Model
{
    use HasFactory, HasSoftDelete, HasUuid;

    protected $fillable = ['name', 'code', 'address', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected static function booted(): void
    {
        static::created(function (Building $building) {
            $locationCodes = [
                'main_store' => ['name' => 'Main Store', 'description' => 'Central inventory'],
                'bar' => ['name' => 'Bar', 'description' => 'Bar section inventory'],
                'kitchen' => ['name' => 'Kitchen', 'description' => 'Kitchen section inventory'],
            ];

            foreach ($locationCodes as $code => $data) {
                StockLocation::firstOrCreate(
                    ['building_id' => $building->id, 'code' => $code],
                    [
                        'name' => $building->name.' '.$data['name'],
                        'description' => $data['description'],
                        'is_active' => true,
                    ]
                );
            }

            BuildingModule::firstOrCreate(
                ['building_id' => $building->id, 'type' => BuildingModule::TYPE_RESTAURANT, 'code' => 'default'],
                ['name' => $building->name.' Restaurant', 'status' => 'active', 'is_active' => true]
            );

            BuildingModule::firstOrCreate(
                ['building_id' => $building->id, 'type' => BuildingModule::TYPE_BAR, 'code' => 'default'],
                ['name' => $building->name.' Bar', 'status' => 'active', 'is_active' => true]
            );
        });
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    // Add this relationship
    public function rooms(): HasManyThrough
    {
        return $this->hasManyThrough(
            Room::class,
            Floor::class,
            'building_id', // Foreign key on Floor table
            'floor_id',    // Foreign key on Room table
            'id',          // Local key on Building table
            'id'           // Local key on Floor table
        );
    }

    public function modules(): HasMany
    {
        return $this->hasMany(BuildingModule::class);
    }

    public function restaurants(): HasMany
    {
        return $this->modules()->where('type', BuildingModule::TYPE_RESTAURANT);
    }

    public function bars(): HasMany
    {
        return $this->modules()->where('type', BuildingModule::TYPE_BAR);
    }

    public function stockLocations(): HasMany
    {
        return $this->hasMany(StockLocation::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
