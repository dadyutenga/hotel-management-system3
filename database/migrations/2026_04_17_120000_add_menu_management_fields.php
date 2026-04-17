<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_categories', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('name');
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('service_location_tag', 50)->nullable()->after('is_available');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('item_name_snapshot', 150)->nullable()->after('menu_item_id');
            $table->decimal('base_unit_price', 10, 2)->default(0)->after('quantity');
            $table->decimal('options_unit_price', 10, 2)->default(0)->after('base_unit_price');
            $table->json('selected_options_snapshot')->nullable()->after('options_unit_price');
            $table->string('options_signature', 64)->default('none')->after('selected_options_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'item_name_snapshot',
                'base_unit_price',
                'options_unit_price',
                'selected_options_snapshot',
                'options_signature',
            ]);
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('service_location_tag');
        });

        Schema::table('menu_categories', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};

