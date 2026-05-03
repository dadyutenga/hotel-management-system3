<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->uuid('cleaning_assigned_to')->nullable()->after('status');
            $table->timestamp('cleaning_assigned_at')->nullable()->after('cleaning_assigned_to');
            $table->timestamp('cleaning_completed_at')->nullable()->after('cleaning_assigned_at');
            $table->uuid('cleaning_confirmed_by')->nullable()->after('cleaning_completed_at');
            $table->timestamp('cleaning_confirmed_at')->nullable()->after('cleaning_confirmed_by');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'cleaning_assigned_to',
                'cleaning_assigned_at',
                'cleaning_completed_at',
                'cleaning_confirmed_by',
                'cleaning_confirmed_at',
            ]);
        });
    }
};
