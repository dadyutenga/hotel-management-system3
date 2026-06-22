<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Seed new roles
        DB::table('roles')->insert([
            [
                'id' => (string) Str::uuid(),
                'name' => 'pos_bar',
                'description' => 'POS bar — settle and process bar orders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'pos_kitchen',
                'description' => 'POS kitchen — settle and process kitchen/restaurant orders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Migrate existing cashier users to pos_kitchen (default)
        $posKitchenRole = DB::table('roles')->where('name', 'pos_kitchen')->first();
        $cashierRole = DB::table('roles')->where('name', 'cashier')->first();

        if ($posKitchenRole && $cashierRole) {
            DB::table('users')
                ->where('role_id', $cashierRole->id)
                ->update(['role_id' => $posKitchenRole->id]);
        }
    }

    public function down(): void
    {
        // Move pos_kitchen users back to cashier
        $posKitchenRole = DB::table('roles')->where('name', 'pos_kitchen')->first();
        $cashierRole = DB::table('roles')->where('name', 'cashier')->first();

        if ($posKitchenRole && $cashierRole) {
            DB::table('users')
                ->where('role_id', $posKitchenRole->id)
                ->update(['role_id' => $cashierRole->id]);
        }

        // Remove new roles
        DB::table('roles')->whereIn('name', ['pos_bar', 'pos_kitchen'])->delete();
    }
};
