<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('checkouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id')->unique();
            $table->string('receipt_number', 30)->unique();
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'cancelled',
            ])->default('pending');

            // Totals
            $table->decimal('total_charges_usd', 12, 2)->default(0);
            $table->decimal('discount_usd', 12, 2)->default(0);
            $table->decimal('grand_total_usd', 12, 2)->default(0);
            $table->decimal('exchange_rate', 12, 4)->default(1);
            $table->decimal('grand_total_tzs', 14, 2)->default(0);

            // Payment split
            $table->decimal('paid_cash_usd', 12, 2)->default(0);
            $table->decimal('paid_card_usd', 12, 2)->default(0);
            $table->decimal('paid_cash_tzs', 14, 2)->default(0);
            $table->decimal('paid_card_tzs', 14, 2)->default(0);
            $table->decimal('total_paid_usd', 12, 2)->default(0);
            $table->decimal('change_due_usd', 12, 2)->default(0);

            $table->enum('payment_method', [
                'cash_usd',
                'cash_tzs',
                'card_usd',
                'card_tzs',
                'split',
            ])->nullable();

            $table->text('notes')->nullable();
            $table->uuid('initiated_by');
            $table->uuid('completed_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings');
            $table->foreign('initiated_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
};
