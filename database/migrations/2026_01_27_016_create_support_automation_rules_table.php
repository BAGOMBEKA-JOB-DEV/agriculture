<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_automation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();

            // Trigger: ticket_created, status_updated, etc.
            $table->string('trigger_type');

            // Conditions: JSON object with criteria
            $table->json('conditions');

            // Actions: JSON object with things to do
            $table->json('actions');

            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('run_count')->default(0);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();

            $table->index(['trigger_type', 'is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_automation_rules');
    }
};
