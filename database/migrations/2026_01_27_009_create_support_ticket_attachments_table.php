<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->nullable()->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('message_id')->nullable()->constrained('support_ticket_messages')->onDelete('cascade');

            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path');
            $table->bigInteger('file_size'); // In bytes
            $table->string('mime_type');

            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_scanned')->default(false); // Virus scan status

            $table->timestamps();

            $table->index('ticket_id');
            $table->index('message_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_attachments');
    }
};
