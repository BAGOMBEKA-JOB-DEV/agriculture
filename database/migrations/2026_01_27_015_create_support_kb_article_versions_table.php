<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_kb_article_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('support_kb_articles')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->integer('version_number');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('change_note')->nullable();
            $table->timestamps();

            $table->index('article_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_kb_article_versions');
    }
};
