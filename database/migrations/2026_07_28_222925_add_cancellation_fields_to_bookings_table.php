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
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancellation_date')->nullable();
            $table->decimal('deduction_percentage', 5, 2)->nullable();
            $table->decimal('deduction_amount', 10, 2)->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('refund_id')->nullable();
            $table->string('refund_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'cancellation_reason',
                'cancellation_date',
                'deduction_percentage',
                'deduction_amount',
                'refund_amount',
                'refund_id',
                'refund_status'
            ]);
        });
    }
};
