@extends('layouts.admin')

@section('header', 'Edit Property: ' . $property->name)

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

    /* Gallery Cards */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .gallery-card {
        background: white;
        border: 1.5px solid #e2e8f0;
        border-radius: var(--radius-md);
        overflow: hidden;
        position: relative;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
    }

    .gallery-card:hover {
        box-shadow: var(--shadow-md);
        border-color: #c7d2fe;
        transform: translateY(-2px);
    }

    .gallery-card img {
        width: 100%;
        height: 160px;
        object-fit: cover;
    }

    .gallery-card .card-body {
        padding: 0.75rem;
    }

    .gallery-card .card-body label {
        font-size: 0.65rem;
        color: #94a3b8;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.05em;
    }

    .gallery-card .card-body input {
        width: 100%;
        padding: 0.4rem 0.6rem;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        color: #1e293b;
        background: #fafbfc;
        transition: all 0.2s ease;
    }

    .gallery-card .card-body input:focus {
        outline: none;
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .btn-remove-image {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        box-shadow: var(--shadow-sm);
    }

    .btn-remove-image:hover {
        background: #dc2626;
        transform: scale(1.1);
    }

    /* Drop Zone */
    .drop-zone {
        border: 2px dashed #e2e8f0;
        padding: 2.5rem;
        border-radius: var(--radius-lg);
        background: #fafbfc;
        text-align: center;
        transition: all 0.3s ease;
    }

    .drop-zone:hover {
        border-color: #c7d2fe;
        background: #f8fafc;
    }

    .drop-zone .upload-label {
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

    .drop-zone .upload-label:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }

    .drop-zone .hint-text {
        color: #94a3b8;
        font-size: 0.8rem;
        margin: 0;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .preview-item {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: var(--radius-sm);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }

    .preview-item:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .preview-item img {
        width: 100%;
        height: 140px;
        object-fit: cover;
    }

    .preview-item input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: none;
        border-top: 1px solid #e2e8f0;
        font-size: 0.75rem;
        color: #1e293b;
        background: #fafbfc;
    }

    .preview-item input:focus {
        outline: none;
        background: white;
    }

    /* Room Cards */
    .room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.25rem;
    }

    .room-card-item {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
    }

    .room-card-item:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
        border-color: #c7d2fe;
    }

    .room-card-item .room-image {
        width: 100%;
        height: 160px;
        object-fit: cover;
    }

    .room-card-item .room-placeholder {
        width: 100%;
        height: 160px;
        background: linear-gradient(135deg, #e0e7ff, #ede9fe);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .room-card-item .room-body {
        padding: 1rem;
    }

    .room-card-item .room-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .room-card-item .room-bed {
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .room-card-item .room-meals {
        font-size: 0.75rem;
        color: #4f46e5;
        margin-bottom: 0.5rem;
    }

    .room-card-item .room-meta {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 0.75rem;
    }

    .room-card-item .room-status {
        font-weight: 600;
    }

    .room-card-item .room-status.active {
        color: #16a34a;
    }

    .room-card-item .room-status.inactive {
        color: #dc2626;
    }

    .room-card-item .room-actions {
        display: flex;
        gap: 0.5rem;
    }

    .room-card-item .room-actions .btn-edit {
        flex: 1;
        padding: 0.5rem;
        background: var(--primary-gradient);
        color: white;
        border-radius: var(--radius-sm);
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 600;
        text-align: center;
        transition: all 0.2s ease;
        border: none;
    }

    .room-card-item .room-actions .btn-edit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .room-card-item .room-actions .btn-delete {
        flex: 1;
        padding: 0.5rem;
        background: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .room-card-item .room-actions .btn-delete:hover {
        background: #fca5a5;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        border: 2px dashed #e2e8f0;
        border-radius: var(--radius-md);
        background: #fafbfc;
    }

    .empty-state svg {
        color: #cbd5e1;
        margin: 0 auto 0.75rem;
        display: block;
    }

    .empty-state .title {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .empty-state .subtitle {
        color: #94a3b8;
        font-size: 0.875rem;
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

    /* Add Buttons */
    .btn-add {
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

    .btn-add:hover {
        background: #dcfce7;
        border-color: #86efac;
        transform: translateY(-1px);
    }

    .btn-add-primary {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: var(--radius-md);
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .btn-add-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
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

    /* Cancellation Rule */
    .cancellation-rule {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        margin-bottom: 1rem;
    }

    .cancellation-rule .rule-field {
        flex: 1;
    }

    .cancellation-rule .btn-remove-rule {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: var(--radius-sm);
        padding: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cancellation-rule .btn-remove-rule:hover {
        background: #fca5a5;
        transform: scale(1.05);
    }

    /* Special Date Row */
    .special-date-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 0.75rem;
        align-items: flex-end;
        margin-bottom: 0.75rem;
        background: #f8fafc;
        padding: 0.75rem 1rem;
        border-radius: var(--radius-sm);
        border: 1px solid #e2e8f0;
    }

    .special-date-row .btn-remove-sd {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: var(--radius-sm);
        padding: 0.6rem 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }

    .special-date-row .btn-remove-sd:hover {
        background: #fca5a5;
        transform: scale(1.05);
    }

    /* File Input Styling */
    input[type="file"] {
        padding: 0.5rem;
        font-size: 0.8rem;
        background: white;
    }

    input[type="file"]::file-selector-button {
        padding: 0.4rem 1rem;
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s ease;
        margin-right: 1rem;
    }

    input[type="file"]::file-selector-button:hover {
        transform: scale(1.02);
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
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

        .gallery-grid {
            grid-template-columns: 1fr 1fr;
        }

        .room-grid {
            grid-template-columns: 1fr;
        }

        .cancellation-rule {
            flex-direction: column;
            align-items: stretch;
        }

        .cancellation-rule .btn-remove-rule {
            align-self: flex-end;
        }
    }

    @media (max-width: 480px) {
        .amenity-grid {
            grid-template-columns: 1fr;
        }

        .gallery-grid {
            grid-template-columns: 1fr;
        }

        .preview-grid {
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

<form action="{{ route('admin.properties.update', $property) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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
                    <input type="text" name="name" value="{{ old('name', $property->name) }}" required class="form-input" placeholder="e.g. Hilltop Villa & Suites" style="flex: 2;">
                </div>
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Category <span class="required-star">*</span></label>
                <select name="category_id" required class="form-input">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $property->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Destination <span class="required-star">*</span></label>
                <select name="destination_id" required class="form-input">
                    <option value="">Select Destination</option>
                    @foreach($destinations as $destination)
                        <option value="{{ $destination->id }}" {{ old('destination_id', $property->destination_id) == $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Assign to Vendor <span class="required-star">*</span></label>
                <select name="vendor_id" required class="form-input">
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('vendor_id', $property->vendor_id) == $vendor->id ? 'selected' : '' }}>{{ $vendor->user->name }} ({{ $vendor->user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group full-width">
                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1.5rem;">
                    <div>
                        <label class="form-label">Total Bedrooms <span class="required-star">*</span></label>
                        <input type="number" name="total_bedrooms" value="{{ old('total_bedrooms', $property->total_bedrooms) }}" required class="form-input" min="0">
                    </div>
                    <div>
                        <label class="form-label">Total Bathrooms <span class="required-star">*</span></label>
                        <input type="number" name="total_bathrooms" value="{{ old('total_bathrooms', $property->total_bathrooms) }}" required class="form-input" min="0">
                    </div>
                    <div>
                        <label class="form-label">Max Guests <span class="required-star">*</span></label>
                        <input type="number" name="max_guests" value="{{ old('max_guests', $property->max_guests) }}" required class="form-input" min="0">
                    </div>
                </div>
            </div>

            <div class="form-group full-width">
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" class="form-input" placeholder="Tell us about the property...">{{ old('description', $property->description) }}</textarea>
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
                <input type="text" name="city" value="{{ old('city', $property->city) }}" required class="form-input" placeholder="e.g. Panchgani">
            </div>
            <div class="form-group">
                <label class="form-label">State <span class="required-star">*</span></label>
                <input type="text" name="state" value="{{ old('state', $property->state) }}" required class="form-input" placeholder="e.g. Maharashtra">
            </div>
            <div class="form-group">
                <label class="form-label">Country <span class="required-star">*</span></label>
                <input type="text" name="country" value="{{ old('country', $property->country) }}" required class="form-input">
            </div>
            <div class="form-group">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                    <div>
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" value="{{ old('latitude', $property->latitude) }}" class="form-input" placeholder="e.g. 17.92">
                    </div>
                    <div>
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" value="{{ old('longitude', $property->longitude) }}" class="form-input" placeholder="e.g. 73.81">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- OPERATIONAL INFO -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Operational Info & Status
        </h3>
        <div class="form-grid">
            <div class="form-group">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                    <div>
                        <label class="form-label">Check-in After</label>
                        <input type="time" name="check_in_time" value="{{ old('check_in_time', $property->check_in_time) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Check-out Before</label>
                        <input type="time" name="check_out_time" value="{{ old('check_out_time', $property->check_out_time) }}" class="form-input">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Publishing Status <span class="required-star">*</span></label>
                <select name="status" required class="form-input">
                    <option value="pending" {{ old('status', $property->status) == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="active" {{ old('status', $property->status) == 'active' ? 'selected' : '' }}>Active / Live</option>
                    <option value="inactive" {{ old('status', $property->status) == 'inactive' ? 'selected' : '' }}>Inactive / Hidden</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Property Brochure <span class="help-text">(PDF)</span></label>
                <input type="file" name="brochure" accept="application/pdf" class="form-input">
                @if($property->brochure)
                    <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <a href="{{ Storage::url($property->brochure) }}" target="_blank" style="font-size: 0.8rem; color: #4338ca; text-decoration: underline; font-weight: 500;">View Current Brochure</a>
                    </div>
                @else
                    <p style="color:#94a3b8; font-size:0.7rem; margin-top:0.25rem;">No brochure uploaded yet (Max 10MB)</p>
                @endif
            </div>
            <div class="form-group">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                    <div>
                        <label class="form-label">GST %</label>
                        <input type="number" name="gst" value="{{ old('gst', $property->gst) }}" class="form-input" min="0" step="0.01">
                    </div>
                    <div>
                        <label class="form-label">Price / Amount</label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount', $property->amount) }}" class="form-input" placeholder="Amount">
                    </div>
                </div>
            </div>
            <div class="form-group full-width">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem;">
                    <div>
                        <label class="form-label">Extra Person Charge</label>
                        <input type="number" name="extra_person_charge" value="{{ old('extra_person_charge', $property->extra_person_charge) }}" class="form-input" min="0" step="0.01">
                    </div>
                    <div style="display: flex; align-items: flex-end; gap: 2rem; padding-bottom: 0.25rem;">
                        <label class="checkbox-group">
                            <input type="checkbox" name="show_on_homepage" value="1" {{ old('show_on_homepage', $property->show_on_homepage) ? 'checked' : '' }}>
                            <span>Show on Homepage</span>
                        </label>
                        <label class="checkbox-group">
                            <input type="checkbox" name="show_in_menu" value="1" {{ old('show_in_menu', $property->show_in_menu) ? 'checked' : '' }}>
                            <span>Show in Menu</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- INSTAGRAM VIDEOS -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
            Instagram Videos <span class="help-text">(Thumbnail + Video Link)</span>
        </h3>
        <div id="insta-links-container">
            @php $instaVideos = is_array($property->instagram_videos) ? $property->instagram_videos : []; @endphp
            @foreach($instaVideos as $index => $video)
                @php
                    $isOldFormat = !is_array($video);
                    $videoLink = $isOldFormat ? $video : ($video['video_link'] ?? '');
                    $videoImage = $isOldFormat ? null : ($video['image'] ?? null);
                @endphp
                <div class="dynamic-card">
                    <button type="button" class="btn-remove-dynamic" onclick="this.parentElement.remove()">✕</button>
                    <div style="display:grid; grid-template-columns:140px 1fr; gap:1.5rem;">
                        <div>
                            @if($videoImage)
                                <img src="{{ Storage::url($videoImage) }}" style="width:100%; height:80px; object-fit:cover; border-radius:var(--radius-sm); margin-bottom:0.5rem;">
                            @endif
                            <input type="hidden" name="existing_instagram_video_images[{{ $index }}]" value="{{ $videoImage ?? '' }}">
                            <input type="file" name="instagram_video_images[{{ $index }}]" class="form-input" style="font-size:0.7rem; padding:0.25rem;" accept="image/*">
                        </div>
                        <div style="display:grid; align-content:center; gap:0.5rem;">
                            <label class="form-label" style="font-size:0.75rem;">Video URL <span class="required-star">*</span></label>
                            <input type="url" name="instagram_video_links[{{ $index }}]" value="{{ $videoLink }}" class="form-input" placeholder="https://www.instagram.com/reels/XXXXX/" required>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn-add-primary" onclick="addInstaLink()">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Add Instagram Video
        </button>
    </div>

    <!-- NEARBY ATTRACTIONS -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Nearby Attractions
        </h3>
        <div id="attractions-container">
            @php $attractions = is_array($property->nearby_attractions) ? $property->nearby_attractions : []; @endphp
            @foreach($attractions as $index => $attr)
                <div class="dynamic-card">
                    <button type="button" class="btn-remove-dynamic" onclick="this.parentElement.remove()">✕</button>
                    <div style="display:grid; grid-template-columns:140px 1fr; gap:1.5rem;">
                        <div>
                            @if(isset($attr['image']) && $attr['image'])
                                <img src="{{ Storage::url($attr['image']) }}" style="width:100%; height:100px; object-fit:cover; border-radius:var(--radius-sm); margin-bottom:0.5rem;">
                            @endif
                            <input type="hidden" name="existing_attraction_images[]" value="{{ $attr['image'] ?? '' }}">
                            <input type="file" name="attraction_images[]" class="form-input" style="font-size:0.7rem; padding:0.25rem;">
                        </div>
                        <div style="display:grid; gap:0.75rem;">
                            <input type="text" name="attraction_headings[]" value="{{ $attr['heading'] ?? '' }}" class="form-input" placeholder="Heading (e.g. Table Land)">
                            <input type="text" name="attraction_alts[]" value="{{ $attr['alt_text'] ?? '' }}" class="form-input" placeholder="Image Alt Text">
                            <textarea name="attraction_descriptions[]" rows="2" class="form-input" placeholder="Description">{{ $attr['description'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn-add" onclick="addAttraction()">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Add Attraction
        </button>
    </div>

    <!-- AMENITIES -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.143-7.714L1 12l6.857-2.143L11 3z"></path></svg>
            Included Amenities
        </h3>
        <div class="amenity-grid">
            @php $pAmenities = $property->amenities->pluck('id')->toArray(); @endphp
            @foreach($amenities as $amenity)
                <label class="amenity-item">
                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" {{ in_array($amenity->id, old('amenities', $pAmenities)) ? 'checked' : '' }}>
                    @if($amenity->icon)
                        <img src="{{ Storage::url($amenity->icon) }}" style="width:18px; height:18px; object-fit:contain;">
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
            @php $pCollections = $property->collections->pluck('id')->toArray(); @endphp
            @foreach($collections as $collection)
                <label class="amenity-item">
                    <input type="checkbox" name="collections[]" value="{{ $collection->id }}" {{ in_array($collection->id, old('collections', $pCollections)) ? 'checked' : '' }}>
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
            $selectedMeals = old('meals', $property->meals ?? []);
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
            @php $cancellationRules = $property->cancellationRules ?? collect(); @endphp
            @foreach($cancellationRules as $index => $rule)
                <div class="cancellation-rule">
                    <div class="rule-field">
                        <label class="form-label">Days Before Check-in</label>
                        <input type="number" name="cancellation_rules[{{ $index }}][days_before]" value="{{ $rule->days_before }}" class="form-input" required min="0" placeholder="e.g. 5">
                    </div>
                    <div class="rule-field">
                        <label class="form-label">Deduction Percentage (%)</label>
                        <input type="number" name="cancellation_rules[{{ $index }}][deduction_percentage]" value="{{ $rule->deduction_percentage }}" class="form-input" required min="0" max="100" step="0.01" placeholder="e.g. 20">
                    </div>
                    <button type="button" class="btn-remove-rule" onclick="this.parentElement.remove()">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn-add" onclick="addCancellationRule()">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Add Cancellation Rule
        </button>
    </div>

    <!-- SPECIAL DATES (Weekend / Holiday Pricing) -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Special Date Pricing
        </h3>
        <p style="color:#64748b; font-size:0.875rem; margin-bottom:1.5rem;">Weekend ya holiday par alag price set karo. Yeh price API mein <code>special_dates</code> field mein ayega.</p>

        <div id="special-dates-container">
            @php $specialDates = $property->specialDates ?? collect(); @endphp
            @foreach($specialDates as $index => $sd)
                <div class="special-date-row">
                    <div>
                        <label class="form-label" style="font-size:0.78rem;">Date <span class="required-star">*</span></label>
                        <input type="date" name="special_dates[{{ $index }}][date]" value="{{ $sd->date->format('Y-m-d') }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label" style="font-size:0.78rem;">Amount (₹) <span class="required-star">*</span></label>
                        <input type="number" name="special_dates[{{ $index }}][amount]" value="{{ $sd->amount }}" class="form-input" min="0" step="0.01" required placeholder="e.g. 15000">
                    </div>
                    <div>
                        <label class="form-label" style="font-size:0.78rem;">Label</label>
                        <input type="text" name="special_dates[{{ $index }}][label]" value="{{ $sd->label }}" class="form-input" placeholder="e.g. Weekend, Diwali">
                    </div>
                    <button type="button" class="btn-remove-sd" onclick="this.closest('.special-date-row').remove()" title="Remove">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endforeach
        </div>

        <button type="button" class="btn-add" onclick="addSpecialDate()">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Add Special Date
        </button>
    </div>

    <!-- IMAGES -->
    <div class="form-section">
        <h3 class="section-title">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Property Gallery
        </h3>
        
        <div class="form-group">
            <label class="form-label">Existing Images & Descriptions</label>
            <div class="gallery-grid">
                @foreach($property->gallery ?? [] as $index => $item)
                    @php 
                        $imgPath = is_array($item) ? ($item['image'] ?? '') : $item;
                        $imgAlt = is_array($item) ? ($item['alt'] ?? '') : '';
                        $displayUrl = (strpos($imgPath, 'http') === 0) ? $imgPath : Storage::url($imgPath);
                    @endphp
                    <div class="gallery-card" id="img-card-{{ $index }}">
                        <img src="{{ $displayUrl }}" alt="{{ $imgAlt }}">
                        <div class="card-body">
                            <label>Alt Text</label>
                            <input type="text" name="existing_alts[{{ $index }}]" value="{{ $imgAlt }}" placeholder="Image description">
                        </div>
                        <button type="button" class="btn-remove-image" onclick="deletePropertyImage('{{ $imgPath }}', {{ $index }})">✕</button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-group" style="margin-top: 3rem;">
            <label class="form-label">Add More Images</label>
            <div class="drop-zone">
                <input type="file" name="images[]" multiple accept="image/*" id="images-input" style="display: none;">
                <label for="images-input" class="upload-label">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Select New Photos
                </label>
                <p class="hint-text">Upload HD quality images for better conversion</p>
                <div id="image-preview-container" class="preview-grid"></div>
            </div>
        </div>
    </div>

    <!-- ROOMS & UNITS -->
    <div class="form-section" style="margin-top:0;">
        <h3 class="section-title" style="justify-content: space-between;">
            <span style="display:flex;align-items:center;gap:0.5rem;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Rooms &amp; Units
                <span class="badge-count">{{ $property->rooms()->count() }} Rooms</span>
            </span>
            <a href="{{ route('admin.properties.rooms.create', $property) }}" class="btn-add-primary" style="font-size:0.8rem; padding:0.45rem 1.1rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                Add New Room
            </a>
        </h3>

        @php $rooms = $property->rooms()->latest()->get(); @endphp

        @if($rooms->isEmpty())
            <div class="empty-state">
                <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <p class="title">No rooms yet</p>
                <p class="subtitle">Click <strong>Add New Room</strong> above to add rooms for this property.</p>
            </div>
        @else
            <div class="room-grid">
                @foreach($rooms as $room)
                    @php
                        $firstImage = collect($room->images ?? [])
                            ->map(fn($img) => is_array($img) ? $img : ['path' => $img, 'alt' => ''])
                            ->first();
                    @endphp
                    <div class="room-card-item">
                        @if($firstImage && $firstImage['path'])
                            <img src="{{ Storage::url($firstImage['path']) }}" alt="{{ $firstImage['alt'] }}" class="room-image">
                        @else
                            <div class="room-placeholder">
                                <svg width="40" height="40" fill="none" stroke="#a5b4fc" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            </div>
                        @endif
                        <div class="room-body">
                            <div class="room-title">{{ $room->title }}</div>
                            @if($room->bed_type)
                                <div class="room-bed">🛏 {{ $room->bed_type }}</div>
                            @endif
                            @if(!empty($room->meals))
                                <div class="room-meals">🍽 {{ implode(', ', $room->meals) }}</div>
                            @endif
                            <div class="room-meta">
                                {{ count($room->images ?? []) }} image(s)
                                &nbsp;•&nbsp;
                                <span class="room-status {{ $room->is_active ? 'active' : 'inactive' }}">
                                    {{ $room->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="room-actions">
                                <a href="{{ route('admin.rooms.edit', $room) }}" class="btn-edit">Edit</a>
                                <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Delete this room?')" style="flex:1;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="action-buttons">
        <a href="{{ route('admin.properties.index') }}" class="btn-cancel">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            Cancel
        </a>
        <button type="submit" class="btn-submit">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
            Update Property
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // Preview logic for new images
    document.getElementById('images-input').addEventListener('change', function(e) {
        const container = document.getElementById('image-preview-container');
        container.innerHTML = '';
        Array.from(e.target.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <input type="text" name="alts[]" value="${file.name.split('.')[0]}" placeholder="Alt text">
                `;
                container.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    });

    async function deletePropertyImage(imagePath, index) {
        if(!confirm('Are you sure you want to remove this image?')) return;
        try {
            const response = await fetch(`/admin/properties/image/{{ $property->id }}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ path: imagePath })
            });
            const data = await response.json();
            if(data.success) document.getElementById(`img-card-${index}`).remove();
        } catch (error) { alert('Failed to delete image'); }
    }

    let instaIdx = {{ count($instaVideos) }};
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

    function addAttraction() {
        const container = document.getElementById('attractions-container');
        const card = document.createElement('div');
        card.className = 'dynamic-card';
        card.innerHTML = `
            <button type="button" class="btn-remove-dynamic" onclick="this.parentElement.remove()">✕</button>
            <div style="display:grid; grid-template-columns:140px 1fr; gap:1.5rem;">
                <div>
                    <label class="form-label" style="font-size:0.75rem;">New Image</label>
                    <input type="file" name="attraction_images[]" class="form-input" style="font-size:0.7rem; padding:0.25rem;">
                </div>
                <div style="display:grid; gap:0.75rem;">
                    <input type="text" name="attraction_headings[]" class="form-input" placeholder="Heading (e.g. Table Land)">
                    <input type="text" name="attraction_alts[]" class="form-input" placeholder="Image Alt Text">
                    <textarea name="attraction_descriptions[]" rows="2" class="form-input" placeholder="Description"></textarea>
                </div>
            </div>
        `;
        container.appendChild(card);
    }

    let cancelIdx = {{ isset($cancellationRules) ? count($cancellationRules) : 0 }};
    function addCancellationRule() {
        const container = document.getElementById('cancellation-rules-container');
        const card = document.createElement('div');
        card.className = 'cancellation-rule';
        card.innerHTML = `
            <div class="rule-field">
                <label class="form-label">Days Before Check-in</label>
                <input type="number" name="cancellation_rules[${cancelIdx}][days_before]" class="form-input" placeholder="e.g. 5" required min="0">
            </div>
            <div class="rule-field">
                <label class="form-label">Deduction Percentage (%)</label>
                <input type="number" name="cancellation_rules[${cancelIdx}][deduction_percentage]" class="form-input" placeholder="e.g. 20" required min="0" max="100" step="0.01">
            </div>
            <button type="button" class="btn-remove-rule" onclick="this.parentElement.remove()">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;
        container.appendChild(card);
        cancelIdx++;
    }

    let sdIdx = {{ isset($specialDates) ? count($specialDates) : 0 }};
    function addSpecialDate() {
        const container = document.getElementById('special-dates-container');
        const row = document.createElement('div');
        row.className = 'special-date-row';
        row.innerHTML = `
            <div>
                <label class="form-label" style="font-size:0.78rem;">Date <span class="required-star">*</span></label>
                <input type="date" name="special_dates[${sdIdx}][date]" class="form-input" required>
            </div>
            <div>
                <label class="form-label" style="font-size:0.78rem;">Amount (₹) <span class="required-star">*</span></label>
                <input type="number" name="special_dates[${sdIdx}][amount]" class="form-input" min="0" step="0.01" required placeholder="e.g. 15000">
            </div>
            <div>
                <label class="form-label" style="font-size:0.78rem;">Label</label>
                <input type="text" name="special_dates[${sdIdx}][label]" class="form-input" placeholder="e.g. Weekend, Diwali">
            </div>
            <button type="button" class="btn-remove-sd" onclick="this.closest('.special-date-row').remove()" title="Remove">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;
        container.appendChild(row);
        sdIdx++;
    }
</script>
@endsection