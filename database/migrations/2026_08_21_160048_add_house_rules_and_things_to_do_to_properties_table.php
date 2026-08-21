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
            $table->text('house_rules')->nullable()->after('description');
            $table->text('house_rules_description')->nullable()->after('house_rules');
            $table->json('things_to_do')->nullable()->after('house_rules_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['house_rules', 'house_rules_description', 'things_to_do']);
        });
    }
};
