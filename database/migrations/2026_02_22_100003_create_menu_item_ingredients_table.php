<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_item_ingredients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('menu_item_id');
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 4);
            $table->string('unit', 30);
            $table->timestamps();

            $table->foreign('menu_item_id')->references('id')->on('menu_items')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products');
            $table->unique(['menu_item_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_ingredients');
    }
};
