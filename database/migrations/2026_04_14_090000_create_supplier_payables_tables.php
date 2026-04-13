<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_payables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_id');
            $table->string('reference', 100);
            $table->date('payable_date');
            $table->enum('currency', ['USD', 'TZS'])->default('USD');
            $table->decimal('amount_total', 14, 2)->default(0);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('balance', 14, 2)->default(0);
            $table->enum('status', ['unpaid', 'partial', 'paid', 'cancelled'])->default('unpaid');
            $table->string('source_module', 50)->default('procurement');
            $table->string('source_reference_type', 50)->nullable();
            $table->uuid('source_reference_id')->nullable();
            $table->uuid('journal_entry_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['source_module', 'source_reference_id'], 'supplier_payables_source_unique');
            $table->index(['supplier_id', 'status']);
        });

        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_id');
            $table->date('payment_date');
            $table->enum('currency', ['USD', 'TZS'])->default('USD');
            $table->decimal('amount', 14, 2);
            $table->enum('method', ['cash', 'bank', 'mobile', 'card']);
            $table->string('reference', 100)->nullable();
            $table->enum('status', ['draft', 'pending_approval', 'posted', 'cancelled'])->default('draft');
            $table->uuid('journal_entry_id')->nullable();
            $table->uuid('financial_transaction_id')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->nullOnDelete();
            $table->foreign('financial_transaction_id')->references('id')->on('financial_transactions')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('posted_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['supplier_id', 'status']);
        });

        Schema::create('supplier_payment_allocations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('supplier_payment_id');
            $table->uuid('supplier_payable_id');
            $table->decimal('allocated_amount', 14, 2);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('supplier_payment_id')->references('id')->on('supplier_payments')->cascadeOnDelete();
            $table->foreign('supplier_payable_id')->references('id')->on('supplier_payables')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['supplier_payment_id', 'supplier_payable_id'], 'supplier_payment_allocations_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payment_allocations');
        Schema::dropIfExists('supplier_payments');
        Schema::dropIfExists('supplier_payables');
    }
};
