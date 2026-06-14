<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            // SQLite cannot drop columns that are part of an index, so drop the index first
            $table->dropIndex('receipts_receiptable_type_receiptable_id_index');
            // Drop the old bigint morph columns
            $table->dropColumn(['receiptable_type', 'receiptable_id']);
        });

        Schema::table('receipts', function (Blueprint $table) {
            // Recreate with UUID support
            $table->string('receiptable_type')->nullable()->after('module');
            $table->uuid('receiptable_id')->nullable()->after('receiptable_type');
            $table->index(['receiptable_type', 'receiptable_id'], 'receipts_receiptable_index');
        });
    }

    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropIndex('receipts_receiptable_index');
            $table->dropColumn(['receiptable_type', 'receiptable_id']);
        });

        Schema::table('receipts', function (Blueprint $table) {
            $table->nullableMorphs('receiptable')->after('module');
        });
    }
};
