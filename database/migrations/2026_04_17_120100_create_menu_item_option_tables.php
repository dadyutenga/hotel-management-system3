<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_option_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 120);
            $table->enum('selection_type', ['single', 'multiple'])->default('single');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('menu_option_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('menu_option_group_id');
            $table->string('label', 120);
            $table->decimal('price_delta', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('menu_option_group_id')
                ->references('id')
                ->on('menu_option_groups')
                ->cascadeOnDelete();
        });

        Schema::create('menu_item_option_group', function (Blueprint $table) {
            $table->uuid('menu_item_id');
            $table->uuid('menu_option_group_id');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->primary(['menu_item_id', 'menu_option_group_id']);

            $table->foreign('menu_item_id')
                ->references('id')
                ->on('menu_items')
                ->cascadeOnDelete();
            $table->foreign('menu_option_group_id')
                ->references('id')
                ->on('menu_option_groups')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_option_group');
        Schema::dropIfExists('menu_option_values');
        Schema::dropIfExists('menu_option_groups');
    }
};

