<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_take_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('stock_take_id');
            $table->foreign('stock_take_id')->references('id')->on('stock_takes')->cascadeOnDelete();
            $table->uuid('beverage_id');
            $table->foreign('beverage_id')->references('id')->on('beverages')->restrictOnDelete();
            $table->string('barcode_scanned', 100);
            $table->integer('expected_quantity')->default(0);
            $table->integer('physical_count')->default(0);
            $table->integer('variance')->default(0);
            $table->timestamps();
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_take_items');
    }
};
