@extends('layouts.admin')

@section('header', 'Add Room — ' . $property->name)

@section('styles')
<style>
    .form-section { background: white; border: 1px solid var(--border); border-radius: 0.75rem; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .section-title { font-size: 1rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--primary); display: flex; align-items: center; gap: 0.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.75rem; }
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
    .full-width { grid-column: span 2; }
    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main); }
    .form-input { width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem; background: white; }
    .form-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .image-drop { border: 2px dashed #c7d2fe; background: #f8fafc; border-radius: 0.75rem; padding: 2.5rem; text-align: center; cursor: pointer; transition: border-color 0.2s, background 0.2s; }
    .image-drop:hover { border-color: var(--primary); background: #eef2ff; }
    .image-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 1rem; margin-top: 1.25rem; }
    .img-preview-card { background: white; border: 1px solid var(--border); border-radius: 0.5rem; overflow: hidden; }
    .img-preview-card img { width: 100%; height: 110px; object-fit: cover; display: block; }
    .img-preview-card input { width: 100%; padding: 0.4rem 0.5rem; border: none; border-top: 1px solid var(--border); font-size: 0.75rem; font-family: inherit; }
</style>
@endsection

@section('content')

@if($errors->any())
    <div style="background:#fee2e2; border:1px solid #ef4444; color:#991b1b; padding:1rem; border-radius:0.5rem; margin-bottom:1.5rem;">
        <ul style="margin:0; padding-left:1.5rem;">
            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

{{-- Back breadcrumb --}}
<div style="margin-bottom:1.5rem;">
    <a href="{{ route('admin.properties.rooms.index', $property) }}"
       style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.875rem; color:var(--text-muted); text-decoration:none; font-weight:500;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"></path></svg>
        Back to Rooms
    </a>
</div>

<form action="{{ route('admin.properties.rooms.store', $property) }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- BASIC DETAILS --}}
    <div class="form-section">
        <h3 class="section-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Room Details
        </h3>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Room Title <span style="color:var(--danger)">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required class="form-input" placeholder="e.g. Deluxe King Room">
                @error('title') <p style="color:var(--danger); font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Bed Type</label>
                <select name="bed_type" class="form-input">
                    <option value="">— Select Bed Type —</option>
                    @foreach(['Single','Double','Queen','King','Twin','Bunk Bed','Sofa Bed','Triple'] as $bed)
                        <option value="{{ $bed }}" {{ old('bed_type') == $bed ? 'selected' : '' }}>{{ $bed }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group full-width">
                <label class="form-label">Room Description</label>
                <textarea name="description" rows="4" class="form-input" placeholder="Describe this room — layout, view, special features...">{{ old('description') }}</textarea>
            </div>
        </div>
    </div>

    {{-- ROOM IMAGES --}}
    <div class="form-section">
        <h3 class="section-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Room Images
            <span style="font-size:0.75rem; font-weight:400; color:var(--text-muted);">— each image gets its own alt text</span>
        </h3>

        <div class="image-drop" onclick="document.getElementById('room-images-input').click()">
            <input type="file" id="room-images-input" name="room_images[]"
                   multiple accept="image/*" style="display:none"
                   onchange="previewImages(this)">
            <svg width="40" height="40" fill="none" stroke="#a5b4fc" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.75rem; display:block;">
                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p style="font-weight:600; color:var(--text-main); margin:0 0 0.25rem;">Click to upload room photos</p>
            <p style="font-size:0.8rem; color:var(--text-muted); margin:0;">JPG, PNG, WebP — max 4MB each. Multiple allowed.</p>
        </div>

        <div id="image-preview-grid" class="image-preview-grid"></div>
    </div>

    {{-- SUBMIT --}}
    <div style="display:flex; gap:1rem; justify-content:flex-end; margin-bottom:3rem;">
        <a href="{{ route('admin.properties.rooms.index', $property) }}"
           style="padding:0.875rem 2rem; background:white; border:1px solid var(--border); color:var(--text-main); border-radius:0.5rem; font-weight:600; text-decoration:none;">
            Cancel
        </a>
        <button type="submit"
                style="padding:0.875rem 2.5rem; background:var(--primary); border:none; color:white; border-radius:0.5rem; font-weight:700; cursor:pointer; box-shadow:0 4px 12px rgba(99,102,241,0.3);">
            Save Room
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    function previewImages(input) {
        const grid = document.getElementById('image-preview-grid');
        grid.innerHTML = '';
        Array.from(input.files).forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = e => {
                const card = document.createElement('div');
                card.className = 'img-preview-card';
                card.innerHTML = `
                    <img src="${e.target.result}" alt="">
                    <input type="text" name="room_image_alts[]"
                           value="${file.name.replace(/\.[^.]+$/, '')}"
                           placeholder="Alt text for this image">
                `;
                grid.appendChild(card);
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endsection
