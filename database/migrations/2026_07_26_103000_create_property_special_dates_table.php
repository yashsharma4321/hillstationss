<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_special_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->date('date');                          // specific date  e.g. 2026-08-15
            $table->decimal('amount', 12, 2);              // special price for that date
            $table->string('label')->nullable();           // e.g. "Weekend", "Holiday", "Diwali"
            $table->timestamps();

            $table->unique(['property_id', 'date']);       // one price per date per property
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_special_dates');
    }
};
