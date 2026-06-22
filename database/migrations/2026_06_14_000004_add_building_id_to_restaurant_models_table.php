<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'menu_categories',
            'menu_items',
            'menu_option_groups',
            'menu_option_values',
            'menu_item_option_group',
            'tables',
            'orders',
            'kitchen_tickets',
            'bar_tickets',
            'buffet_packages',
            'buffet_sales',
            'buffet_package_menu_item',
            'kitchen_stock_items',
            'kitchen_stock_movements',
            'bar_damage_reports',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (! Schema::hasColumn($tableName, 'building_id')) {
                        $table->uuid('building_id')->nullable()->after('id');
                        $table->index('building_id');
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'menu_categories',
            'menu_items',
            'menu_option_groups',
            'menu_option_values',
            'menu_item_option_group',
            'tables',
            'orders',
            'kitchen_tickets',
            'bar_tickets',
            'buffet_packages',
            'buffet_sales',
            'buffet_package_menu_item',
            'kitchen_stock_items',
            'kitchen_stock_movements',
            'bar_damage_reports',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'building_id')) {
                        $table->dropIndex(['building_id']);
                        $table->dropColumn('building_id');
                    }
                });
            }
        }
    }
};
