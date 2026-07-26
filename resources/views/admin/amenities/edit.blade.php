@extends('layouts.admin')

@section('header', 'Edit Amenity')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Edit Amenity: {{ $amenity->name }}</h2>
        <a href="{{ route('admin.amenities.index') }}" style="color: var(--secondary); text-decoration: none; font-size: 0.875rem;">← Back to List</a>
    </div>

    <form action="{{ route('admin.amenities.update', $amenity) }}" method="POST" enctype="multipart/form-data" style="padding: 2rem; max-width: 800px;">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 2rem;">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Amenity Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" value="{{ old('name', $amenity->name) }}" required 
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;"
                    placeholder="e.g. Swimming Pool, Free WiFi">
                @error('name')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Category (Optional)</label>
                <select name="category" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem; appearance: none; background: #fff url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2216%22 height=%2216%22 fill=%22none%22 stroke=%22%2364748b%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 class=%22feather feather-chevron-down%22%3E%3Cpath d=%22m6 9 6 6 6-6%22/%3E%3C/svg%3E') no-repeat right 0.75rem center;">
                    <option value="" @selected(old('category', $amenity->category) == '')>General</option>
                    <option value="Basic" @selected(old('category', $amenity->category) == 'Basic')>Basic</option>
                    <option value="Luxury" @selected(old('category', $amenity->category) == 'Luxury')>Luxury</option>
                    <option value="Safety" @selected(old('category', $amenity->category) == 'Safety')>Safety</option>
                    <option value="Outdoor" @selected(old('category', $amenity->category) == 'Outdoor')>Outdoor</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Amenity Icon</label>
                @if($amenity->icon)
                    <div style="margin-bottom: 0.5rem;">
                        <img src="{{ Storage::url($amenity->icon) }}" alt="Preview" style="height: 32px; object-fit: contain;">
                    </div>
                @endif
                <input type="file" name="icon" accept="image/*" 
                    style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Upload a new icon to replace the current one.</p>
                @error('icon')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <a href="{{ route('admin.amenities.index') }}" style="padding: 0.75rem 1.5rem; background: white; border: 1px solid var(--border); color: var(--text-main); border-radius: 0.5rem; font-weight: 500; text-decoration: none;">Cancel</a>
            <button type="submit" style="padding: 0.75rem 2rem; background: var(--primary); border: none; color: white; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">Update Amenity</button>
        </div>
    </form>
</div>
@endsection
