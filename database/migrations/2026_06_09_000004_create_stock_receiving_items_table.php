<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_receiving_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('receiving_id');
            $table->foreign('receiving_id')->references('id')->on('stock_receivings')->cascadeOnDelete();
            $table->uuid('beverage_id');
            $table->foreign('beverage_id')->references('id')->on('beverages')->restrictOnDelete();
            $table->string('barcode_scanned', 100);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_buying_price', 10, 2)->default(0);
            $table->timestamps();
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_receiving_items');
    }
};
