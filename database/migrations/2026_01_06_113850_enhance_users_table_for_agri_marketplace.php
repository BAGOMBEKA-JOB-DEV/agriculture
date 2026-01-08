<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'super_admin', 'vendor', 'buyer', 'trader', 'user'])->default('user')->after('email');
            $table->enum('user_type', ['farmer', 'trader', 'company', 'buyer'])->nullable()->after('role');
            $table->string('phone')->nullable()->after('email');
            $table->string('location')->nullable()->after('phone');
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending')->after('location');
            $table->timestamp('verified_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'user_type', 'phone', 'location', 'status', 'verified_at']);
        });
    }
};
