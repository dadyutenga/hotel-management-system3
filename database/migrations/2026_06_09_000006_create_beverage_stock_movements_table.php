<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beverage_stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('beverage_id');
            $table->foreign('beverage_id')->references('id')->on('beverages')->restrictOnDelete();
            $table->string('movement_type', 20);
            $table->integer('quantity');
            $table->string('reference_type', 100)->nullable();
            $table->uuid('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->uuid('performed_by')->nullable();
            $table->foreign('performed_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beverage_stock_movements');
    }
};
