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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->text('content')->nullable();
            $table->string('image_url')->nullable();
            $table->enum('type', ['post', 'repost'])->default('post');
            $table->foreignId('original_post_id')->nullable()->constrained('posts')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('post_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['like', 'love', 'celebrate', 'funny', 'insightful', 'support'])->default('like');
            $table->timestamps();

            $table->unique(['post_id', 'user_id']); // One reaction per user per post
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_reactions');
        Schema::dropIfExists('posts');
    }
};
