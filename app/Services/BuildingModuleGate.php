<?php

namespace App\Services;

use App\Models\Building;
use App\Models\BuildingModule;

class BuildingModuleGate
{
    public static function hasRestaurant(?string $buildingId = null): bool
    {
        return self::hasModule(BuildingModule::TYPE_RESTAURANT, $buildingId);
    }

    public static function hasBar(?string $buildingId = null): bool
    {
        return self::hasModule(BuildingModule::TYPE_BAR, $buildingId);
    }

    public static function hasModule(string $type, ?string $buildingId = null): bool
    {
        $buildingId ??= BuildingContext::buildingId();

        if (! $buildingId) {
            return true;
        }

        return BuildingModule::where('building_id', $buildingId)
            ->where('type', $type)
            ->where('status', 'active')
            ->where('is_active', true)
            ->exists();
    }

    public static function ensureModule(string $type, ?string $buildingId = null, ?string $message = null): void
    {
        if (! self::hasModule($type, $buildingId)) {
            abort(403, $message ?? 'The required building module is not active for this building.');
        }
    }

    public static function ensureRestaurant(?string $buildingId = null): void
    {
        self::ensureModule(BuildingModule::TYPE_RESTAURANT, $buildingId, 'No active restaurant module for this building.');
    }

    public static function ensureBar(?string $buildingId = null): void
    {
        self::ensureModule(BuildingModule::TYPE_BAR, $buildingId, 'No active bar module for this building.');
    }

    public static function availableModuleTypes(Building $building): array
    {
        $types = [];

        if (self::hasRestaurant($building->id)) {
            $types[] = BuildingModule::TYPE_RESTAURANT;
        }

        if (self::hasBar($building->id)) {
            $types[] = BuildingModule::TYPE_BAR;
        }

        return $types;
    }
}
