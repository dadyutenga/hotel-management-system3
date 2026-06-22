<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;

class AuthMethodResolver
{
    /**
     * Roles that authenticate via the admin login page (email + password).
     * Only admin and managers use passwords.
     */
    private const ADMIN_LOGIN_ROLES = [
        Role::ADMIN,
        Role::MANAGER,
    ];

    /**
     * Roles that authenticate via the staff login page (email + PIN).
     * All other roles use PIN-based authentication.
     */
    private const STAFF_LOGIN_ROLES = [
        Role::SUPERVISOR,
        Role::FRONT_DESK,
        Role::HOUSE_HELP,
        Role::STORE_MANAGER,
        Role::STORE_KEEPER,
        Role::ACCOUNTANT,
        Role::STOCK_CONTROLLER,
        Role::RESTAURANT_MANAGER,
        Role::LAUNDRY_MANAGER,
        Role::WAITER,
        Role::CASHIER,
        Role::BAR_TENDER,
        Role::POS_BAR,
        Role::POS_KITCHEN,
    ];

    /**
     * Check if a user can authenticate via the admin login page.
     */
    public static function canUseAdminLogin(User $user): bool
    {
        $roleName = $user->roleName();

        if ($roleName === null) {
            return false;
        }

        foreach (self::ADMIN_LOGIN_ROLES as $allowedRole) {
            if (Role::matches($roleName, $allowedRole)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a user can authenticate via the staff login page.
     * All non-admin/manager roles use staff login.
     */
    public static function canUseStaffLogin(User $user): bool
    {
        $roleName = $user->roleName();

        if ($roleName === null) {
            return false;
        }

        // Admin and managers cannot use staff PIN login
        foreach (self::ADMIN_LOGIN_ROLES as $adminRole) {
            if (Role::matches($roleName, $adminRole)) {
                return false;
            }
        }

        // Everyone else can
        return true;
    }

    /**
     * Get the auth method for a given role name.
     *
     * @return string 'admin' or 'staff'
     */
    public static function forRole(string $roleName): string
    {
        $normalized = Role::normalizeName($roleName);

        foreach (self::ADMIN_LOGIN_ROLES as $adminRole) {
            if (Role::matches($normalized, $adminRole)) {
                return 'admin';
            }
        }

        return 'staff';
    }

    /**
     * Get all roles that use admin login.
     */
    public static function adminLoginRoles(): array
    {
        return self::ADMIN_LOGIN_ROLES;
    }

    /**
     * Get all roles that use staff login.
     */
    public static function staffLoginRoles(): array
    {
        return self::STAFF_LOGIN_ROLES;
    }

    /**
     * Check if a role requires a password for authentication.
     */
    public static function requiresPassword(string $roleName): bool
    {
        return self::forRole($roleName) === 'admin';
    }

    /**
     * Check if a role requires a passkey/PIN for authentication.
     */
    public static function requiresPasskey(string $roleName): bool
    {
        return self::forRole($roleName) === 'staff';
    }
}
