<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('meal_property', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['property_id', 'meal_id']);
        });

        $defaultMeals = [
            'Breakfast',
            'Lunch',
            'Dinner',
            'All Inclusive',
            'Breakfast & Dinner',
            'No Meals',
            'Veg',
            'Non-Veg',
            'Jain',
        ];

        foreach ($defaultMeals as $index => $meal) {
            DB::table('meals')->insert([
                'name' => $meal,
                'slug' => Str::slug($meal),
                'sort_order' => $index + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasColumn('properties', 'meals')) {
            $mealIdsByName = DB::table('meals')->pluck('id', 'name');

            DB::table('properties')
                ->select('id', 'meals')
                ->whereNotNull('meals')
                ->orderBy('id')
                ->chunkById(100, function ($properties) use ($mealIdsByName) {
                    foreach ($properties as $property) {
                        $propertyMeals = json_decode($property->meals, true);
                        if (!is_array($propertyMeals)) {
                            continue;
                        }

                        foreach ($propertyMeals as $mealName) {
                            $mealId = $mealIdsByName[$mealName] ?? null;
                            if (!$mealId) {
                                continue;
                            }

                            DB::table('meal_property')->updateOrInsert(
                                [
                                    'property_id' => $property->id,
                                    'meal_id' => $mealId,
                                ],
                                [
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]
                            );
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_property');
        Schema::dropIfExists('meals');
    }
};
