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
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('title')->nullable()->after('property_id');
            $table->text('description')->nullable()->after('title');
            $table->string('bed_type')->nullable()->after('description');
            $table->json('images')->nullable()->after('bed_type');
            $table->json('meals')->nullable()->after('images');
            
            // Drop room_type_id since user didn't mention it in the new structure
            if (Schema::hasColumn('rooms', 'room_type_id')) {
                $table->dropForeign(['room_type_id']);
                $table->dropColumn('room_type_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'bed_type', 'images', 'meals']);
            $table->foreignId('room_type_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
