<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->uuid('approved_by')->nullable()->after('confirmed_at');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->uuid('rejected_by')->nullable()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');

            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('rejected_by')->references('id')->on('users')->nullOnDelete();
        });

        DB::table('goods_received_notes')
            ->where('status', 'pending_confirmation')
            ->update(['status' => 'submitted']);

        DB::table('goods_received_notes')
            ->where('status', 'confirmed')
            ->update([
                'status' => 'approved',
                'approved_by' => DB::raw('COALESCE(approved_by, confirmed_by)'),
                'approved_at' => DB::raw('COALESCE(approved_at, confirmed_at)'),
            ]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE goods_received_notes MODIFY status ENUM('draft','submitted','confirmed_by_storekeeper','pending_manager_approval','approved','rejected') NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        DB::table('goods_received_notes')
            ->where('status', 'submitted')
            ->update(['status' => 'pending_confirmation']);

        DB::table('goods_received_notes')
            ->whereIn('status', ['approved', 'confirmed_by_storekeeper', 'pending_manager_approval'])
            ->update(['status' => 'confirmed']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE goods_received_notes MODIFY status ENUM('draft','pending_confirmation','confirmed','rejected') NOT NULL DEFAULT 'draft'");
        }

        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn(['approved_by', 'approved_at', 'rejected_by', 'rejected_at']);
        });
    }
};

