<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder {
    public function run(): void {
        DB::table('roles')->insert([
            ['name' => 'admin', 'display_name' => 'Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'front_desk', 'display_name' => 'Front Desk', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'supervisor', 'display_name' => 'Supervisor', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}