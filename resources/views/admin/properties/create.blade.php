@extends('layouts.admin')

@section('header', 'Add New Property')

@section('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1, #8b5cf6);
        --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        --radius-sm: 0.5rem;
        --radius-md: 0.75rem;
        --radius-lg: 1rem;
    }

    .form-section {
        background: white;
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-sm);
        transition: box-shadow 0.3s ease;
    }
    
    .form-section:hover {
        box-shadow: var(--shadow-md);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1.75rem;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 1rem;
    }

    .section-title svg {
        width: 22px;
        height: 22px;
        color: var(--primary);
    }

    .section-title .badge-count {
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        color: #4338ca;
        font-size: 0.7rem;
        padding: 0.2rem 0.75rem;
        border-radius: 999px;
        font-weight: 700;
        margin-left: auto;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .full-width {
        grid-column: span 2;
    }

    .form-group {
        margin-bottom: 0.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1e293b;
        letter-spacing: 0.01em;
    }

    .form-label .required-star {
        color: #ef4444;
        margin-left: 2px;
    }

    .form-label .help-text {
        font-weight: 400;
        font-size: 0.75rem;
        color: #94a3b8;
        margin-left: 0.5rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #e2e8f0;
        border-radius: var(--radius-sm);
        font-family: inherit;
        font-size: 0.875rem;
        color: #1e293b;
        transition: all 0.2s ease;
        background-color: #fafbfc;
    }

    .form-input:hover {
        border-color: #cbd5e1;
        background-color: #ffffff;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary);
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .form-input::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    select.form-input {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
        cursor: pointer;
    }

    textarea.form-input {
        resize: vertical;
        min-height: 100px;
    }

    .form-error {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Amenity Grid */
    .amenity-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.75rem;
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: var(--radius-md);
        border: 1.5px solid #e2e8f0;
    }

    .amenity-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        font-size: 0.875rem;
        cursor: pointer;
        padding: 0.5rem 0.75rem;
        border-radius: var(--radius-sm);
        transition: background 0.15s ease;
        background: white;
        border: 1px solid #e2e8f0;
    }

    .amenity-item:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .amenity-item input[type="checkbox"] {
        width: 1.125rem;
        height: 1.125rem;
        accent-color: var(--primary);
        cursor: pointer;
        flex-shrink: 0;
    }

    .amenity-item img {
        width: 18px;
        height: 18px;
        object-fit: contain;
        flex-shrink: 0;
    }

    /* Room Card */
    .room-card {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: var(--radius-md);
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        position: relative;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
    }

    .room-card:hover {
        border-color: #c7d2fe;
        box-shadow: var(--shadow-md);
    }

    .room-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px dashed #e2e8f0;
    }

    .room-card-title {
        font-weight: 700;
        font-size: 1rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }

    .room-card-title .room-number {
        background: var(--primary-gradient);
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .btn-remove-room {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: var(--radius-sm);
        padding: 0.4rem 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .btn-remove-room:hover {
        background: #fca5a5;
        transform: translateY(-1px);
    }

    .btn-add-room {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 2rem;
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        transition: all 0.3s ease;
    }

    .btn-add-room:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
    }

    .btn-add-room:active {
        transform: translateY(0);
    }

    /* Meal Grid */
    .meal-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.5rem;
        background: white;
        padding: 1rem;
        border-radius: var(--radius-sm);
        border: 1.5px solid #e2e8f0;
    }

    .meal-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.825rem;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: var(--radius-sm);
        transition: background 0.15s ease;
    }

    .meal-item:hover {
        background: #f1f5f9;
    }

    .meal-item input[type="checkbox"] {
        accent-color: var(--primary);
        width: 1rem;
        height: 1rem;
        cursor: pointer;
    }

    /* Room Image Drop Zone */
    .room-image-drop {
        border: 2px dashed #c7d2fe;
        background: white;
        border-radius: var(--radius-md);
        padding: 2rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .room-image-drop:hover {
        border-color: var(--primary);
        background: #f8fafc;
        transform: scale(1.01);
    }

    .room-image-drop svg {
        color: #a5b4fc;
    }

    .room-image-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .room-img-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-sm);
        overflow: hidden;
        transition: all 0.2s ease;
        box-shadow: var(--shadow-sm);
    }

    .room-img-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .room-img-card img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }

    .room-img-card input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: none;
        border-top: 1px solid #e2e8f0;
        font-size: 0.75rem;
        color: #1e293b;
        background: #fafbfc;
    }

    .room-img-card input:focus {
        outline: none;
        background: white;
    }

    /* Empty State */
    .empty-rooms {
        text-align: center;
        padding: 3rem 2rem;
        color: #94a3b8;
        font-size: 0.9rem;
        border: 2px dashed #e2e8f0;
        border-radius: var(--radius-md);
        background: #fafbfc;
    }

    .empty-rooms svg {
        color: #cbd5e1;
    }

    .empty-rooms p {
        color: #64748b;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-bottom: 4rem;
        padding-top: 2rem;
        border-top: 2px solid #f1f5f9;
    }

    .btn-cancel {
        padding: 0.875rem 2.5rem;
        background: white;
        border: 1.5px solid #e2e8f0;
        color: #64748b;
        border-radius: var(--radius-md);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-cancel:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #1e293b;
        text-decoration: none;
    }

    .btn-submit {
        padding: 0.875rem 3.5rem;
        background: var(--primary-gradient);
        border: none;
        color: white;
        border-radius: var(--radius-md);
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    /* Add buttons */
    .btn-add-rule,
    .btn-add-attraction,
    .btn-add-insta {
        margin-top: 0.5rem;
        background: #f0fdf4;
        color: #166534;
        border: 1.5px dashed #bbf7d0;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-md);
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-add-rule:hover,
    .btn-add-attraction:hover,
    .btn-add-insta:hover {
        background: #dcfce7;
        border-color: #86efac;
        transform: translateY(-1px);
    }

    /* Dynamic Cards */
    .dynamic-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-md);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        position: relative;
        transition: all 0.2s ease;
    }

    .dynamic-card:hover {
        border-color: #cbd5e1;
        box-shadow: var(--shadow-sm);
    }

    .btn-remove-dynamic {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 14px;
        line-height: 1;
    }

    .btn-remove-dynamic:hover {
        background: #fca5a5;
        transform: scale(1.1);
    }

    /* Gallery Drop Zone */
    .gallery-drop-zone {
        border: 2px dashed #e2e8f0;
        padding: 2.5rem;
        border-radius: var(--radius-lg);
        background: #fafbfc;
        text-align: center;
        transition: all 0.3s ease;
    }

    .gallery-drop-zone:hover {
        border-color: #c7d2fe;
        background: #f8fafc;
    }

    .gallery-drop-zone .upload-label {
        background: white;
        border: 1.5px solid #e2e8f0;
        padding: 0.75rem 2rem;
        border-radius: var(--radius-md);
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #1e293b;
        transition: all 0.2s ease;
        margin-bottom: 1rem;
    }

    .gallery-drop-zone .upload-label:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }

    .gallery-drop-zone .hint-text {
        color: #94a3b8;
        font-size: 0.8rem;
        margin: 0;
    }

    .gallery-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .gallery-preview-item {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-sm);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }

    .gallery-preview-item:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .gallery-preview-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .gallery-preview-item input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: none;
        border-top: 1px solid #e2e8f0;
        font-size: 0.75rem;
        color: #1e293b;
        background: #fafbfc;
    }

    .gallery-preview-item input:focus {
        outline: none;
        background: white;
    }

    /* Checkbox Group */
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        color: #1e293b;
        padding: 0.5rem 0.75rem;
        border-radius: var(--radius-sm);
        transition: background 0.15s ease;
    }

    .checkbox-group:hover {
        background: #f1f5f9;
    }

    .checkbox-group input[type="checkbox"] {
        width: 1.125rem;
        height: 1.125rem;
        accent-color: var(--primary);
        cursor: pointer;
    }

    /* Error Alert */
    .error-alert {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
        padding: 1rem 1.5rem;
        border-radius: var(--radius-md);
        margin-bottom: 2rem;
    }

    .error-alert ul {
        margin: 0;
        padding-left: 1.25rem;
    }

    .error-alert li {
        margin-bottom: 0.25rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-section {
            padding: 1.25rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .full-width {
            grid-column: span 1;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons a,
        .action-buttons button {
            width: 100%;
            justify-content: center;
        }

        .amenity-grid {
            grid-template-columns: 1fr 1fr;
            padding: 1rem;
        }

        .room-card {
            padding: 1.25rem;
        }

        .room-card-header {
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .gallery-preview-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 480px) {
        .amenity-grid {
            grid-template-columns: 1fr;
        }

        .gallery-preview-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
@if ($errors->any())
    <div class="error-alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data" id="property-form">
    @csrf

    <!-- BASIC INFORMATION -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Basic Information
        </h3>
        <div class="form-grid">
            <div class="form-group full-width">
                <label class="form-label">Property Name <span class="required-star">*</span></label>
                <div style="display: flex; gap: 1rem;">
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-input" placeholder="e.g. Hilltop Villa & Suites" style="flex: 2;">
                </div>
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Category <span class="required-star">*</span></label>
                <select name="category_id" required class="form-input">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Destination <span class="required-star">*</span></label>
                <select name="destination_id" required class="form-input">
                    <option value="">Select Destination</option>
                    @foreach($destinations as $destination)
                        <option value="{{ $destination->id }}" {{ old('destination_id') == $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Assign to Vendor <span class="required-star">*</span></label>
                <select name="vendor_id" required class="form-input">
                    <option value="">Select Vendor</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->user->name }} ({{ $vendor->user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group full-width">
                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap:1.5rem;">
                    <div>
                        <label class="form-label">Total Bedrooms <span class="required-star">*</span></label>
                        <input type="number" name="total_bedrooms" value="{{ old('total_bedrooms', 0) }}" required class="form-input" min="0">
                    </div>
                    <div>
                        <label class="form-label">Total Bathrooms <span class="required-star">*</span></label>
                        <input type="number" name="total_bathrooms" value="{{ old('total_bathrooms', 0) }}" required class="form-input" min="0">
                    </div>
                    <div>
                        <label class="form-label">Max Guests <span class="required-star">*</span></label>
                        <input type="number" name="max_guests" value="{{ old('max_guests', 0) }}" required class="form-input" min="0">
                    </div>
                    <div>
                        <label class="form-label">Max Capacity</label>
                        <input type="number" name="max_capacity" value="{{ old('max_capacity') }}" class="form-input" min="0" placeholder="Guests + Staff">
                    </div>
                </div>
            </div>

            <div class="form-group full-width">
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" class="form-input" placeholder="Tell us about the property...">{{ old('description') }}</textarea>
            </div>
        </div>
    </div>

    <!-- LOCATION DETAILS -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Location Details
        </h3>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">City <span class="required-star">*</span></label>
                <input type="text" name="city" value="{{ old('city') }}" required class="form-input" placeholder="e.g. Panchgani">
            </div>
            <div class="form-group">
                <label class="form-label">State <span class="required-star">*</span></label>
                <input type="text" name="state" value="{{ old('state') }}" required class="form-input" placeholder="e.g. Maharashtra">
            </div>
            <div class="form-group">
                <label class="form-label">Country <span class="required-star">*</span></label>
                <input type="text" name="country" value="{{ old('country', 'India') }}" required class="form-input">
            </div>
            <div class="form-group">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                    <div>
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" value="{{ old('latitude') }}" class="form-input" placeholder="e.g. 17.92">
                    </div>
                    <div>
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" value="{{ old('longitude') }}" class="form-input" placeholder="e.g. 73.81">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AMENITIES -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.143-7.714L1 12l6.857-2.143L11 3z"></path></svg>
            Included Amenities
        </h3>
        <div class="amenity-grid">
            @foreach($amenities as $amenity)
                <label class="amenity-item">
                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" {{ is_array(old('amenities')) && in_array($amenity->id, old('amenities')) ? 'checked' : '' }}>
                    @if($amenity->icon)
                        <img src="{{ Storage::url($amenity->icon) }}" alt="{{ $amenity->name }}">
                    @endif
                    {{ $amenity->name }}
                </label>
            @endforeach
        </div>
    </div>

    <!-- COLLECTIONS -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            Property Collections
        </h3>
        <div class="amenity-grid">
            @foreach($collections as $collection)
                <label class="amenity-item">
                    <input type="checkbox" name="collections[]" value="{{ $collection->id }}" {{ is_array(old('collections')) && in_array($collection->id, old('collections')) ? 'checked' : '' }}>
                    {{ $collection->heading }}
                </label>
            @endforeach
        </div>
    </div>

    <!-- MEAL PLANS -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 7h13M7 13l-1.5-7"></path><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/></svg>
            Meal Plans Included
        </h3>
        @php
            $mealOptions = ['Breakfast', 'Lunch', 'Dinner', 'All Inclusive', 'Breakfast & Dinner', 'No Meals', 'Veg', 'Non-Veg', 'Jain'];
            $selectedMeals = old('meals', []);
        @endphp
        <div class="amenity-grid">
            @foreach($mealOptions as $meal)
                <label class="amenity-item">
                    <input type="checkbox" name="meals[]" value="{{ $meal }}" {{ in_array($meal, $selectedMeals) ? 'checked' : '' }}>
                    {{ $meal }}
                </label>
            @endforeach
        </div>
    </div>

    <!-- CANCELLATION RULES -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Cancellation Rules
        </h3>
        <p style="color:#64748b; font-size:0.875rem; margin-bottom:1.5rem;">Define how much to deduct if the user cancels before check-in.</p>
        <div id="cancellation-rules-container">
            <!-- Dynamic rules -->
        </div>
        <button type="button" class="btn-add-rule" onclick="addCancellationRule()">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Add Cancellation Rule
        </button>
    </div>

    <!-- SETTINGS & MEDIA -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Operational Info & Gallery
        </h3>
        <div class="form-grid">
            <div class="form-group">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                    <div>
                        <label class="form-label">Check-in After</label>
                        <input type="time" name="check_in_time" value="{{ old('check_in_time', '14:00') }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Check-out Before</label>
                        <input type="time" name="check_out_time" value="{{ old('check_out_time', '11:00') }}" class="form-input">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Publishing Status <span class="required-star">*</span></label>
                <select name="status" required class="form-input">
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active / Live</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive / Hidden</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Property Brochure <span class="help-text">(PDF)</span></label>
                <input type="file" name="brochure" accept="application/pdf" class="form-input" style="padding: 0.5rem;">
                <p style="color:#94a3b8; font-size:0.7rem; margin-top:0.25rem;">Upload property brochure or details PDF (Max 10MB)</p>
            </div>
            <div class="form-group">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                    <div>
                        <label class="form-label">GST %</label>
                        <input type="number" name="gst" value="{{ old('gst', 0) }}" class="form-input" min="0" step="0.01">
                    </div>
                    <div>
                        <label class="form-label">Price / Amount</label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-input" placeholder="Amount">
                    </div>
                </div>
            </div>
            <div class="form-group full-width">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem;">
                    <div>
                        <label class="form-label">Extra Person Charge</label>
                        <input type="number" name="extra_person_charge" value="{{ old('extra_person_charge', 0) }}" class="form-input" min="0" step="0.01">
                    </div>
                    <div style="display: flex; align-items: flex-end; gap: 2rem; padding-bottom: 0.25rem;">
                        <label class="checkbox-group">
                            <input type="checkbox" name="show_on_homepage" value="1" {{ old('show_on_homepage') ? 'checked' : '' }}>
                            <span>Show on Homepage</span>
                        </label>
                        <label class="checkbox-group">
                            <input type="checkbox" name="show_in_menu" value="1" {{ old('show_in_menu') ? 'checked' : '' }}>
                            <span>Show in Menu</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group full-width">
                <label class="form-label">Property Gallery <span class="required-star">*</span></label>
                <div class="gallery-drop-zone">
                    <input type="file" name="images[]" multiple accept="image/*" required id="gallery-input" style="display: none;">
                    <label for="gallery-input" class="upload-label">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Choose Photos
                    </label>
                    <p class="hint-text">Upload HD quality images for better conversion</p>
                    <div id="gallery-preview" class="gallery-preview-grid"></div>
                </div>
            </div>

            <!-- INSTAGRAM VIDEOS -->
            <div class="form-group full-width" style="margin-top: 1.5rem; border-top: 1.5px solid #f1f5f9; padding-top: 2rem;">
                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; color: var(--primary); font-size: 1rem;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                    Instagram Videos <span class="help-text">(Thumbnail + Video Link)</span>
                </label>
                <div id="insta-links-container">
                    <!-- Dynamic Instagram video blocks -->
                </div>
                <button type="button" class="btn-add-insta" onclick="addInstaLink()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                    Add Instagram Video
                </button>
            </div>

            <!-- NEARBY ATTRACTIONS -->
            <div class="form-group full-width" style="margin-top: 1.5rem; border-top: 1.5px solid #f1f5f9; padding-top: 2rem;">
                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; color: var(--primary); font-size: 1rem;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Nearby Attractions
                </label>
                <div id="attractions-container">
                    <!-- Dynamic attractions will be added here -->
                </div>
                <button type="button" class="btn-add-attraction" onclick="addAttraction()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                    Add Attraction
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- ROOMS & UNITS BUILDER --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Rooms &amp; Units
            <span class="badge-count" id="room-count-badge">0 Rooms</span>
        </h3>

        <div id="empty-rooms-placeholder" class="empty-rooms">
            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.75rem; color:#cbd5e1;"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <p style="font-weight:600; color:#1e293b; margin-bottom:0.25rem;">No rooms added yet</p>
            <p style="color:#94a3b8;">Click <strong>Add Room</strong> below to add rooms with images, bed type &amp; meal plans.</p>
        </div>

        <div id="rooms-container"></div>

        <div style="margin-top:1.5rem;">
            <button type="button" class="btn-add-room" onclick="addRoom()">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                Add Room
            </button>
        </div>
    </div>

    <div class="action-buttons">
        <a href="{{ route('admin.properties.index') }}" class="btn-cancel">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            Cancel
        </a>
        <button type="submit" class="btn-submit">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Add Property Now
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // ── Property Gallery preview ──────────────────────────────────────────
    document.getElementById('gallery-input').addEventListener('change', function(e) {
        const container = document.getElementById('gallery-preview');
        container.innerHTML = '';
        Array.from(e.target.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'gallery-preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <input type="text" name="image_alts[]" value="${file.name.replace(/\.[^.]+$/, '')}" 
                           placeholder="Alt description">
                `;
                container.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    });

    // ── Room Builder ──────────────────────────────────────────────────────
    let roomIndex = 0;

    const MEAL_OPTIONS = ['Breakfast', 'Lunch', 'Dinner', 'All Inclusive', 'Breakfast & Dinner', 'No Meals', 'Veg', 'Non-Veg', 'Jain'];
    const BED_TYPES    = ['Single', 'Double', 'Queen', 'King', 'Twin', 'Bunk Bed', 'Sofa Bed', 'Triple'];

    function updateBadge() {
        const count = document.querySelectorAll('.room-card').length;
        document.getElementById('room-count-badge').textContent = count + (count === 1 ? ' Room' : ' Rooms');
        document.getElementById('empty-rooms-placeholder').style.display = count === 0 ? 'block' : 'none';
    }

    function addRoom() {
        const idx = roomIndex++;
        const container = document.getElementById('rooms-container');

        const bedOptions = BED_TYPES.map(b => `<option value="${b}">${b}</option>`).join('');
        const mealCheckboxes = MEAL_OPTIONS.map(m =>
            `<label class="meal-item">
                <input type="checkbox" name="rooms[${idx}][meals][]" value="${m}"> ${m}
            </label>`
        ).join('');

        const card = document.createElement('div');
        card.className = 'room-card';
        card.id = `room-card-${idx}`;
        card.innerHTML = `
            <div class="room-card-header">
                <div class="room-card-title">
                    <span class="room-number">${idx + 1}</span> Room Details
                </div>
                <button type="button" class="btn-remove-room" onclick="removeRoom(${idx})">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                    Remove
                </button>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.25rem;">
                <div>
                    <label class="form-label">Room Title <span class="required-star">*</span></label>
                    <input type="text" name="rooms[${idx}][title]" required class="form-input" placeholder="e.g. Deluxe King Room">
                </div>
                <div>
                    <label class="form-label">Bed Type</label>
                    <select name="rooms[${idx}][bed_type]" class="form-input">
                        <option value="">Select Bed Type</option>
                        ${bedOptions}
                    </select>
                </div>
                <div style="grid-column:span 2;">
                    <label class="form-label">Room Description</label>
                    <textarea name="rooms[${idx}][description]" rows="2" class="form-input" placeholder="Describe this room..."></textarea>
                </div>
            </div>

            <div style="margin-bottom:1.25rem;">
                <label class="form-label" style="margin-bottom:0.5rem;">Meal Plans Included</label>
                <div class="meal-grid">${mealCheckboxes}</div>
            </div>

            <div>
                <label class="form-label">Room Images <span class="help-text">(each image can have alt text)</span></label>
                <div class="room-image-drop" onclick="document.getElementById('room-img-input-${idx}').click()">
                    <input type="file" id="room-img-input-${idx}" name="room_images[${idx}][]"
                           multiple accept="image/*" style="display:none"
                           onchange="previewRoomImages(this, ${idx})">
                    <svg width="36" height="36" fill="none" stroke="#a5b4fc" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.5rem;display:block;"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p style="font-size:0.8rem; color:#94a3b8; margin:0;">Click to upload room photos</p>
                </div>
                <div id="room-img-preview-${idx}" class="room-image-preview"></div>
            </div>
        `;

        container.appendChild(card);
        updateBadge();
    }

    function removeRoom(idx) {
        const card = document.getElementById(`room-card-${idx}`);
        if (card) {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            card.style.transition = 'all 0.2s';
            setTimeout(() => { card.remove(); updateBadge(); }, 200);
        }
    }

    function previewRoomImages(input, idx) {
        const preview = document.getElementById(`room-img-preview-${idx}`);
        preview.innerHTML = '';
        Array.from(input.files).forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const card = document.createElement('div');
                card.className = 'room-img-card';
                card.innerHTML = `
                    <img src="${e.target.result}" alt="">
                    <input type="text" name="room_image_alts[${idx}][]"
                           value="${file.name.replace(/\.[^.]+$/, '')}"
                           placeholder="Alt text for this image">
                `;
                preview.appendChild(card);
            };
            reader.readAsDataURL(file);
        });
    }

    let instaIdx = 0;
    function addInstaLink() {
        const container = document.getElementById('insta-links-container');
        const card = document.createElement('div');
        card.className = 'dynamic-card';
        card.innerHTML = `
            <button type="button" class="btn-remove-dynamic" onclick="this.parentElement.remove()">✕</button>
            <div style="display:grid; grid-template-columns:140px 1fr; gap:1.5rem;">
                <div>
                    <label class="form-label" style="font-size:0.75rem;">Thumbnail Image</label>
                    <input type="file" name="instagram_video_images[${instaIdx}]" class="form-input" style="font-size:0.7rem; padding:0.25rem;" accept="image/*">
                </div>
                <div style="display:grid; align-content:center; gap:0.5rem;">
                    <label class="form-label" style="font-size:0.75rem;">Video URL <span class="required-star">*</span></label>
                    <input type="url" name="instagram_video_links[${instaIdx}]" class="form-input" placeholder="https://www.instagram.com/reels/XXXXX/" required>
                </div>
            </div>
        `;
        container.appendChild(card);
        instaIdx++;
    }

    let cancelIdx = 0;
    function addCancellationRule() {
        const container = document.getElementById('cancellation-rules-container');
        const card = document.createElement('div');
        card.className = 'dynamic-card';
        card.style.paddingRight = '3.5rem';
        card.innerHTML = `
            <button type="button" class="btn-remove-dynamic" onclick="this.parentElement.remove()">✕</button>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div>
                    <label class="form-label">Days Before Check-in</label>
                    <input type="number" name="cancellation_rules[${cancelIdx}][days_before]" class="form-input" placeholder="e.g. 5" required min="0">
                </div>
                <div>
                    <label class="form-label">Deduction Percentage (%)</label>
                    <input type="number" name="cancellation_rules[${cancelIdx}][deduction_percentage]" class="form-input" placeholder="e.g. 20" required min="0" max="100" step="0.01">
                </div>
            </div>
        `;
        container.appendChild(card);
        cancelIdx++;
    }

    let attractionIdx = 0;
    function addAttraction() {
        const container = document.getElementById('attractions-container');
        const card = document.createElement('div');
        card.className = 'dynamic-card';
        card.style.paddingRight = '3.5rem';
        card.innerHTML = `
            <button type="button" class="btn-remove-dynamic" onclick="this.parentElement.remove()">✕</button>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div>
                    <label class="form-label">Attraction Name <span class="required-star">*</span></label>
                    <input type="text" name="attractions[${attractionIdx}][name]" class="form-input" placeholder="e.g. Mahabaleshwar Temple" required>
                </div>
                <div>
                    <label class="form-label">Distance (km)</label>
                    <input type="number" step="0.1" name="attractions[${attractionIdx}][distance]" class="form-input" placeholder="e.g. 2.5" min="0">
                </div>
            </div>
        `;
        container.appendChild(card);
        attractionIdx++;
    }

    document.addEventListener('DOMContentLoaded', function() {
        addInstaLink();
    });
</script>
@endsection