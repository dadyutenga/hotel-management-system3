<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key'         => 'adjustment_approval_threshold',
                'value'       => '50',
                'description' => 'Adjustments above this unit count require STORE_MANAGER approval',
            ],
            [
                'key'         => 'low_stock_alert_enabled',
                'value'       => 'true',
                'description' => 'Toggle low stock notifications on/off',
            ],
        ];

        foreach ($settings as $s) {
            DB::table('system_settings')->updateOrInsert(['key' => $s['key']], $s);
        }
    }
}
