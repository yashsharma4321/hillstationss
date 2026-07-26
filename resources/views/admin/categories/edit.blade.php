@extends('layouts.admin')

@section('header', 'Edit Category')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Edit Category: {{ $category->name }}</h2>
        <a href="{{ route('admin.categories.index') }}" style="color: var(--secondary); text-decoration: none; font-size: 0.875rem;">← Back to List</a>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" style="padding: 2rem; max-width: 800px;">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 2rem;">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Category Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required 
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;"
                    placeholder="e.g. Luxury Villas">
                @error('name')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Category Group</label>
                <select name="category_group" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">
                    <option value="">No Group</option>
                    <option value="BY AMENITIES" {{ old('category_group', $category->category_group) == 'BY AMENITIES' ? 'selected' : '' }}>BY AMENITIES</option>
                    <option value="BY EXPERIENCE" {{ old('category_group', $category->category_group) == 'BY EXPERIENCE' ? 'selected' : '' }}>BY EXPERIENCE</option>
                    <option value="BY GROUP SIZE" {{ old('category_group', $category->category_group) == 'BY GROUP SIZE' ? 'selected' : '' }}>BY GROUP SIZE</option>
                    <option value="BY BUDGET" {{ old('category_group', $category->category_group) == 'BY BUDGET' ? 'selected' : '' }}>BY BUDGET</option>
                    <option value="PET FRIENDLY" {{ old('category_group', $category->category_group) == 'PET FRIENDLY' ? 'selected' : '' }}>PET FRIENDLY</option>
                </select>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Group this category under a menu section.</p>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Category Icon/Image</label>
                @if($category->icon)
                    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; background: #f8fafc; padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--border);">
                        <img src="{{ Storage::url($category->icon) }}" alt="Preview" style="height: 64px; border-radius: 0.25rem;">
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Current Icon</span>
                    </div>
                @endif
                <input type="file" name="icon" accept="image/*" 
                    style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Upload a new icon to replace the current one.</p>
                @error('icon')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; font-weight: 500; color: var(--text-main); cursor: pointer;">
                    <input type="checkbox" name="is_best_view" value="1" {{ old('is_best_view', $category->is_best_view) ? 'checked' : '' }} 
                        style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                    <span>Mark as Best View</span>
                </label>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-left: 2rem;">Highlight this category on the homepage/frontend.</p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; font-weight: 500; color: var(--text-main); cursor: pointer;">
                    <input type="checkbox" name="show_in_menu" value="1" {{ old('show_in_menu', $category->show_in_menu) ? 'checked' : '' }} 
                        style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                    <span>Show in Menu</span>
                </label>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-left: 2rem;">Show this category in the main navigation menu.</p>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <a href="{{ route('admin.categories.index') }}" style="padding: 0.75rem 1.5rem; background: white; border: 1px solid var(--border); color: var(--text-main); border-radius: 0.5rem; font-weight: 500; text-decoration: none;">Cancel</a>
            <button type="submit" style="padding: 0.75rem 2rem; background: var(--primary); border: none; color: white; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">Update Category</button>
        </div>
    </form>
</div>
@endsection
