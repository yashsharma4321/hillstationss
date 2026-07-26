<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedTinyInteger('amenities_rating')->nullable()->after('rating');
            $table->unsignedTinyInteger('cleanliness_rating')->nullable()->after('amenities_rating');
            $table->unsignedTinyInteger('communication_rating')->nullable()->after('cleanliness_rating');
            $table->unsignedTinyInteger('location_rating')->nullable()->after('communication_rating');
            $table->unsignedTinyInteger('value_rating')->nullable()->after('location_rating');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'amenities_rating',
                'cleanliness_rating',
                'communication_rating',
                'location_rating',
                'value_rating',
            ]);
        });
    }
};
