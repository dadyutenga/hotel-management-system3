<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_locations', function (Blueprint $table) {
            $table->uuid('building_id')->nullable()->after('id');
            $table->foreign('building_id')->references('id')->on('buildings')->nullOnDelete();
            $table->index('building_id');
            $table->dropUnique(['code']);
            $table->unique(['building_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::table('stock_locations', function (Blueprint $table) {
            $table->dropForeign(['building_id']);
            $table->dropIndex(['building_id']);
            $table->dropUnique(['building_id', 'code']);
            $table->unique(['code']);
            $table->dropColumn('building_id');
        });
    }
};
