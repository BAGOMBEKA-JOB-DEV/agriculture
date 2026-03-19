<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // e.g., TKT-2024-00001
            $table->string('subject');
            $table->text('description');

            // Party relationships
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained('support_departments')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('support_ticket_categories')->onDelete('set null');

            // Ticket properties
            $table->enum('priority', ['low', 'normal', 'high', 'urgent', 'critical'])->default('normal');
            $table->enum('status', ['new', 'open', 'pending', 'on_hold', 'resolved', 'closed'])->default('new');
            $table->enum('source', ['email', 'chat', 'phone', 'web', 'social', 'whatsapp', 'api'])->default('web');

            // Multi-brand support
            $table->foreignId('brand_id')->nullable()->constrained('support_brands')->onDelete('set null');

            // Linked tickets
            $table->foreignId('parent_ticket_id')->nullable()->constrained('support_tickets')->onDelete('set null');
            $table->foreignId('merged_into_ticket_id')->nullable()->constrained('support_tickets')->onDelete('set null');

            // SLA
            $table->foreignId('sla_policy_id')->nullable()->constrained('support_sla_policies')->onDelete('set null');

            // Timestamps
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('due_date')->nullable();

            // Metadata
            $table->json('tags')->nullable();
            $table->json('custom_fields')->nullable();
            $table->boolean('is_spam')->default(false);
            $table->decimal('spam_score', 3, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Performance indexes
            $table->index(['customer_id', 'status']);
            $table->index(['assigned_agent_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['priority', 'created_at']);
            $table->index('ticket_number');
            $table->index(['department_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
