<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('supplier_payments') || ! Schema::hasColumn('supplier_payments', 'reference')) {
            return;
        }

        $duplicateReferences = DB::table('supplier_payments')
            ->select('reference')
            ->whereNotNull('reference')
            ->where('reference', '!=', '')
            ->groupBy('reference')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('reference');

        foreach ($duplicateReferences as $reference) {
            $ids = DB::table('supplier_payments')
                ->where('reference', $reference)
                ->orderBy('created_at')
                ->orderBy('id')
                ->pluck('id')
                ->values();

            foreach ($ids->slice(1)->values() as $index => $id) {
                $suffix = strtoupper(substr(str_replace('-', '', (string) $id), 0, 8));
                $base = substr((string) $reference, 0, 90);
                $candidate = $base . '-' . $suffix;

                while (DB::table('supplier_payments')->where('reference', $candidate)->exists()) {
                    $candidate = substr((string) $reference, 0, 86) . '-' . $suffix . ($index + 2);
                }

                DB::table('supplier_payments')
                    ->where('id', $id)
                    ->update(['reference' => substr($candidate, 0, 100)]);
            }
        }

        try {
            Schema::table('supplier_payments', function (Blueprint $table): void {
                $table->unique('reference', 'supplier_payments_reference_unique');
            });
        } catch (\Throwable) {
            // Index may already exist in environments where this migration was run manually.
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('supplier_payments')) {
            return;
        }

        try {
            Schema::table('supplier_payments', function (Blueprint $table): void {
                $table->dropUnique('supplier_payments_reference_unique');
            });
        } catch (\Throwable) {
            // Ignore when index is absent.
        }
    }
};
