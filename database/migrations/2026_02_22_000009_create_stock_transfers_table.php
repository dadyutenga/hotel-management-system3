<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('from_location_id');
            $table->uuid('to_location_id');
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 3);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled']);
            $table->uuid('requested_by');
            $table->uuid('approved_by')->nullable();
            $table->uuid('fulfilled_by')->nullable();
            $table->text('reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('from_location_id')->references('id')->on('stock_locations')->restrictOnDelete();
            $table->foreign('to_location_id')->references('id')->on('stock_locations')->restrictOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('requested_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('fulfilled_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
