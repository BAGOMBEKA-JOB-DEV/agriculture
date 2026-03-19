<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('message');

            // Message properties
            $table->boolean('is_internal')->default(false); // Private notes for agents
            $table->enum('message_type', ['reply', 'note', 'system'])->default('reply');
            $table->enum('channel', ['email', 'chat', 'phone', 'web', 'api'])->default('web');

            $table->boolean('is_draft')->default(false);
            $table->json('attachments')->nullable(); // Array of attachment IDs

            $table->timestamps();
            $table->softDeletes();

            $table->index(['ticket_id', 'created_at']);
            $table->index(['user_id', 'is_internal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_messages');
    }
};
