<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function index()
    {
        $meals = \App\Models\Meal::orderBy('sort_order')->paginate(10);
        return view('admin.meals.index', compact('meals'));
    }

    public function create()
    {
        return view('admin.meals.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:meals',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        \App\Models\Meal::create($data);

        return redirect()->route('admin.meals.index')->with('success', 'Meal created successfully.');
    }

    public function edit(\App\Models\Meal $meal)
    {
        return view('admin.meals.edit', compact('meal'));
    }

    public function update(Request $request, \App\Models\Meal $meal)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:meals,name,' . $meal->id,
            'sort_order' => 'nullable|integer',
        ]);

        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $meal->update($data);

        return redirect()->route('admin.meals.index')->with('success', 'Meal updated successfully.');
    }

    public function destroy(\App\Models\Meal $meal)
    {
        $meal->delete();
        return redirect()->route('admin.meals.index')->with('success', 'Meal deleted successfully.');
    }
}
