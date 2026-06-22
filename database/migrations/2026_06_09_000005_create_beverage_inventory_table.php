<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beverage_inventory', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('beverage_id')->unique();
            $table->foreign('beverage_id')->references('id')->on('beverages')->cascadeOnDelete();
            $table->integer('quantity_on_hand')->default(0);
            $table->timestamp('last_updated')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beverage_inventory');
    }
};
