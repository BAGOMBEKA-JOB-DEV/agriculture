<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rfqs', function (Blueprint $table) {
            $table->decimal('final_price', 15, 2)->nullable()->after('status')->comment('Actual agreed price for GMV reports');
            $table->timestamp('accepted_at')->nullable()->after('final_price')->comment('Timestamp when RFQ was fully accepted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rfqs', function (Blueprint $table) {
            $table->dropColumn(['final_price', 'accepted_at']);
        });
    }
};
