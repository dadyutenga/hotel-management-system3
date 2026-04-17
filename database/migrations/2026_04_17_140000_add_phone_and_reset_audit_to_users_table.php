<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email')->index();
            $table->timestamp('password_reset_requested_at')->nullable()->after('remember_token');
            $table->timestamp('password_reset_completed_at')->nullable()->after('password_reset_requested_at');
            $table->string('password_reset_phone', 20)->nullable()->after('password_reset_completed_at');
            $table->boolean('must_change_password')->default(false)->after('password_reset_phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropColumn([
                'phone',
                'password_reset_requested_at',
                'password_reset_completed_at',
                'password_reset_phone',
                'must_change_password',
            ]);
        });
    }
};

