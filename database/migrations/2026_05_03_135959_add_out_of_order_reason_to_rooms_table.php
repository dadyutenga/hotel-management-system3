<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('out_of_order_reason')->nullable()->after('cleaning_confirmed_at');
            $table->uuid('out_of_order_set_by')->nullable()->after('out_of_order_reason');
            $table->timestamp('out_of_order_set_at')->nullable()->after('out_of_order_set_by');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['out_of_order_reason', 'out_of_order_set_by', 'out_of_order_set_at']);
        });
    }
};
