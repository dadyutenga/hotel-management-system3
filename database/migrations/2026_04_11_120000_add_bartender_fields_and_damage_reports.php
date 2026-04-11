<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_source')) {
                $table->string('order_source', 30)->nullable()->after('order_type');
            }

            if (!Schema::hasColumn('orders', 'bartender_status')) {
                $table->string('bartender_status', 30)->nullable()->after('status');
            }

            if (!Schema::hasColumn('orders', 'bartender_status_updated_at')) {
                $table->timestamp('bartender_status_updated_at')->nullable()->after('bartender_status');
            }

            if (!Schema::hasColumn('orders', 'stock_deducted_at')) {
                $table->timestamp('stock_deducted_at')->nullable()->after('bartender_status_updated_at');
            }

            if (!Schema::hasColumn('orders', 'stock_reversed_at')) {
                $table->timestamp('stock_reversed_at')->nullable()->after('stock_deducted_at');
            }

            if (!Schema::hasColumn('orders', 'billed_to_folio_at')) {
                $table->timestamp('billed_to_folio_at')->nullable()->after('stock_reversed_at');
            }

            if (!Schema::hasColumn('orders', 'billing_error')) {
                $table->text('billing_error')->nullable()->after('billed_to_folio_at');
            }
        });

        Schema::create('bar_damage_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('location_id');
            $table->decimal('quantity', 10, 3);
            $table->string('reason', 40);
            $table->text('notes')->nullable();
            $table->uuid('reported_by');
            $table->timestamp('reported_at');
            $table->uuid('stock_movement_id');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('location_id')->references('id')->on('stock_locations');
            $table->foreign('reported_by')->references('id')->on('users');
            $table->foreign('stock_movement_id')->references('id')->on('stock_movements');

            $table->index(['location_id', 'reported_at']);
            $table->index(['reported_by', 'reported_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bar_damage_reports');

        Schema::table('orders', function (Blueprint $table) {
            $drop = [];

            foreach ([
                'order_source',
                'bartender_status',
                'bartender_status_updated_at',
                'stock_deducted_at',
                'stock_reversed_at',
                'billed_to_folio_at',
                'billing_error',
            ] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $drop[] = $column;
                }
            }

            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
