<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number', 30)->unique();
            $table->uuid('location_id');
            $table->uuid('table_id')->nullable();
            $table->enum('order_type', [
                'guest',
                'walkin',
            ]);
            $table->uuid('booking_id')->nullable();
            $table->string('customer_name', 150)->nullable();
            $table->enum('status', [
                'open',
                'sent',
                'ready',
                'served',
                'settled',
                'cancelled',
            ])->default('open');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('payment_method', [
                'cash',
                'card',
                'charge_to_booking',
            ])->nullable();
            $table->text('notes')->nullable();
            $table->uuid('created_by');
            $table->uuid('settled_by')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('stock_locations');
            $table->foreign('table_id')->references('id')->on('tables')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
