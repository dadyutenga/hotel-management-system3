<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_number', 30)->unique();
            $table->enum('payment_type', [
                'checkout',
                'walkin',
                'advance',
            ]);
            $table->uuid('checkout_id')->nullable();
            $table->uuid('order_id')->nullable();
            $table->uuid('booking_id')->nullable();

            $table->enum('currency', ['USD', 'TZS']);
            $table->decimal('amount', 14, 2);
            $table->decimal('amount_usd', 12, 2);
            $table->decimal('exchange_rate', 12, 4)->default(1);

            $table->enum('method', [
                'cash',
                'card',
                'mobile_money',
                'bank_transfer',
            ]);

            $table->enum('status', [
                'pending',
                'completed',
                'failed',
                'refunded',
            ])->default('pending');

            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->uuid('created_by');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('checkout_id')->references('id')->on('checkouts')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');

            $table->index(['payment_type', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_payments');
    }
};
