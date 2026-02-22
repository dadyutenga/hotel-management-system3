<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id');
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('selling_price', 10, 2);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('menu_categories');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
