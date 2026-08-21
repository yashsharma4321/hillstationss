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



<script>
    CKEDITOR.replace('property-description-editor', {
        height: 260,
        removeButtons: 'About',
        allowedContent: true
    });

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

    let instaIdx = {{ isset($instaVideos) && is_countable($instaVideos) ? count($instaVideos) : 0 }};
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
                    <input type="url" name="instagram_video_links[${instaIdx}]" class="form-input" placeholder="https://www.instagram.com/reels/XXXXX/">
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
                <input type="number" name="cancellation_rules[${cancelIdx}][days_before]" class="form-input" placeholder="e.g. 5" min="0">
            </div>
            <div class="rule-field">
                <label class="form-label">Deduction Percentage (%)</label>
                <input type="number" name="cancellation_rules[${cancelIdx}][deduction_percentage]" class="form-input" placeholder="e.g. 20" min="0" max="100" step="0.01">
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
                <input type="date" name="special_dates[${sdIdx}][date]" class="form-input">
            </div>
            <div>
                <label class="form-label" style="font-size:0.78rem;">Amount (₹) <span class="required-star">*</span></label>
                <input type="number" name="special_dates[${sdIdx}][amount]" class="form-input" min="0" step="0.01" placeholder="e.g. 15000">
            </div>
            
                    <div>
                        <label class="form-label" style="font-size:0.78rem;">Status</label>
                        <select name="special_dates[${sdIdx}][is_open]" class="form-input">
                            <option value="1" ${isOpen == '1' ? 'selected' : ''}>Open</option>
                            <option value="0" ${isOpen == '0' ? 'selected' : ''}>Closed</option>
                        </select>
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

    function togglePetChargeFields() {
        const petsAllowed = document.getElementById('pets_allowed_select').value;
        const typeContainer = document.getElementById('pet_charge_type_container');
        const chargeContainer = document.getElementById('pet_charge_container');
        
        if (petsAllowed == '1') {
            typeContainer.style.display = 'block';
            togglePetChargeAmountField();
        } else {
            typeContainer.style.display = 'none';
            chargeContainer.style.display = 'none';
        }
    }

    function togglePetChargeAmountField() {
        const chargeType = document.getElementById('pet_charge_type_select').value;
        const chargeContainer = document.getElementById('pet_charge_container');
        
        if (chargeType === 'chargeable') {
            chargeContainer.style.display = 'block';
        } else {
            chargeContainer.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('pets_allowed_select')) {
            togglePetChargeFields();
        }
    });
</script>




<script>
    function openInternalBulkModal() {
        if (typeof renderRealCalendar === 'function') {
            renderRealCalendar();
        }
        document.getElementById('mainFormView').style.display = 'none';
        document.getElementById('calendarView').style.display = 'block';
    }
    function closeInternalBulkModal() {
        document.getElementById('calendarView').style.display = 'none';
        document.getElementById('mainFormView').style.display = 'block';
    }
    function generateSpecialDates() {
        const fromDate = document.getElementById('ib_from').value;
        const toDate = document.getElementById('ib_to').value;
        const amount = document.getElementById('ib_amount').value;
        const isOpen = document.querySelector('input[name="ib_is_open"]:checked').value;
        
        if(!fromDate || !toDate) {
            alert('Please fill from date and to date.');
            return;
        }

        const start = new Date(fromDate);
        const end = new Date(toDate);
        if(start > end) {
            alert('From date must be before or equal to To date.');
            return;
        }

        const daysChecked = Array.from(document.querySelectorAll('.ib_days:checked')).map(cb => parseInt(cb.value));
        if(daysChecked.length === 0) {
            alert('Please select at least one day.');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('property_ids[]', '{{ $property->id }}');
        formData.append('from_date', fromDate);
        formData.append('to_date', toDate);
        formData.append('amount', amount);
        formData.append('is_open', isOpen);
        
        daysChecked.forEach(d => formData.append('days[]', d));

        fetch('{{ route("admin.properties.bulk_special_dates") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'An error occurred.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Request failed.');
        });
    }
</script>
@endsection
