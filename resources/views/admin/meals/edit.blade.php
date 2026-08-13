@extends('layouts.admin')

@section('header', 'Edit Meal')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Edit Meal: {{ $meal->name }}</h2>
        <a href="{{ route('admin.meals.index') }}" style="color: var(--secondary); text-decoration: none; font-size: 0.875rem;">← Back to List</a>
    </div>

    <form action="{{ route('admin.meals.update', $meal) }}" method="POST" style="padding: 2rem; max-width: 800px;">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 2rem;">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Meal Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" value="{{ old('name', $meal->name) }}" required 
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;"
                    placeholder="e.g. Breakfast, Lunch">
                @error('name')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $meal->sort_order) }}" 
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; font-size: 0.875rem; font-weight: 500; color: var(--text-main); cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $meal->is_active) ? 'checked' : '' }} style="margin-right: 0.5rem; width: 1.1rem; height: 1.1rem;">
                    Active Status
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <a href="{{ route('admin.meals.index') }}" style="padding: 0.75rem 1.5rem; background: white; border: 1px solid var(--border); color: var(--text-main); border-radius: 0.5rem; font-weight: 500; text-decoration: none;">Cancel</a>
            <button type="submit" style="padding: 0.75rem 2rem; background: var(--primary); border: none; color: white; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">Update Meal</button>
        </div>
    </form>
</div>
@endsection
