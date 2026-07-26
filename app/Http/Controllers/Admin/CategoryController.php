<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyCategory;
use App\Http\Requests\StorePropertyCategoryRequest;
use App\Http\Requests\UpdatePropertyCategoryRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = PropertyCategory::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StorePropertyCategoryRequest $request)
    {
        $data = $request->validated();
        
        $slug = Str::slug($request->name);
        $count = 1;
        $originalSlug = $slug;
        while (PropertyCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        $data['slug'] = $slug;

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('categories', 'public');
        }

        $data['is_best_view'] = $request->has('is_best_view');
        $data['show_in_menu'] = $request->has('show_in_menu');

        PropertyCategory::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(PropertyCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdatePropertyCategoryRequest $request, PropertyCategory $category)
    {
        $data = $request->validated();

        $slug = Str::slug($request->name);
        $count = 1;
        $originalSlug = $slug;
        while (PropertyCategory::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        $data['slug'] = $slug;

        if ($request->hasFile('icon')) {
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            $data['icon'] = $request->file('icon')->store('categories', 'public');
        }

        $data['is_best_view'] = $request->has('is_best_view');
        $data['show_in_menu'] = $request->has('show_in_menu');

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(PropertyCategory $category)
    {
        if ($category->icon) {
            Storage::disk('public')->delete($category->icon);
        }
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
