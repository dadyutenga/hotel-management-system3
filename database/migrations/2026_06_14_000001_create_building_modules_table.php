<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('building_modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('building_id');
            $table->string('type', 20); // restaurant | bar
            $table->string('name', 150);
            $table->string('code', 50)->nullable();
            $table->string('status', 20)->default('active'); // active | inactive
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('building_id')->references('id')->on('buildings')->cascadeOnDelete();
            $table->index(['building_id', 'type', 'status']);
            $table->unique(['building_id', 'type', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('building_modules');
    }
};
