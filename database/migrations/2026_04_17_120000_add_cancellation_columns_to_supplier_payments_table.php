<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('supplier_payments', 'cancelled_by')) {
                $table->uuid('cancelled_by')->nullable()->after('posted_at');
                $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('supplier_payments', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_payments', 'cancelled_by')) {
                $table->dropForeign(['cancelled_by']);
                $table->dropColumn('cancelled_by');
            }

            if (Schema::hasColumn('supplier_payments', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
        });
    }
};
