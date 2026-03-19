<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('support_ticket_categories')->onDelete('cascade');
            $table->string('icon')->nullable();
            $table->string('color')->default('#6B7280');
            $table->boolean('is_active')->default(true);
            $table->foreignId('auto_assign_to_department_id')->nullable()->constrained('support_departments')->onDelete('set null');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_categories');
    }
};
