<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PostgreSQL, we need to drop the constraint and change the column type
        if (DB::getDriverName() === 'pgsql') {
            // Drop the check constraint
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            
            // Change column to string
            DB::statement('ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(255)');
        } else {
            // For MySQL and other databases
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 255)->default('user')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting would recreate the enum, but since we're moving to dynamic roles,
        // we'll just keep it as string even on rollback
        // If you really need to revert, you'd need to recreate the enum constraint manually
    }
};
