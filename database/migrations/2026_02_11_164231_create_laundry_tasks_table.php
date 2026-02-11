<?php
// database/migrations/2026_02_12_000001_create_laundry_tasks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('task_number')->unique();
            $table->foreignUuid('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            
            // Denormalized for quick access
            $table->string('guest_name');
            $table->string('room_number');
            
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'returned'])->default('pending');
            
            // Billing
            $table->boolean('is_amenity')->default(false);
            $table->decimal('cost', 10, 2)->default(0);
            
            // Timestamps for workflow
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('assigned_to');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_tasks');
    }
};