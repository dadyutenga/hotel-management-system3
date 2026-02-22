<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_usage_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('department', 100);
            $table->uuid('product_id');
            $table->decimal('quantity', 10, 3);
            $table->enum('status', ['pending', 'approved', 'rejected', 'fulfilled', 'cancelled']);
            $table->text('reason')->nullable();
            $table->uuid('requested_by');
            $table->uuid('approved_by')->nullable();
            $table->uuid('fulfilled_by')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('requested_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('fulfilled_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_usage_requests');
    }
};
