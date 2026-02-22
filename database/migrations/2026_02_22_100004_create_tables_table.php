<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('location_id');
            $table->string('table_number', 20);
            $table->integer('capacity')->default(4);
            $table->enum('status', [
                'available',
                'occupied',
                'reserved',
                'cleaning',
            ])->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('stock_locations');
            $table->unique(['location_id', 'table_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
