<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->default(0)->after('business_name');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->default(0)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('amount');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
