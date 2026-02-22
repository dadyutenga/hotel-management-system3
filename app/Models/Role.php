<?php
// app/Models/Role.php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model {
    use HasUuid;

    protected $fillable = ['name', 'description'];

    // Existing roles
    public const ADMIN = 'admin';
    public const FRONT_DESK = 'front_desk';
    public const SUPERVISOR = 'supervisor';
    public const HOUSE_HELP = 'house_help';
    public const STORE_MANAGER = 'store_manager';
    public const STORE_KEEPER = 'store_keeper';

    // New roles for Bar / Restaurant / Cashier modules
    public const BAR_MANAGER = 'bar_manager';
    public const BAR_TENDER = 'bar_tender';
    public const KITCHEN_MANAGER = 'kitchen_manager';
    public const WAITER = 'waiter';
    public const CASHIER = 'cashier';

    public static array $roles = [
        self::ADMIN           => 'System administrator with full access',
        self::FRONT_DESK      => 'Booking management and guest charges',
        self::SUPERVISOR      => 'Approvals and operational oversight',
        self::HOUSE_HELP      => 'Internal usage requests only',
        self::STORE_MANAGER   => 'Full store control, pricing, all reports',
        self::STORE_KEEPER    => 'Stock operations, restock, fulfillments',
        self::BAR_MANAGER     => 'Bar inventory and menu management',
        self::BAR_TENDER      => 'Bar order taking and stock view',
        self::KITCHEN_MANAGER => 'Kitchen inventory and recipe management',
        self::WAITER          => 'Table order taking',
        self::CASHIER         => 'Payment settlement and shift reports',
    ];

    public static function seedRoles(): void {
        foreach (self::$roles as $name => $description) {
            self::updateOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }
    }

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
}