<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        $roles = Role::pluck('id', 'name');

        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@hotel.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['admin'],
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Front Desk Officer',
            'email' => 'frontdesk@hotel.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['front_desk'],
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Operations Supervisor',
            'email' => 'supervisor@hotel.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['supervisor'],
            'is_active' => true,
        ]);
    }
}