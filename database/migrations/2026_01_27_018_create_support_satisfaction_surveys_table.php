<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_satisfaction_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');

            $table->integer('rating')->nullable(); // 1-5 stars
            $table->integer('nps_score')->nullable(); // 0-10
            $table->text('feedback_text')->nullable();

            $table->string('survey_type')->default('csat'); // csat, nps, ces
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['ticket_id', 'survey_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_satisfaction_surveys');
    }
};
