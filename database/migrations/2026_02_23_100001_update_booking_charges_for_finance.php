<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_charges', function (Blueprint $table) {
            // Add source column if not already present
            if (!Schema::hasColumn('booking_charges', 'source')) {
                $table->string('source', 50)->default('hotel')->after('charge_type');
            }
            // Currency support
            if (!Schema::hasColumn('booking_charges', 'currency')) {
                $table->enum('currency', ['USD', 'TZS'])->default('USD')->after('amount');
            }
            if (!Schema::hasColumn('booking_charges', 'amount_tzs')) {
                $table->decimal('amount_tzs', 12, 2)->default(0)->after('currency');
            }
            // Checkout FK — set when guest pays at checkout
            if (!Schema::hasColumn('booking_charges', 'checkout_id')) {
                $table->uuid('checkout_id')->nullable()->after('booking_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_charges', function (Blueprint $table) {
            $columns = [];
            foreach (['source', 'currency', 'amount_tzs', 'checkout_id'] as $col) {
                if (Schema::hasColumn('booking_charges', $col)) {
                    $columns[] = $col;
                }
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
