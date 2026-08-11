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
        Schema::table('booking_requests', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->after('vendor_id');
            $table->decimal('total_amount', 10, 2)->default(0)->after('check_out');
            $table->decimal('subtotal', 10, 2)->default(0)->after('total_amount');
            $table->decimal('discount', 10, 2)->default(0)->after('subtotal');
            $table->decimal('gst', 10, 2)->default(0)->after('discount');
            $table->unsignedTinyInteger('children')->default(0)->after('adults');
            $table->foreignId('room_type_id')->nullable()->constrained()->onDelete('set null')->after('children');
            $table->string('coupon_code')->nullable()->after('room_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('booking_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['room_type_id']);
            $table->dropColumn(['user_id', 'total_amount', 'subtotal', 'discount', 'gst', 'children', 'room_type_id', 'coupon_code']);
        });
    }
};
