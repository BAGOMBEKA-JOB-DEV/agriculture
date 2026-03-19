<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_sla_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            // Conditions for applying this SLA
            $table->enum('priority_level', ['low', 'normal', 'high', 'urgent', 'critical'])->nullable();
            $table->enum('customer_tier', ['standard', 'premium', 'vip'])->nullable();
            $table->foreignId('department_id')->nullable()->constrained('support_departments')->onDelete('set null');

            // SLA targets (in minutes)
            $table->integer('response_time_minutes');
            $table->integer('resolution_time_minutes');

            // Business rules
            $table->boolean('business_hours_only')->default(false);
            $table->foreignId('business_hours_id')->nullable()->constrained('support_business_hours')->onDelete('set null');
            $table->boolean('pause_on_pending')->default(true);
            $table->boolean('pause_on_customer_reply_wait')->default(true);

            $table->timestamps();

            $table->index(['is_active', 'priority_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_sla_policies');
    }
};
