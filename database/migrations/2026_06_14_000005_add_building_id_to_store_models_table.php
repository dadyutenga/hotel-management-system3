<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'products',
            'stock_levels',
            'stock_movements',
            'stock_adjustments',
            'stock_adjustment_items',
            'stock_transfers',
            'internal_usage_requests',
            'internal_usage_request_items',
            'stock_receivings',
            'stock_receiving_items',
            'stock_takes',
            'stock_take_items',
            'beverages',
            'beverage_categories',
            'beverage_inventory',
            'beverage_stock_movements',
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
            'products',
            'stock_levels',
            'stock_movements',
            'stock_adjustments',
            'stock_adjustment_items',
            'stock_transfers',
            'internal_usage_requests',
            'internal_usage_request_items',
            'stock_receivings',
            'stock_receiving_items',
            'stock_takes',
            'stock_take_items',
            'beverages',
            'beverage_categories',
            'beverage_inventory',
            'beverage_stock_movements',
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
