<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        $adminRole = Role::where('name', Role::ADMIN)->first();
        $frontDeskRole = Role::where('name', Role::FRONT_DESK)->first();
        $managerRole = Role::where('name', Role::MANAGER)->first();
        $supervisorRole = Role::where('name', Role::SUPERVISOR)->first();
        $houseHelpRole = Role::where('name', Role::HOUSE_HELP)->first();
        $storeManagerRole = Role::where('name', Role::STORE_MANAGER)->first();
        $storeKeeperRole = Role::where('name', Role::STORE_KEEPER)->first();
        $restaurantManagerRole = Role::where('name', Role::RESTAURANT_MANAGER)->first();
        $waiterRole = Role::where('name', Role::WAITER)->first();
        $barTenderRole = Role::where('name', Role::BAR_TENDER)->first();
        $laundryManagerRole = Role::where('name', Role::LAUNDRY_MANAGER)->first();
        $accountantRole = Role::where('name', Role::ACCOUNTANT)->first();

        $defaultPassword = Hash::make('password');

        $seedUser = function (string $email, string $name, ?Role $role) use ($defaultPassword): void {
            if ($role === null) {
                return;
            }

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => $defaultPassword,
                    'role_id' => $role->id,
                    'is_active' => true,
                ]
            );
        };

        $seedUser('admin@hotel.com', 'System Administrator', $adminRole);
        $seedUser('frontdesk@hotel.com', 'Front Desk Officer', $frontDeskRole);
        $seedUser('manager@hotel.com', 'General Manager', $managerRole);
        $seedUser('supervisor@hotel.com', 'Operations Supervisor', $supervisorRole);
        $seedUser('househelp@hotel.com', 'House Help Staff', $houseHelpRole);
        $seedUser('storemanager@hotel.com', 'Store Manager', $storeManagerRole);
        $seedUser('storekeeper@hotel.com', 'Store Keeper', $storeKeeperRole);
        $seedUser('restaurantmanager@hotel.com', 'Restaurant Manager', $restaurantManagerRole);
        $seedUser('waiter@hotel.com', 'Restaurant Waiter', $waiterRole);
        $seedUser('bartender@hotel.com', 'Bar Tender', $barTenderRole);
        $seedUser('laundrymanager@hotel.com', 'Laundry Manager', $laundryManagerRole);
        $seedUser('accountant@hotel.com', 'Accountant', $accountantRole);
    }
}