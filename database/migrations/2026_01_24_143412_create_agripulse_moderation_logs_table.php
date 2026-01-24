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
        Schema::create('agripulse_moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_id')->constrained('users');
            $table->string('action'); // 'hide_post', 'delete_post', 'restore_post', etc.
            $table->morphs('subject'); // Post, Comment, User, etc.
            $table->json('metadata')->nullable(); // Store additional context
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agripulse_moderation_logs');
    }
};
