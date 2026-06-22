<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

trait BuildingScoped
{
    public function scopeForBuilding($query, ?string $buildingId)
    {
        if ($buildingId && Schema::hasColumn($this->getTable(), 'building_id')) {
            return $query->where($this->getTable().'.building_id', $buildingId);
        }

        return $query;
    }

    public function scopeForUserBuilding($query)
    {
        $user = auth()->user();

        if (! $user || $user->isAdmin()) {
            return $query;
        }

        return $query->forBuilding($user->building_id);
    }
}
