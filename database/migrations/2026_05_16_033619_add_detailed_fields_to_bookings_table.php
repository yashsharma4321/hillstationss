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
            $table->integer('adults')->default(1)->after('guest_count');
            $table->integer('children')->default(0)->after('adults');
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null')->after('property_id');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('total_amount');
            $table->decimal('gst_amount', 12, 2)->default(0)->after('discount_amount');
            $table->decimal('commission_amount', 12, 2)->default(0)->after('gst_amount');
            $table->decimal('vendor_amount', 12, 2)->default(0)->after('commission_amount');
            $table->integer('commission_percentage')->default(0)->after('vendor_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['adults', 'children', 'coupon_id', 'discount_amount', 'gst_amount', 'commission_amount', 'vendor_amount', 'commission_percentage']);
        });
    }
};
