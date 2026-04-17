<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buffet_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 120);
            $table->decimal('adult_price', 12, 2);
            $table->decimal('child_price', 12, 2)->default(0);
            $table->json('available_days')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('buffet_sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sale_number', 30)->unique();
            $table->uuid('buffet_package_id');
            $table->uuid('booking_id')->nullable();
            $table->enum('sale_type', ['walkin', 'booking']);
            $table->unsignedInteger('adults_count')->default(0);
            $table->unsignedInteger('children_count')->default(0);
            $table->string('package_name_snapshot', 120);
            $table->decimal('adult_price_snapshot', 12, 2);
            $table->decimal('child_price_snapshot', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['pending', 'charged', 'settled', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->uuid('served_by')->nullable();
            $table->uuid('settled_by')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->index(['sale_type', 'status']);
            $table->index('booking_id');
            $table->foreign('buffet_package_id')->references('id')->on('buffet_packages');
            $table->foreign('booking_id')->references('id')->on('bookings')->nullOnDelete();
            $table->foreign('served_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('settled_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buffet_sales');
        Schema::dropIfExists('buffet_packages');
    }
};

