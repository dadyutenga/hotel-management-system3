<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('passkey')->nullable()->after('password');
            $table->boolean('passkey_enabled')->default(false)->after('passkey');
            $table->string('login_type', 20)->default('full')->after('passkey_enabled');
            $table->string('property_code', 50)->nullable()->after('login_type');
            $table->tinyInteger('failed_passkey_attempts')->default(0)->after('property_code');
            $table->timestamp('passkey_locked_until')->nullable()->after('failed_passkey_attempts');
            $table->timestamp('last_passkey_login')->nullable()->after('passkey_locked_until');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'passkey',
                'passkey_enabled',
                'login_type',
                'property_code',
                'failed_passkey_attempts',
                'passkey_locked_until',
                'last_passkey_login',
            ]);
        });
    }
};
