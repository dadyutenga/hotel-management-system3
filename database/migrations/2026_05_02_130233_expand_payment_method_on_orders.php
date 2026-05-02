<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite enum CHECK constraint only allowed ['cash','card','charge_to_booking'].
        // POS now sends 'mobile' / 'mobile_money' too. Drop and recreate as plain string.

        $driver = DB::connection()->getDriverName();

        Schema::table('orders', function (Blueprint $table) {
            // Save existing data into a temp column
            $table->string('payment_method_new', 50)->nullable()->after('total');
        });

        DB::statement('UPDATE orders SET payment_method_new = payment_method');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('payment_method_new', 'payment_method');
        });
    }

    public function down(): void
    {
        // Cannot restore the enum CHECK constraint — just keep it as string.
    }
};
