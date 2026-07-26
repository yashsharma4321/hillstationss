<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_categories', function (Blueprint $table) {
            $table->boolean('is_best_view')->default(false)->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('property_categories', function (Blueprint $table) {
            $table->dropColumn('is_best_view');
        });
    }
};
