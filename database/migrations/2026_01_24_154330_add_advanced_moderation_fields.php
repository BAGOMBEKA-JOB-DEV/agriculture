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
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_pinned')->default(false)->after('status');
        });

        Schema::table('post_comments', function (Blueprint $table) {
            $table->enum('status', ['published', 'hidden'])->default('published')->after('content');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('is_pinned');
        });

        Schema::table('post_comments', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropSoftDeletes();
        });
    }
};
