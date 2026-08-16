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
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('pets_allowed')->default(false)->after('meals');
            $table->string('pet_charge_type')->nullable()->after('pets_allowed'); // 'free' or 'chargeable'
            $table->decimal('pet_charge', 10, 2)->default(0.00)->after('pet_charge_type');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['pets_allowed', 'pet_charge_type', 'pet_charge']);
        });
    }
};
