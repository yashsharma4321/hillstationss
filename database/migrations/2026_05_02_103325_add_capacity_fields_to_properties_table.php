<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->integer('total_bedrooms')->default(0)->after('description');
            $table->integer('total_bathrooms')->default(0)->after('total_bedrooms');
            $table->integer('max_guests')->default(0)->after('total_bathrooms');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['total_bedrooms', 'total_bathrooms', 'max_guests']);
        });
    }
};
