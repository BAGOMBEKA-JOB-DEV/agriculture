<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_ticket_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->default('#6B7280');
            $table->timestamps();
        });

        // Pivot table for many-to-many relationship
        Schema::create('support_ticket_tag_pivot', function (Blueprint $table) {
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('support_ticket_tags')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['ticket_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_tag_pivot');
        Schema::dropIfExists('support_ticket_tags');
    }
};
