<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('max_guests')->default(1);
            $table->decimal('base_price', 12, 2);
            $table->decimal('weekend_price', 12, 2)->nullable();
            $table->decimal('extra_guest_charge', 12, 2)->default(0.00);
            $table->integer('total_units')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
