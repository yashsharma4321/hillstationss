@extends('layouts.admin')

@section('header', 'Create Destination')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Destination Details</h2>
        <a href="{{ route('admin.destinations.index') }}" style="color: var(--secondary); text-decoration: none; font-size: 0.875rem;">← Back to List</a>
    </div>

    <form action="{{ route('admin.destinations.store') }}" method="POST" enctype="multipart/form-data" style="padding: 2rem; max-width: 800px;">
        @csrf
        
        <div style="margin-bottom: 2rem;">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Destination Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required 
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;"
                    placeholder="e.g. Mahabaleshwar">
                @error('name')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">State</label>
                <select name="state_id" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">
                    <option value="">Select State (Optional)</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                    @endforeach
                </select>
                @error('state_id')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Banner Image</label>
                <input type="file" name="image" accept="image/*" 
                    style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">
                @error('image')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Description</label>
                <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;" placeholder="Brief info about this location...">{{ old('description') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Latitude</label>
                    <input type="text" name="latitude" value="{{ old('latitude') }}" 
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;"
                        placeholder="e.g. 17.9307">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Longitude</label>
                    <input type="text" name="longitude" value="{{ old('longitude') }}" 
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;"
                        placeholder="e.g. 73.6477">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Status</label>
                <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem; display: flex; gap: 2rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.875rem;">
                    <input type="checkbox" name="is_best_rate" value="1" {{ old('is_best_rate') ? 'checked' : '' }}>
                    <span>Best Rates Destination</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.875rem;">
                    <input type="checkbox" name="show_on_homepage" value="1" {{ old('show_on_homepage') ? 'checked' : '' }}>
                    <span>Show on Homepage</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.875rem;">
                    <input type="checkbox" name="show_in_menu" value="1" {{ old('show_in_menu') ? 'checked' : '' }}>
                    <span>Show in Menu</span>
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <a href="{{ route('admin.destinations.index') }}" style="padding: 0.75rem 1.5rem; background: white; border: 1px solid var(--border); color: var(--text-main); border-radius: 0.5rem; font-weight: 500; text-decoration: none;">Cancel</a>
            <button type="submit" style="padding: 0.75rem 2rem; background: var(--primary); border: none; color: white; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">Save Destination</button>
        </div>
    </form>
</div>
@endsection
