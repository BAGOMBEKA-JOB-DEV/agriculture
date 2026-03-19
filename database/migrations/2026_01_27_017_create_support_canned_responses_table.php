<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_canned_responses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('shortcut')->unique()->nullable();
            $table->text('content');
            $table->string('category')->nullable();

            $table->boolean('is_public')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('support_departments')->onDelete('set null');

            $table->integer('usage_count')->default(0);
            $table->timestamps();

            $table->index(['is_public', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_canned_responses');
    }
};
