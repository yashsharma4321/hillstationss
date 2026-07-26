@extends('layouts.admin')

@section('header', 'Edit Page')

@section('styles')
    <style>
        .block-selector {
            position: relative;
        }

        .block-selector-btn {
            padding: 0.5rem 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .block-options {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            background: white;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            z-index: 10;
            min-width: 200px;
            overflow: hidden;
        }

        .block-options.show {
            display: block;
        }

        .block-option-btn {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.75rem 1rem;
            background: none;
            border: none;
            border-bottom: 1px solid var(--border);
            font-size: 0.875rem;
            cursor: pointer;
            color: var(--text-main);
        }

        .block-option-btn:hover {
            background: #f8fafc;
            color: var(--primary);
        }

        .block-option-btn:last-child {
            border-bottom: none;
        }

        .section-block {
            background: white;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .remove-btn {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            font-family: inherit;
            font-size: 0.875rem;
        }

        .nested-list {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px dashed var(--border);
        }
    </style>
@endsection

@section('content')
    <div class="content-card">
        <div class="card-header">
            <h2 style="font-size: 1.125rem; font-weight: 600;">Edit Page: {{ $page->title }}</h2>
            <a href="{{ route('admin.pages.index') }}"
                style="color: var(--secondary); text-decoration: none; font-size: 0.875rem;">← Back to Pages</a>
        </div>

        @if ($errors->any())
            <div style="padding: 1rem; background: #fee2e2; color: #991b1b; border-bottom: 1px solid #fecaca;">
                <ul style="margin-left: 1.5rem; font-size: 0.875rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.pages.update', $page) }}" method="POST" enctype="multipart/form-data"
            style="padding: 2rem;">
            @csrf
            @method('PUT')

            <div
                style="background: #f8fafc; padding: 1.5rem; border-radius: 0.5rem; border: 1px solid var(--border); margin-bottom: 2rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-main);">Basic
                    Information</h3>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Page Title <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $page->title) }}" required class="form-input"
                        placeholder="e.g. About Us">
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Short Description</label>
                    <textarea name="description" rows="3" class="form-input" style="resize: vertical;"
                        placeholder="Brief summary of the page content...">{{ old('description', $page->description) }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Banner Image</label>
                        @if($page->banner_image)
                            <div style="margin-bottom: 0.5rem;">
                                <img src="{{ Storage::url($page->banner_image) }}" alt="Preview"
                                    style="height: 64px; border-radius: 0.25rem;">
                            </div>
                        @endif
                        <input type="file" name="banner_image" accept="image/*" class="form-input"
                            style="padding: 0.6rem;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Banner Image Alt Text</label>
                        <input type="text" name="banner_alt_text" value="{{ old('banner_alt_text', $page->banner_alt_text) }}" class="form-input"
                            placeholder="Description for screen readers">
                    </div>
                </div>
            <div
                style="background: white; padding: 1.5rem; border-radius: 0.5rem; border: 1px solid var(--border); margin-bottom: 2rem;">
                <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-main);">SEO Settings (Optional)</h3>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" class="form-input" placeholder="SEO Title">
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Meta Description</label>
                    <textarea name="meta_description" rows="3" class="form-input" placeholder="Brief description for search engines...">{{ old('meta_description', $page->meta_description) }}</textarea>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords) }}" class="form-input" placeholder="Keyword 1, Keyword 2...">
                </div>

                <div class="form-group">
                    <label class="form-label">JSON-LD Schema Markup</label>
                    <textarea name="schema" rows="5" class="form-input" style="font-family: monospace; font-size: 0.8rem;" placeholder='{ "@context": "https://schema.org", "@type": "WebPage", ... }'>{{ old('schema', is_array($page->schema) ? json_encode($page->schema, JSON_PRETTY_PRINT) : $page->schema) }}</textarea>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Paste your JSON schema here. It will be validated on save.</p>
                </div>
            </div>
        </div>

            <div style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--text-main);">Page Content Builder</h3>
                    <div class="block-selector">
                        <button type="button" class="block-selector-btn"
                            onclick="document.getElementById('blockOptions').classList.toggle('show')">
                            + Add Section
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>
                        <div id="blockOptions" class="block-options">
                            <button type="button" class="block-option-btn" data-type="text">📝 Text Block</button>
                            <button type="button" class="block-option-btn" data-type="image">🖼️ Single Image</button>
                            <button type="button" class="block-option-btn" data-type="image_points">✓ Image with
                                Points</button>
                            <button type="button" class="block-option-btn" data-type="video">▶️ Video Embed</button>
                            <button type="button" class="block-option-btn" data-type="faq">❓ FAQ Accordion</button>
                            <button type="button" class="block-option-btn" data-type="gallery">📸 Image Gallery</button>
                            <button type="button" class="block-option-btn" data-type="carousel">🎠 Carousel</button>
                            <button type="button" class="block-option-btn" data-type="feature_grid">📜 Feature Grid</button>
                            <button type="button" class="block-option-btn" data-type="stats_grid">📊 Stats Grid</button>
                            <button type="button" class="block-option-btn" data-type="best_rates">🏠 Best Rates Grid</button>
                            <button type="button" class="block-option-btn" data-type="featured_properties">🏢 Featured Properties</button>
                            <button type="button" class="block-option-btn" data-type="featured_destinations">📍 Featured Destinations</button>
                            <button type="button" class="block-option-btn" data-type="multi_text">📝 Multi-Text Section</button>
                        </div>
                    </div>
                </div>

                <div id="sections-container">
                    @php
                        $existingSections = $page->detail ? ($page->detail->json_data['sections'] ?? []) : [];
                    @endphp
                    @foreach($existingSections as $index => $section)
                        @php
                            $type = $section['type'] ?? 'text';
                        @endphp
                        <div class="section-block">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border);">
                                <h4 style="font-size: 1rem; font-weight: 600; color: var(--primary);">
                                    <span
                                        style="background: #f1f5f9; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; margin-right: 0.5rem; color: var(--secondary);">Block</span>
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </h4>
                                <button type="button" class="remove-btn">✖ Remove</button>
                            </div>

                            <input type="hidden" name="sections[{{ $index }}][type]" value="{{ $type }}">

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">Section Key (Unique ID) <span
                                            style="color:var(--danger)">*</span></label>
                                    <input type="text" name="sections[{{ $index }}][key]" value="{{ $section['key'] ?? '' }}"
                                        class="form-input" placeholder="e.g. hero_banner" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Block Title (Optional)</label>
                                    <input type="text" name="sections[{{ $index }}][title]"
                                        value="{{ $section['title'] ?? '' }}" class="form-input"
                                        placeholder="e.g. Our Services">
                                </div>
                            </div>

                            @if($type === 'text')
                                <div class="form-group">
                                    <label class="form-label">Image (Optional)</label>
                                    @if(isset($section['image']) && $section['image'])
                                        <div style="margin-bottom:0.5rem">
                                            <img src="{{ Storage::url($section['image']) }}"
                                                style="height:64px; border-radius:0.25rem;">
                                            <input type="hidden" name="sections[{{ $index }}][existing_image]"
                                                value="{{ $section['image'] }}">
                                        </div>
                                    @endif
                                    <input type="file" name="sections[{{ $index }}][image]" accept="image/*" class="form-input"
                                        style="padding:0.6rem">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Content</label>
                                    <textarea name="sections[{{ $index }}][content]" rows="4" class="form-input rich-editor"
                                        style="resize:vertical;">{{ $section['content'] ?? '' }}</textarea>
                                </div>
                            @elseif($type === 'image')
                                <div class="form-group">
                                    <label class="form-label">Image</label>
                                    @if(isset($section['image']) && $section['image'])
                                        <div style="margin-bottom:0.5rem">
                                            <img src="{{ Storage::url($section['image']) }}"
                                                style="height:64px; border-radius:0.25rem;">
                                            <input type="hidden" name="sections[{{ $index }}][existing_image]"
                                                value="{{ $section['image'] }}">
                                        </div>
                                    @endif
                                    <input type="file" name="sections[{{ $index }}][image]" accept="image/*" class="form-input"
                                        style="padding:0.6rem">
                                </div>
                            @elseif($type === 'video')
                                <div class="form-group">
                                    <label class="form-label">Video URL or Embed Code</label>
                                    <input type="text" name="sections[{{ $index }}][url]" value="{{ $section['url'] ?? '' }}"
                                        class="form-input" placeholder="e.g. https://youtube.com/watch?v=...">
                                </div>
                            @elseif($type === 'image_points')
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                    <div class="form-group">
                                        <label class="form-label">Image</label>
                                        @if(isset($section['image']) && $section['image'])
                                            <div style="margin-bottom:0.5rem">
                                                <img src="{{ Storage::url($section['image']) }}"
                                                    style="height:64px; border-radius:0.25rem;">
                                                <input type="hidden" name="sections[{{ $index }}][existing_image]"
                                                    value="{{ $section['image'] }}">
                                            </div>
                                        @endif
                                        <input type="file" name="sections[{{ $index }}][image]" accept="image/*" class="form-input"
                                            style="padding:0.6rem">
                                    </div>
                                    <div class="form-group nested-list" data-idx="{{ $index }}">
                                        <label class="form-label">Bullet Points</label>
                                        <div class="points-container">
                                            @foreach($section['points'] ?? [] as $point)
                                                <input type="text" name="sections[{{ $index }}][points][]" value="{{ $point }}"
                                                    class="form-input" style="margin-bottom:0.5rem" placeholder="Point">
                                            @endforeach
                                            <!-- Always have at least one blank if none exist? -->
                                        </div>
                                        <button type="button" class="add-point-btn"
                                            style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add Point</button>
                                    </div>
                                </div>
                            @elseif($type === 'faq')
                                <div class="form-group nested-list" data-idx="{{ $index }}">
                                    <label class="form-label">Questions & Answers</label>
                                    <div class="faqs-container">
                                        @foreach($section['items'] ?? [] as $fIdx => $item)
                                            <div
                                                style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                                                <button type="button" class="remove-element-btn"
                                                    style="position:absolute; top: -0.5rem; right: -0.5rem; background: var(--danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer;">X</button>
                                                <input type="text" name="sections[{{ $index }}][items][{{ $fIdx }}][q]"
                                                    value="{{ $item['q'] ?? '' }}" class="form-input"
                                                    style="margin-bottom:0.5rem; font-weight:600;" placeholder="Question">
                                                <textarea name="sections[{{ $index }}][items][{{ $fIdx }}][a]" class="form-input"
                                                    rows="2" placeholder="Answer">{{ $item['a'] ?? '' }}</textarea>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="add-faq-btn"
                                        style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add QA Pair</button>
                                </div>
                            @elseif($type === 'gallery')
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                    <div class="form-group">
                                        <label class="form-label">Label 1</label>
                                        <input type="text" name="sections[{{ $index }}][label1]" value="{{ $section['label1'] ?? '' }}" class="form-input" placeholder="e.g. Luxury Villas">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Label 2</label>
                                        <input type="text" name="sections[{{ $index }}][label2]" value="{{ $section['label2'] ?? '' }}" class="form-input" placeholder="e.g. 50+ Completed">
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom: 1rem;">
                                    <label class="form-label">Gallery Introduction/Content</label>
                                    <textarea name="sections[{{ $index }}][content]" rows="3" class="form-input rich-editor">{{ $section['content'] ?? '' }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Existing Images</label>
                                    <div style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-bottom:1rem;">
                                        @foreach($section['images'] ?? [] as $gImg)
                                            <div
                                                style="position:relative; width: 80px; height: 80px; background:#f1f5f9; border-radius:0.25rem; overflow:hidden;">
                                                <img src="{{ Storage::url($gImg) }}" style="width:100%; height:100%; object-fit:cover;">
                                                <input type="hidden" name="sections[{{ $index }}][existing_images][]"
                                                    value="{{ $gImg }}">
                                                <button type="button" class="remove-element-btn"
                                                    style="position:absolute; top:0; right:0; background:rgba(239,68,68,0.9); color:white; border:none; width:20px; height:20px; font-size:10px; cursor:pointer;"
                                                    title="Remove this image">X</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <label class="form-label">Upload Additional Images</label>
                                    <input type="file" name="sections[{{ $index }}][images][]" multiple accept="image/*"
                                        class="form-input" style="padding:0.6rem">
                                </div>
                            @elseif($type === 'carousel')
                                <div class="form-group nested-list" data-idx="{{ $index }}">
                                    <label class="form-label">Carousel Items</label>
                                    <div class="carousel-items-container">
                                        @foreach($section['items'] ?? [] as $iIdx => $item)
                                            <div
                                                style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                                                <button type="button" class="remove-element-btn"
                                                    style="position:absolute; top: -0.5rem; right: -0.5rem; background: var(--danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; z-index:1;">X</button>
                                                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:1rem;">
                                                    <div class="form-group">
                                                        <label class="form-label">Image</label>
                                                        @if(isset($item['image']) && $item['image'])
                                                            <div style="margin-bottom:0.5rem">
                                                                <img src="{{ Storage::url($item['image']) }}"
                                                                    style="height:64px; border-radius:0.25rem;">
                                                                <input type="hidden"
                                                                    name="sections[{{ $index }}][items][{{ $iIdx }}][existing_image]"
                                                                    value="{{ $item['image'] }}">
                                                            </div>
                                                        @endif
                                                        <input type="file" name="sections[{{ $index }}][items][{{ $iIdx }}][image]"
                                                            accept="image/*" class="form-input" style="padding:0.5rem">
                                                    </div>
                                                    <div>
                                                        <input type="text" name="sections[{{ $index }}][items][{{ $iIdx }}][title]"
                                                            value="{{ $item['title'] ?? '' }}" class="form-input"
                                                            style="margin-bottom:0.5rem; font-weight:600;" placeholder="Item Title">
                                                        <textarea name="sections[{{ $index }}][items][{{ $iIdx }}][description]"
                                                            class="form-input" rows="2"
                                                            placeholder="Item Description">{{ $item['description'] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="add-carousel-item-btn"
                                        style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add Carousel
                                        Item</button>
                                </div>
                            @elseif($type === 'feature_grid')
                                <div class="form-group">
                                    <label class="form-label">Block Description (Optional)</label>
                                    <textarea name="sections[{{ $index }}][description]" class="form-input" rows="2" placeholder="Section subtitle or introduction...">{{ $section['description'] ?? '' }}</textarea>
                                </div>
                                <div class="form-group nested-list" data-idx="{{ $index }}">
                                    <label class="form-label">Items (Image, Alt, Title, Description)</label>
                                    <div class="grid-items-container">
                                        @foreach($section['items'] ?? [] as $iIdx => $item)
                                            <div style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                                                <button type="button" class="remove-element-btn" style="position:absolute; top: -0.5rem; right: -0.5rem; background: var(--danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; z-index:1;">X</button>
                                                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:1rem;">
                                                    <div class="form-group">
                                                        <label class="form-label">Image</label>
                                                        @if(isset($item['image']) && $item['image'])
                                                            <div style="margin-bottom:0.5rem">
                                                                  <img src="{{ Storage::url($item['image']) }}" style="height:64px; border-radius:0.25rem;">
                                                                <input type="hidden" name="sections[{{ $index }}][items][{{ $iIdx }}][existing_image]" value="{{ $item['image'] }}">
                                                            </div>
                                                        @endif
                                                        <input type="file" name="sections[{{ $index }}][items][{{ $iIdx }}][image]" accept="image/*" class="form-input" style="padding:0.5rem">
                                                        <input type="text" name="sections[{{ $index }}][items][{{ $iIdx }}][alt]" value="{{ $item['alt'] ?? '' }}" class="form-input" style="margin-top:0.5rem; font-size: 0.8rem;" placeholder="Image Alt Text">
                                                    </div>
                                                    <div>
                                                        <input type="text" name="sections[{{ $index }}][items][{{ $iIdx }}][title]" value="{{ $item['title'] ?? '' }}" class="form-input" style="margin-bottom:0.5rem; font-weight:600;" placeholder="Item Title">
                                                        <textarea name="sections[{{ $index }}][items][{{ $iIdx }}][description]" class="form-input" rows="2" placeholder="Item Description">{{ $item['description'] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="add-grid-item-btn" style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add Item</button>
                                </div>
                            @elseif($type === 'stats_grid')
                                <div class="form-group">
                                    <label class="form-label">Background Image (Optional)</label>
                                    @if(isset($section['background_image']) && $section['background_image'])
                                        <div style="margin-bottom:0.5rem">
                                            <img src="{{ Storage::url($section['background_image']) }}"
                                                style="height:64px; border-radius:0.25rem;">
                                            <input type="hidden" name="sections[{{ $index }}][existing_background_image]"
                                                value="{{ $section['background_image'] }}">
                                        </div>
                                    @endif
                                    <input type="file" name="sections[{{ $index }}][background_image]" accept="image/*" class="form-input"
                                        style="padding:0.6rem">
                                </div>
                                <div class="form-group nested-list" data-idx="{{ $index }}">
                                    <label class="form-label">Stat Items (Icon, Value, Label)</label>
                                    <div class="stats-items-container">
                                        @foreach($section['items'] ?? [] as $iIdx => $item)
                                            <div style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                                                <button type="button" class="remove-element-btn" style="position:absolute; top: -0.5rem; right: -0.5rem; background: var(--danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; z-index:1;">X</button>
                                                <div style="display:grid; grid-template-columns: 80px 1fr; gap:1rem;">
                                                    <div class="form-group">
                                                        @if(isset($item['image']) && $item['image'])
                                                            <div style="margin-bottom:0.5rem">
                                                                <img src="{{ Storage::url($item['image']) }}" style="height:40px; border-radius:0.25rem;">
                                                                <input type="hidden" name="sections[{{ $index }}][items][{{ $iIdx }}][existing_image]" value="{{ $item['image'] }}">
                                                            </div>
                                                        @endif
                                                        <input type="file" name="sections[{{ $index }}][items][{{ $iIdx }}][image]" accept="image/*" class="form-input" style="padding:0.3rem">
                                                    </div>
                                                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:0.5rem;">
                                                        <input type="text" name="sections[{{ $index }}][items][{{ $iIdx }}][title]" value="{{ $item['title'] ?? '' }}" class="form-input" placeholder="e.g. 100+">
                                                        <input type="text" name="sections[{{ $index }}][items][{{ $iIdx }}][label]" value="{{ $item['label'] ?? '' }}" class="form-input" placeholder="e.g. Happy Guests">
                                                        <input type="text" name="sections[{{ $index }}][items][{{ $iIdx }}][link]" value="{{ $item['link'] ?? '' }}" class="form-input" placeholder="Link (Optional)">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="add-stats-item-btn" style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add Stat</button>
                                </div>
                            @elseif($type === 'best_rates')
                                <div class="form-group">
                                    <label class="form-label">Sub-title (Optional)</label>
                                    <input type="text" name="sections[{{ $index }}][subtitle]" value="{{ $section['subtitle'] ?? '' }}" class="form-input" placeholder="Block sub-title">
                                </div>
                            @elseif($type === 'featured_properties')
                                <div class="form-group">
                                    <label class="form-label">Sub-title (Optional)</label>
                                    <input type="text" name="sections[{{ $index }}][subtitle]" value="{{ $section['subtitle'] ?? '' }}" class="form-input" placeholder="Block sub-title">
                                </div>
                                <div class="form-group nested-list" data-idx="{{ $index }}">
                                    <label class="form-label">BHK Filters (Numbers only, e.g. 3, 4, 6)</label>
                                    <div class="bhk-container" style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-bottom:0.5rem;">
                                        @foreach($section['bhks'] ?? [] as $bhk)
                                            <div style="position:relative;">
                                                <input type="number" name="sections[{{ $index }}][bhks][]" value="{{ $bhk }}" class="form-input" style="width:80px" placeholder="BHK">
                                                <button type="button" class="remove-element-btn" style="position:absolute; top:-5px; right:-5px; background:red; color:white; border:none; border-radius:50%; width:15px; height:15px; font-size:8px; cursor:pointer;">x</button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="add-bhk-btn" style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add BHK Tab</button>
                                </div>
                            @elseif($type === 'featured_destinations')
                                <div class="form-group">
                                    <label class="form-label">Sub-title (Optional)</label>
                                    <input type="text" name="sections[{{ $index }}][subtitle]" value="{{ $section['subtitle'] ?? '' }}" class="form-input" placeholder="Block sub-title">
                                 </div>
                            @elseif($type === 'multi_text')
                                <div class="form-group nested-list" data-idx="{{ $index }}">
                                    <label class="form-label">Multi-Text Items (Title & Content)</label>
                                    <div class="multi-text-container">
                                        @foreach($section['items'] ?? [] as $mIdx => $item)
                                            <div style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                                                <button type="button" class="remove-element-btn" style="position:absolute; top: -0.5rem; right: -0.5rem; background: var(--danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; z-index:1;">X</button>
                                                <div class="form-group">
                                                    <label class="form-label">Item Title</label>
                                                    <input type="text" name="sections[{{ $index }}][items][{{ $mIdx }}][title]" value="{{ $item['title'] ?? '' }}" class="form-input" placeholder="Enter title...">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Item Content</label>
                                                    <textarea name="sections[{{ $index }}][items][{{ $mIdx }}][content]" class="form-input rich-editor" rows="4">{{ $item['content'] ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="add-multi-text-item-btn" style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add Text Item</button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <p id="empty-sections-msg"
                    style="text-align: center; color: var(--text-muted); padding: 3rem; border: 2px dashed var(--border); border-radius: 0.5rem; font-size: 0.875rem; display:{{ count($existingSections) > 0 ? 'none' : 'block' }}">
                    This page is empty. Start building your layout by clicking "+ Add Section".
                </p>
            </div>

            <div
                style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                <a href="{{ route('admin.pages.index') }}"
                    style="padding: 0.75rem 1.5rem; background: white; border: 1px solid var(--border); color: var(--text-main); border-radius: 0.5rem; font-weight: 500; text-decoration: none;">Cancel</a>
                <button type="submit"
                    style="padding: 0.75rem 2rem; background: var(--primary); border: none; color: white; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">Update
                    Page</button>
            </div>
        </form>
    </div>
@endsection

<!-- TEMPLATES FOR NEW BLOCKS -->
<template id="tpl-text">
    <div class="form-group">
        <label class="form-label">Image (Optional)</label>
        <input type="file" name="sections[__IDX__][image]" accept="image/*" class="form-input" style="padding:0.6rem">
    </div>
    <div class="form-group">
        <label class="form-label">Content</label>
        <textarea name="sections[__IDX__][content]" rows="4" class="form-input rich-editor" style="resize:vertical;"
            placeholder="Add text content here..."></textarea>
    </div>
</template>

<template id="tpl-image">
    <div class="form-group">
        <label class="form-label">Image</label>
        <input type="file" name="sections[__IDX__][image]" accept="image/*" class="form-input" style="padding:0.6rem">
    </div>
</template>

<template id="tpl-image_points">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <div class="form-group">
            <label class="form-label">Image</label>
            <input type="file" name="sections[__IDX__][image]" accept="image/*" class="form-input"
                style="padding:0.6rem">
        </div>
        <div class="form-group nested-list" data-idx="__IDX__">
            <label class="form-label">Bullet Points</label>
            <div class="points-container">
                <input type="text" name="sections[__IDX__][points][]" class="form-input" style="margin-bottom:0.5rem"
                    placeholder="Point 1">
            </div>
            <button type="button" class="add-point-btn"
                style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add Point</button>
        </div>
    </div>
</template>

<template id="tpl-video">
    <div class="form-group">
        <label class="form-label">Video URL or Embed Code</label>
        <input type="text" name="sections[__IDX__][url]" class="form-input"
            placeholder="e.g. https://youtube.com/watch?v=...">
    </div>
</template>

<template id="tpl-faq">
    <div class="form-group nested-list" data-idx="__IDX__">
        <label class="form-label">Questions & Answers</label>
        <div class="faqs-container">
            <div
                style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                <button type="button" class="remove-element-btn"
                    style="position:absolute; top: -0.5rem; right: -0.5rem; background: var(--danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer;">X</button>
                <input type="text" name="sections[__IDX__][items][0][q]" class="form-input"
                    style="margin-bottom:0.5rem; font-weight:600;" placeholder="Question">
                <textarea name="sections[__IDX__][items][0][a]" class="form-input" rows="2"
                    placeholder="Answer"></textarea>
            </div>
        </div>
        <button type="button" class="add-faq-btn" style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+
            Add QA Pair</button>
    </div>
</template>

<template id="tpl-gallery">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
        <div class="form-group">
            <label class="form-label">Label 1</label>
            <input type="text" name="sections[__IDX__][label1]" class="form-input" placeholder="e.g. Luxury Villas">
        </div>
        <div class="form-group">
            <label class="form-label">Label 2</label>
            <input type="text" name="sections[__IDX__][label2]" class="form-input" placeholder="e.g. 50+ Completed">
        </div>
    </div>
    <div class="form-group" style="margin-bottom: 1rem;">
        <label class="form-label">Gallery Introduction/Content</label>
        <textarea name="sections[__IDX__][content]" rows="3" class="form-input rich-editor" placeholder="Describe this gallery..."></textarea>
    </div>
    <div class="form-group">
        <label class="form-label">Upload Images</label>
        <input type="file" name="sections[__IDX__][images][]" multiple accept="image/*" class="form-input"
            style="padding:0.6rem">
        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Hold CTRL/CMD to select multiple
            files.</p>
    </div>
</template>

<template id="tpl-feature_grid">
    <div class="form-group">
        <label class="form-label">Block Description (Optional)</label>
        <textarea name="sections[__IDX__][description]" class="form-input" rows="2" placeholder="Section subtitle or introduction..."></textarea>
    </div>
    <div class="form-group nested-list" data-idx="__IDX__">
        <label class="form-label">Items (Image, Alt, Title, Description)</label>
        <div class="grid-items-container">
            <div style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Image</label>
                        <input type="file" name="sections[__IDX__][items][0][image]" accept="image/*" class="form-input" style="padding:0.5rem">
                        <input type="text" name="sections[__IDX__][items][0][alt]" class="form-input" style="margin-top:0.5rem; font-size: 0.8rem;" placeholder="Image Alt Text">
                    </div>
                    <div>
                        <input type="text" name="sections[__IDX__][items][0][title]" class="form-input" style="margin-bottom:0.5rem; font-weight:600;" placeholder="Item Title">
                        <textarea name="sections[__IDX__][items][0][description]" class="form-input" rows="2" placeholder="Item Description"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="add-grid-item-btn" style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add Item</button>
    </div>
</template>

<template id="tpl-best_rates">
    <div class="form-group">
        <label class="form-label">Sub-title (Optional)</label>
        <input type="text" name="sections[__IDX__][subtitle]" class="form-input" placeholder="e.g. Handpicked villas with great deals">
    </div>
</template>

<template id="tpl-featured_properties">
    <div class="form-group">
        <label class="form-label">Sub-title (Optional)</label>
        <input type="text" name="sections[__IDX__][subtitle]" class="form-input" placeholder="e.g. Recommended just for you">
    </div>
    <div class="form-group nested-list" data-idx="__IDX__">
        <label class="form-label">BHK Filters (Numbers only, e.g. 3, 4, 6)</label>
        <div class="bhk-container" style="display:flex; flex-wrap:wrap; gap:0.5rem; margin-bottom:0.5rem;">
            <input type="number" name="sections[__IDX__][bhks][]" class="form-input" style="width:80px" placeholder="BHK">
        </div>
        <button type="button" class="add-bhk-btn" style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add BHK Tab</button>
    </div>
</template>

<template id="tpl-featured_destinations">
    <div class="form-group">
        <label class="form-label">Sub-title (Optional)</label>
        <input type="text" name="sections[__IDX__][subtitle]" class="form-input" placeholder="e.g. Explore our most popular locations">
    </div>
</template>

<template id="tpl-stats_grid">
    <div class="form-group">
        <label class="form-label">Background Image (Optional)</label>
        <input type="file" name="sections[__IDX__][background_image]" accept="image/*" class="form-input" style="padding:0.6rem">
    </div>
    <div class="form-group nested-list" data-idx="__IDX__">
        <label class="form-label">Stat Items (Icon, Value, Label)</label>
        <div class="stats-items-container">
            <div style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                <div style="display:grid; grid-template-columns: 80px 1fr; gap:1rem;">
                    <div class="form-group">
                        <input type="file" name="sections[__IDX__][items][0][image]" accept="image/*" class="form-input" style="padding:0.3rem">
                    </div>
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:0.5rem;">
                        <input type="text" name="sections[__IDX__][items][0][title]" class="form-input" placeholder="e.g. 100+">
                        <input type="text" name="sections[__IDX__][items][0][label]" class="form-input" placeholder="e.g. Happy Guests">
                        <input type="text" name="sections[__IDX__][items][0][link]" class="form-input" placeholder="Link (Optional)">
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="add-stats-item-btn" style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add Stat</button>
    </div>
</template>

<template id="tpl-carousel">
    <div class="form-group nested-list" data-idx="__IDX__">
        <label class="form-label">Carousel Items (Image, Title, Description)</label>
        <div class="carousel-items-container">
            <div
                style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                <div style="display:grid; grid-template-columns: 1fr 2fr; gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Image</label>
                        <input type="file" name="sections[__IDX__][items][0][image]" accept="image/*" class="form-input"
                            style="padding:0.5rem">
                    </div>
                    <div>
                        <input type="text" name="sections[__IDX__][items][0][title]" class="form-input"
                            style="margin-bottom:0.5rem; font-weight:600;" placeholder="Item Title">
                        <textarea name="sections[__IDX__][items][0][description]" class="form-input" rows="2"
                            placeholder="Item Description"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="add-carousel-item-btn"
            style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add Carousel Item</button>
    </div>
</template>

<template id="tpl-multi_text">
    <div class="form-group nested-list" data-idx="__IDX__">
        <label class="form-label">Multi-Text Items (Title & Content)</label>
        <div class="multi-text-container">
            <div style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                <button type="button" class="remove-element-btn" style="position:absolute; top: -0.5rem; right: -0.5rem; background: var(--danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; z-index:1;">X</button>
                <div class="form-group">
                    <label class="form-label">Item Title</label>
                    <input type="text" name="sections[__IDX__][items][0][title]" class="form-input" placeholder="Enter title...">
                </div>
                <div class="form-group">
                    <label class="form-label">Item Content</label>
                    <textarea name="sections[__IDX__][items][0][content]" class="form-input rich-editor" rows="4"></textarea>
                </div>
            </div>
        </div>
        <button type="button" class="add-multi-text-item-btn" style="font-size:0.75rem; padding:0.25rem 0.5rem; cursor:pointer;">+ Add Text Item</button>
    </div>
</template>

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        function initRichEditors() {
            document.querySelectorAll('.rich-editor').forEach(function(editor) {
                if (!editor.nextElementSibling || !editor.nextElementSibling.classList.contains('cke')) {
                    CKEDITOR.replace(editor);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('sections-container');
            const emptyMsg = document.getElementById('empty-sections-msg');

            let sectionIndex = 0;
            const existingBlocks = container.querySelectorAll('.section-block');
            if (existingBlocks.length > 0) {
                sectionIndex = existingBlocks.length;
            }

            initRichEditors();


            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.block-selector')) {
                    document.getElementById('blockOptions').classList.remove('show');
                }
            });

            function updateEmptyState() {
                emptyMsg.style.display = container.children.length === 0 ? 'block' : 'none';
            }

            // Add Section
            document.querySelectorAll('.block-option-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const type = this.getAttribute('data-type');
                    const typeLabel = this.innerText;
                    document.getElementById('blockOptions').classList.remove('show');

                    const tpl = document.getElementById('tpl-' + type).innerHTML.replace(/__IDX__/g, sectionIndex);

                    const sectionHtml = `
                        <div class="section-block">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border);">
                                <h4 style="font-size: 1rem; font-weight: 600; color: var(--primary);">
                                    <span style="background: #f1f5f9; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; margin-right: 0.5rem; color: var(--secondary);">Block</span> 
                                    ${typeLabel}
                                </h4>
                                <button type="button" class="remove-btn">✖ Remove</button>
                            </div>

                            <input type="hidden" name="sections[${sectionIndex}][type]" value="${type}">

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label class="form-label">Section Key (Unique ID) <span style="color:var(--danger)">*</span></label>
                                    <input type="text" name="sections[${sectionIndex}][key]" class="form-input" placeholder="e.g. hero_banner" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Block Title (Optional)</label>
                                    <input type="text" name="sections[${sectionIndex}][title]" class="form-input" placeholder="e.g. Our Services">
                                </div>
                            </div>

                            ${tpl}
                        </div>
                    `;

                    container.insertAdjacentHTML('beforeend', sectionHtml);
                    sectionIndex++;
                    updateEmptyState();
                    initRichEditors();
                });
            });

            // Global Event Delegation for dynamic buttons
            container.addEventListener('click', function (e) {
                // Remove block
                if (e.target.classList.contains('remove-btn')) {
                    e.target.closest('.section-block').remove();
                    updateEmptyState();
                }

                // Add Point (for Image_Points block)
                if (e.target.classList.contains('add-point-btn')) {
                    const nestedList = e.target.closest('.nested-list');
                    const idx = nestedList.getAttribute('data-idx');
                    const pointsWrap = nestedList.querySelector('.points-container');
                    pointsWrap.insertAdjacentHTML('beforeend', `<input type="text" name="sections[${idx}][points][]" class="form-input" style="margin-bottom:0.5rem" placeholder="Another point">`);
                }

                // Add FAQ (for FAQ block)
                if (e.target.classList.contains('add-faq-btn')) {
                    const nestedList = e.target.closest('.nested-list');
                    const idx = nestedList.getAttribute('data-idx');
                    const faqsWrap = nestedList.querySelector('.faqs-container');
                    const faqIdx = Date.now(); // Safe index generator
                    faqsWrap.insertAdjacentHTML('beforeend', `
                        <div style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                            <button type="button" class="remove-element-btn" style="position:absolute; top: -0.5rem; right: -0.5rem; background: var(--danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer;">X</button>
                            <input type="text" name="sections[${idx}][items][${faqIdx}][q]" class="form-input" style="margin-bottom:0.5rem; font-weight:600;" placeholder="Question">
                            <textarea name="sections[${idx}][items][${faqIdx}][a]" class="form-input" rows="2" placeholder="Answer"></textarea>
                        </div>
                    `);
                }

                // Add Carousel Item
                if (e.target.classList.contains('add-carousel-item-btn')) {
                    const nestedList = e.target.closest('.nested-list');
                    const idx = nestedList.getAttribute('data-idx');
                    const itemsWrap = nestedList.querySelector('.carousel-items-container');
                    const itemIdx = Date.now();
                    itemsWrap.insertAdjacentHTML('beforeend', `
                        <div style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                            <button type="button" class="remove-element-btn" style="position:absolute; top: -0.5rem; right: -0.5rem; background: var(--danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; z-index:1;">X</button>
                            <div style="display:grid; grid-template-columns: 1fr 2fr; gap:1rem;">
                                <div class="form-group">
                                    <label class="form-label">Image</label>
                                    <input type="file" name="sections[${idx}][items][${itemIdx}][image]" accept="image/*" class="form-input" style="padding:0.5rem">
                                </div>
                                <div>
                                    <input type="text" name="sections[${idx}][items][${itemIdx}][title]" class="form-input" style="margin-bottom:0.5rem; font-weight:600;" placeholder="Item Title">
                                    <textarea name="sections[${idx}][items][${itemIdx}][description]" class="form-input" rows="2" placeholder="Item Description"></textarea>
                                </div>
                            </div>
                        </div>
                    `);
                }

                // Add Feature Grid Item
                if (e.target.classList.contains('add-grid-item-btn')) {
                    const nestedList = e.target.closest('.nested-list');
                    const idx = nestedList.getAttribute('data-idx');
                    const itemsWrap = nestedList.querySelector('.grid-items-container');
                    const itemIdx = Date.now();
                    itemsWrap.insertAdjacentHTML('beforeend', `
                        <div style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                            <button type="button" class="remove-element-btn" style="position:absolute; top: -0.5rem; right: -0.5rem; background: var(--danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; z-index:1;">X</button>
                            <div style="display:grid; grid-template-columns: 1fr 2fr; gap:1rem;">
                                <div class="form-group">
                                    <label class="form-label">Image</label>
                                    <input type="file" name="sections[${idx}][items][${itemIdx}][image]" accept="image/*" class="form-input" style="padding:0.5rem">
                                    <input type="text" name="sections[${idx}][items][${itemIdx}][alt]" class="form-input" style="margin-top:0.5rem; font-size: 0.8rem;" placeholder="Image Alt Text">
                                </div>
                                <div>
                                    <input type="text" name="sections[${idx}][items][${itemIdx}][title]" class="form-input" style="margin-bottom:0.5rem; font-weight:600;" placeholder="Item Title">
                                    <textarea name="sections[${idx}][items][${itemIdx}][description]" class="form-input" rows="2" placeholder="Item Description"></textarea>
                                </div>
                            </div>
                        </div>
                    `);
                }

                // Add Stats Item
                if (e.target.classList.contains('add-stats-item-btn')) {
                    const nestedList = e.target.closest('.nested-list');
                    const idx = nestedList.getAttribute('data-idx');
                    const itemsWrap = nestedList.querySelector('.stats-items-container');
                    const itemIdx = Date.now();
                    itemsWrap.insertAdjacentHTML('beforeend', `
                        <div style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                            <button type="button" class="remove-element-btn" style="position:absolute; top: -0.5rem; right: -0.5rem; background: var(--danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; z-index:1;">X</button>
                            <div style="display:grid; grid-template-columns: 80px 1fr; gap:1rem;">
                                <div class="form-group">
                                    <input type="file" name="sections[${idx}][items][${itemIdx}][image]" accept="image/*" class="form-input" style="padding:0.3rem">
                                </div>
                                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:0.5rem;">
                                    <input type="text" name="sections[${idx}][items][${itemIdx}][title]" class="form-input" placeholder="e.g. 100+">
                                    <input type="text" name="sections[${idx}][items][${itemIdx}][label]" class="form-input" placeholder="e.g. Happy Guests">
                                    <input type="text" name="sections[${idx}][items][${itemIdx}][link]" class="form-input" placeholder="Link (Optional)">
                                </div>
                            </div>
                        </div>
                    `);
                }

                // Add BHK Tab
                if (e.target.classList.contains('add-bhk-btn')) {
                    const nestedList = e.target.closest('.nested-list');
                    const idx = nestedList.getAttribute('data-idx');
                    const wrap = nestedList.querySelector('.bhk-container');
                    wrap.insertAdjacentHTML('beforeend', `<div style="position:relative;"><input type="number" name="sections[${idx}][bhks][]" class="form-input" style="width:80px" placeholder="BHK"><button type="button" class="remove-element-btn" style="position:absolute; top:-5px; right:-5px; background:red; color:white; border:none; border-radius:50%; width:15px; height:15px; font-size:8px; cursor:pointer;">x</button></div>`);
                }

                // Add Multi-Text Item
                if (e.target.classList.contains('add-multi-text-item-btn')) {
                    const nestedList = e.target.closest('.nested-list');
                    const idx = nestedList.getAttribute('data-idx');
                    const itemsWrap = nestedList.querySelector('.multi-text-container');
                    const itemIdx = Date.now();
                    itemsWrap.insertAdjacentHTML('beforeend', `
                        <div style="background: white; padding: 1rem; border: 1px solid var(--border); border-radius: 0.25rem; margin-bottom: 1rem; position:relative;">
                            <button type="button" class="remove-element-btn" style="position:absolute; top: -0.5rem; right: -0.5rem; background: var(--danger); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; z-index:1;">X</button>
                            <div class="form-group">
                                <label class="form-label">Item Title</label>
                                <input type="text" name="sections[${idx}][items][${itemIdx}][title]" class="form-input" placeholder="Enter title...">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Item Content</label>
                                <textarea name="sections[${idx}][items][${itemIdx}][content]" class="form-input rich-editor" rows="4"></textarea>
                            </div>
                        </div>
                    `);
                    initRichEditors();
                }

                // Remove nested element
                if (e.target.classList.contains('remove-element-btn')) {
                    e.target.parentElement.remove();
                }
            });
        });
    </script>
@endsection