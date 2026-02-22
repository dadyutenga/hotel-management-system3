<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('location_id');
            $table->decimal('previous_qty', 10, 3);
            $table->decimal('new_qty', 10, 3);
            $table->decimal('difference', 10, 3)->storedAs('new_qty - previous_qty');
            $table->text('reason');
            $table->boolean('requires_approval')->default(false);
            $table->enum('status', ['pending', 'approved', 'rejected', 'applied']);
            $table->uuid('approved_by')->nullable();
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('location_id')->references('id')->on('stock_locations')->restrictOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
