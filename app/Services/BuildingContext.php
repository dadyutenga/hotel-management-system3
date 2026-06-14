<?php

namespace App\Services;

use App\Models\User;

class BuildingContext
{
    public static function buildingId(): ?string
    {
        $user = auth()->user();

        return ($user instanceof User && ! $user->isAdmin()) ? $user->building_id : null;
    }

    public static function user(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();

        return $user && $user->isAdmin();
    }

    public static function enforce(?string $buildingId): void
    {
        $current = self::buildingId();

        if ($current && $buildingId !== $current) {
            abort(403, 'This resource does not belong to your building.');
        }
    }
}
