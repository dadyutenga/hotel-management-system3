<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_number', 30)->unique();

            $table->enum('type', [
                'checkout_payment',
                'walkin_sale',
                'advance_payment',
                'refund',
                'adjustment',
            ]);

            $table->enum('source_module', [
                'restaurant',
                'bar',
                'store',
                'laundry',
                'accommodation',
                'other',
            ]);

            $table->uuid('payment_id')->nullable();
            $table->uuid('booking_id')->nullable();
            $table->uuid('order_id')->nullable();

            $table->enum('currency', ['USD', 'TZS']);
            $table->decimal('amount', 14, 2);
            $table->decimal('amount_usd', 12, 2);
            $table->decimal('exchange_rate', 12, 4)->default(1);

            $table->enum('payment_method', [
                'cash', 'card', 'mobile_money', 'bank_transfer',
            ]);

            $table->text('description');
            $table->uuid('created_by');
            $table->timestamp('created_at');

            $table->foreign('payment_id')->references('id')->on('finance_payments')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');

            $table->index(['type', 'created_at']);
            $table->index(['source_module', 'created_at']);
            $table->index(['booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
