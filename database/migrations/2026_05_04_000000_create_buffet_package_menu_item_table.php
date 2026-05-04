<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buffet_package_menu_item', function (Blueprint $table) {
            $table->uuid('buffet_package_id');
            $table->uuid('menu_item_id');
            $table->primary(['buffet_package_id', 'menu_item_id']);

            $table->foreign('buffet_package_id')->references('id')->on('buffet_packages')->onDelete('cascade');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buffet_package_menu_item');
    }
};
