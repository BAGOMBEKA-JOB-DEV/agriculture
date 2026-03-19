<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->foreignId('business_hours_id')->constrained('support_business_hours')->onDelete('cascade');
            $table->boolean('is_recurring')->default(false); // Yearly recurring
            $table->timestamps();

            $table->index(['business_hours_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_holidays');
    }
};
