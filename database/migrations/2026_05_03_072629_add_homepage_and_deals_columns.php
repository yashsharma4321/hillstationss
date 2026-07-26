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
            $table->boolean('show_on_homepage')->default(false)->after('status');
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->boolean('is_best_rate')->default(false)->after('status');
            $table->boolean('show_on_homepage')->default(false)->after('is_best_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('show_on_homepage');
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn(['is_best_rate', 'show_on_homepage']);
        });
    }
};
