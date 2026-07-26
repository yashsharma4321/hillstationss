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
        Schema::table('pages', function (Blueprint $table) {
            $table->renameColumn('feature_image', 'banner_image');
            $table->renameColumn('alt_text', 'banner_alt_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->renameColumn('banner_image', 'feature_image');
            $table->renameColumn('banner_alt_text', 'alt_text');
        });
    }
};
