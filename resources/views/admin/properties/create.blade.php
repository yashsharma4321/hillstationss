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



<script>
    CKEDITOR.replace('property-description-editor', {
        height: 260,
        removeButtons: 'About',
        allowedContent: true
    });

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
        addInstaLink();
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
        const label = document.getElementById('ib_label').value;
        
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

        let addedCount = 0;
        const container = document.getElementById('special-dates-container');

        let currentDate = new Date(start);
        while(currentDate <= end) {
            if(daysChecked.includes(currentDate.getDay())) {
                const year = currentDate.getFullYear();
                const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                const day = String(currentDate.getDate()).padStart(2, '0');
                const dateStr = `${year}-${month}-${day}`;
                
                const row = document.createElement('div');
                row.className = 'special-date-row';
                row.innerHTML = `
            <div>
                <label class="form-label" style="font-size:0.78rem;">Date <span class="required-star">*</span></label>
                <input type="date" name="special_dates[${sdIdx}][date]" ${typeof dateStr !== 'undefined' ? 'value="' + dateStr + '"' : ''} class="form-input">
            </div>
            <div>
                <label class="form-label" style="font-size:0.78rem;">Amount (₹) <span class="required-star">*</span></label>
                <input type="number" name="special_dates[${sdIdx}][amount]" ${typeof amount !== 'undefined' ? 'value="' + amount + '"' : ''} class="form-input" min="0" step="0.01" placeholder="e.g. 15000">
            </div>
            <div>
                <label class="form-label" style="font-size:0.78rem;">Status</label>
                <select name="special_dates[${sdIdx}][is_open]" class="form-input">
                    <option value="1" ${typeof isOpen !== 'undefined' && isOpen == '1' ? 'selected' : ''}>Open</option>
                    <option value="0" ${typeof isOpen !== 'undefined' && isOpen == '0' ? 'selected' : ''}>Closed</option>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size:0.78rem;">Label</label>
                <input type="text" name="special_dates[${sdIdx}][label]" ${typeof label !== 'undefined' ? 'value="' + label + '"' : ''} class="form-input" placeholder="e.g. Weekend, Diwali">
            </div>
            <button type="button" class="btn-remove-sd" onclick="this.closest('.special-date-row').remove()" title="Remove">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;
                container.appendChild(row);
                sdIdx++;
                addedCount++;
            }
            currentDate.setDate(currentDate.getDate() + 1);
        }

        closeInternalBulkModal();
        if(addedCount > 0) {
            alert(`Successfully generated ${addedCount} special dates.`);
        } else {
            alert('No dates matched the selected days in the given range.');
        }
    }
</script>
@endsection
