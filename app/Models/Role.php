<?php
// app/Models/Role.php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model {
    use HasUuid;

    protected $fillable = ['name', 'display_name'];

    public const ADMIN = 'admin';
    public const FRONT_DESK = 'front_desk';
    public const SUPERVISOR = 'supervisor';
    public const HOUSE_HELP = 'house_help';
    public const MANAGER = 'manager';
    public const STORE_KEEPER = 'store_keeper';

    public static array $roles = [
        self::ADMIN => 'Administrator',
        self::FRONT_DESK => 'Front Desk',
        self::SUPERVISOR => 'Supervisor',
        self::HOUSE_HELP => 'House Help',
        self::MANAGER => 'Manager',
        self::STORE_KEEPER => 'Store Keeper',
    ];

    public static function seedRoles(): void {
        foreach (self::$roles as $name => $displayName) {
            self::firstOrCreate(
                ['name' => $name],
                ['display_name' => $displayName]
            );
        }
    }

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
}