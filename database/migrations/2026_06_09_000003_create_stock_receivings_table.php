<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_receivings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('receiving_code', 50)->unique();
            $table->string('status', 20)->default('draft');
            $table->uuid('received_by');
            $table->foreign('received_by')->references('id')->on('users')->restrictOnDelete();
            $table->uuid('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('received_at');
            $table->timestamps();
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_receivings');
    }
};
