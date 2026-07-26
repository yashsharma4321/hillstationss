@extends('layouts.admin')

@section('header', 'Edit Room — ' . $room->title)

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
    .meal-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(165px, 1fr)); gap: 0.5rem; }
    .meal-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; cursor: pointer; background: #f8fafc; padding: 0.6rem 0.75rem; border-radius: 0.4rem; border: 1px solid var(--border); transition: background 0.15s; }
    .meal-item:hover { background: #e0e7ff; }
    .meal-item input { accent-color: var(--primary); width: 1rem; height: 1rem; }
    .existing-images-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 1rem; }
    .existing-img-card { background: white; border: 1px solid var(--border); border-radius: 0.6rem; overflow: hidden; position: relative; }
    .existing-img-card img { width: 100%; height: 120px; object-fit: cover; display: block; }
    .existing-img-card .card-body { padding: 0.5rem; }
    .existing-img-card input { width: 100%; padding: 0.35rem 0.4rem; border: 1px solid var(--border); border-radius: 0.3rem; font-size: 0.75rem; font-family: inherit; }
    .btn-delete-img { position: absolute; top: 5px; right: 5px; background: rgba(220,38,38,0.85); color: white; border: none; border-radius: 50%; width: 22px; height: 22px; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1; }
    .image-drop { border: 2px dashed #c7d2fe; background: #f8fafc; border-radius: 0.75rem; padding: 2rem; text-align: center; cursor: pointer; transition: border-color 0.2s, background 0.2s; }
    .image-drop:hover { border-color: var(--primary); background: #eef2ff; }
    .new-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 1rem; margin-top: 1.25rem; }
    .new-img-card { background: white; border: 1px solid var(--border); border-radius: 0.5rem; overflow: hidden; }
    .new-img-card img { width: 100%; height: 110px; object-fit: cover; display: block; }
    .new-img-card input { width: 100%; padding: 0.4rem 0.5rem; border: none; border-top: 1px solid var(--border); font-size: 0.75rem; font-family: inherit; }
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

<form action="{{ route('admin.rooms.update', $room) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- BASIC DETAILS --}}
    <div class="form-section">
        <h3 class="section-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Room Details
        </h3>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Room Title <span style="color:var(--danger)">*</span></label>
                <input type="text" name="title" value="{{ old('title', $room->title) }}" required class="form-input">
                @error('title') <p style="color:var(--danger); font-size:0.75rem; margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Bed Type</label>
                <select name="bed_type" class="form-input">
                    <option value="">— Select Bed Type —</option>
                    @foreach(['Single','Double','Queen','King','Twin','Bunk Bed','Sofa Bed','Triple'] as $bed)
                        <option value="{{ $bed }}" {{ old('bed_type', $room->bed_type) == $bed ? 'selected' : '' }}>{{ $bed }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group full-width">
                <label class="form-label">Room Description</label>
                <textarea name="description" rows="4" class="form-input">{{ old('description', $room->description) }}</textarea>
            </div>
            <div class="form-group">
                <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-size:0.875rem; font-weight:500;">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $room->is_active) ? 'checked' : '' }}
                           style="width:1.125rem; height:1.125rem; accent-color:var(--primary);">
                    <span>Room is Active</span>
                </label>
            </div>
        </div>
    </div>

    {{-- MEAL PLANS --}}
    <div class="form-section">
        <h3 class="section-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Meal Plans Included
        </h3>
        @php $currentMeals = old('meals', $room->meals ?? []); @endphp
        <div class="meal-grid">
            @foreach(['Breakfast','Lunch','Dinner','All Inclusive','Breakfast & Dinner','No Meals'] as $meal)
                <label class="meal-item">
                    <input type="checkbox" name="meals[]" value="{{ $meal }}"
                           {{ in_array($meal, $currentMeals) ? 'checked' : '' }}>
                    {{ $meal }}
                </label>
            @endforeach
        </div>
    </div>

    {{-- EXISTING IMAGES --}}
    @php
        $existingImages = collect($room->images ?? [])
            ->map(fn($img) => is_array($img) ? $img : ['path' => $img, 'alt' => ''])
            ->values();
    @endphp

    @if($existingImages->count())
    <div class="form-section">
        <h3 class="section-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Existing Images
            <span style="font-size:0.75rem; font-weight:400; color:var(--text-muted);">— update alt text or remove images</span>
        </h3>
        <div class="existing-images-grid" id="existing-images-container">
            @foreach($existingImages as $i => $img)
                <div class="existing-img-card" id="existing-img-{{ $i }}">
                    <img src="{{ Storage::url($img['path']) }}" alt="{{ $img['alt'] }}">
                    <div class="card-body">
                        <label style="font-size:0.65rem; color:var(--text-muted); text-transform:uppercase; font-weight:600;">Alt Text</label>
                        <input type="text" name="existing_alts[{{ $i }}]"
                               value="{{ old("existing_alts.$i", $img['alt']) }}"
                               placeholder="Alt text...">
                    </div>
                    <button type="button" class="btn-delete-img"
                            onclick="deleteExistingImage('{{ $img['path'] }}', {{ $i }})">✕</button>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ADD MORE IMAGES --}}
    <div class="form-section">
        <h3 class="section-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Add More Images
        </h3>
        <div class="image-drop" onclick="document.getElementById('new-room-images').click()">
            <input type="file" id="new-room-images" name="room_images[]"
                   multiple accept="image/*" style="display:none"
                   onchange="previewNewImages(this)">
            <svg width="36" height="36" fill="none" stroke="#a5b4fc" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.5rem; display:block;">
                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            <p style="font-weight:600; color:var(--text-main); margin:0 0 0.2rem;">Click to upload more photos</p>
            <p style="font-size:0.8rem; color:var(--text-muted); margin:0;">JPG, PNG, WebP — max 4MB each</p>
        </div>
        <div id="new-preview-grid" class="new-preview-grid"></div>
    </div>

    {{-- SUBMIT --}}
    <div style="display:flex; gap:1rem; justify-content:flex-end; margin-bottom:3rem;">
        <a href="{{ route('admin.properties.rooms.index', $property) }}"
           style="padding:0.875rem 2rem; background:white; border:1px solid var(--border); color:var(--text-main); border-radius:0.5rem; font-weight:600; text-decoration:none;">
            Cancel
        </a>
        <button type="submit"
                style="padding:0.875rem 2.5rem; background:var(--primary); border:none; color:white; border-radius:0.5rem; font-weight:700; cursor:pointer; box-shadow:0 4px 12px rgba(99,102,241,0.3);">
            Update Room
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // Preview newly selected images
    function previewNewImages(input) {
        const grid = document.getElementById('new-preview-grid');
        grid.innerHTML = '';
        Array.from(input.files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = e => {
                const card = document.createElement('div');
                card.className = 'new-img-card';
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

    // Delete existing image via AJAX
    async function deleteExistingImage(imagePath, index) {
        if (!confirm('Remove this image?')) return;
        try {
            const res = await fetch('{{ route('admin.rooms.image.delete', $room) }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ path: imagePath })
            });
            const data = await res.json();
            if (data.success) {
                const card = document.getElementById(`existing-img-${index}`);
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                card.style.transition = 'all 0.2s';
                setTimeout(() => card.remove(), 200);
            } else {
                alert('Could not delete image.');
            }
        } catch (err) {
            alert('Error deleting image.');
        }
    }
</script>
@endsection
