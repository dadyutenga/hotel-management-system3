<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_charges', function (Blueprint $table) {
            $table->uuid('order_id')->nullable()->after('booking_id');
            $table->uuid('created_by')->nullable()->after('status');

            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('booking_charges', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['order_id', 'created_by']);
        });
    }
};
