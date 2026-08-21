@extends('layouts.vendor')

@section('header', 'Edit Property: ' . $property->name)

@section('styles')
<style>
    .form-section { background: white; border: 1px solid var(--border); border-radius: 1rem; padding: 2.5rem; margin-bottom: 2rem; box-shadow: var(--shadow); }
    .section-title { font-size: 1.125rem; font-weight: 700; margin-bottom: 2rem; color: var(--primary); display: flex; align-items: center; gap: 0.75rem; border-bottom: 2px solid var(--bg-body); padding-bottom: 1rem; }
    
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; }
    .full-width { grid-column: span 2; }
    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-main); }
    .form-input { width: 100%; padding: 0.875rem 1rem; border: 1.5px solid var(--border); border-radius: 0.75rem; font-family: inherit; font-size: 0.9rem; transition: all 0.2s; background: #fff; }
    .form-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(73, 166, 140, 0.1); }

    .amenity-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.25rem; background: var(--bg-body); padding: 1.75rem; border-radius: 1rem; border: 1px solid var(--border); }
    .amenity-item { display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; color: var(--text-main); }
    .amenity-item input { width: 1.25rem; height: 1.25rem; accent-color: var(--primary); cursor: pointer; }

    .builder-card { background: white; border: 1.5px solid var(--border); border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem; position: relative; }
    .builder-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px dashed var(--border); }
    
    .btn-add { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.5rem; background: var(--primary); color: white; border: none; border-radius: 0.75rem; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(73, 166, 140, 0.2); }
    .btn-add:hover { transform: translateY(-1px); background: var(--primary-hover); }
    .btn-remove { color: var(--danger); background: #fef2f2; border: none; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600; cursor: pointer; }

    /* Gallery Items */
    .thumbnail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1.5rem; margin-top: 1.5rem; }
    .thumb-item { position: relative; border-radius: 1rem; overflow: hidden; border: 1px solid var(--border); background: #fff; box-shadow: var(--shadow); }
    .thumb-item img { width: 100%; height: 110px; object-fit: cover; }
    .btn-delete-img { position: absolute; top: 0.5rem; right: 0.5rem; background: rgba(239, 68, 68, 0.9); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; }
    
    .required { color: var(--danger); }
</style>
@endsection

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">
    <form action="{{ route('vendor.properties.update', $property) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- BASIC INFO -->
        <div class="form-section">
            <h3 class="section-title">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Basic Information
            </h3>
            <div class="form-grid">
                <div class="form-group full-width">
                <label class="form-label">Property Name <span style="color:var(--danger)">*</span></label>
                <div style="display: flex; gap: 1rem;">
                    <input type="text" name="name" value="{{ old('name', $property->name) }}" required class="form-input" placeholder="e.g. Hilltop Villa & Suites" style="flex: 2;">
                    <div style="flex: 1;">
                        <input type="number" step="0.01" name="amount" value="{{ old('amount', $property->amount) }}" class="form-input" placeholder="Amount / Price">
                    </div>
                </div>
                @error('name') <p style="color:var(--danger); font-size:0.75rem; margin-top:0.25rem">{{ $message }}</p> @enderror
            </div>
                
                <div class="form-group">
                    <label class="form-label">Category <span class="required">*</span></label>
                    <select name="category_id" required class="form-input">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $property->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Destination <span class="required">*</span></label>
                    <select name="destination_id" required class="form-input">
                        @foreach($destinations as $destination)
                            <option value="{{ $destination->id }}" {{ $property->destination_id == $destination->id ? 'selected' : '' }}>{{ $destination->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full-width">
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:2rem;">
                        <div>
                            <label class="form-label">Total Bedrooms <span class="required">*</span></label>
                            <input type="number" name="total_bedrooms" value="{{ old('total_bedrooms', $property->total_bedrooms) }}" required class="form-input" min="0">
                        </div>
                        <div>
                            <label class="form-label">Total Bathrooms <span class="required">*</span></label>
                            <input type="number" name="total_bathrooms" value="{{ old('total_bathrooms', $property->total_bathrooms) }}" required class="form-input" min="0">
                        </div>
                        <div>
                            <label class="form-label">Max Guests <span class="required">*</span></label>
                            <input type="number" name="max_guests" value="{{ old('max_guests', $property->max_guests) }}" required class="form-input" min="0">
                        </div>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="5" class="form-input">{{ old('description', $property->description) }}</textarea>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">House Rules</label>
                    <textarea name="house_rules" rows="5" class="form-input">{{ old('house_rules', $property->house_rules) }}</textarea>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">House Rules Description</label>
                    <textarea name="house_rules_description" rows="5" class="form-input">{{ old('house_rules_description', $property->house_rules_description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- LOCATION -->
        <div class="form-section">
            <h3 class="section-title">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Location Details
            </h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">City <span class="required">*</span></label>
                    <input type="text" name="city" value="{{ old('city', $property->city) }}" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">State <span class="required">*</span></label>
                    <input type="text" name="state" value="{{ old('state', $property->state) }}" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" value="{{ old('country', $property->country) }}" required class="form-input">
                </div>
            </div>
        </div>

        <!-- AMENITIES -->
        <div class="form-section">
            <h3 class="section-title">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.143-7.714L1 12l6.857-2.143L11 3z"></path></svg>
                Amenities
            </h3>
            @php $p_amenities = $property->amenities->pluck('id')->toArray(); @endphp
            <div class="amenity-grid">
                @foreach($amenities as $amenity)
                    <label class="amenity-item">
                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" {{ in_array($amenity->id, $p_amenities) ? 'checked' : '' }}>
                        <span>{{ $amenity->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- COLLECTIONS -->
        <div class="form-section">
            <h3 class="section-title">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Property Collections
            </h3>
            @php $pCollections = $property->collections->pluck('id')->toArray(); @endphp
            <div class="amenity-grid">
                @foreach($collections as $collection)
                    <label class="amenity-item">
                        <input type="checkbox" name="collections[]" value="{{ $collection->id }}" {{ in_array($collection->id, $pCollections) ? 'checked' : '' }}>
                        <span>{{ $collection->heading }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- MEAL PLANS -->
        <div class="form-section">
            <h3 class="section-title">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 7h13M7 13l-1.5-7"></path><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/></svg>
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
                        <span>{{ $meal }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- CANCELLATION RULES -->
        <div class="form-section">
            <h3 class="section-title">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Cancellation Rules
            </h3>
            <p style="color:var(--text-muted); font-size:0.875rem; margin-bottom:1.5rem;">Define how much to deduct if the user cancels before check-in.</p>
            <div id="cancellation-rules-container">
                @php $cancellationRules = $property->cancellationRules ?? collect(); @endphp
                @foreach($cancellationRules as $index => $rule)
                    <div class="builder-card" style="margin-bottom:1rem; padding-bottom:0.5rem;">
                        <div class="builder-card-header" style="margin-bottom:1rem; padding-bottom:0.5rem;">
                            <strong>Rule Details</strong>
                            <button type="button" onclick="this.parentElement.parentElement.remove()" class="btn-remove" style="padding:0.25rem 0.5rem; font-size:0.8rem;">✕</button>
                        </div>
                        <div style="display: flex; gap: 1.5rem;">
                            <div style="flex: 1;">
                                <label class="form-label">Days Before Check-in</label>
                                <input type="number" name="cancellation_rules[{{ $index }}][days_before]" value="{{ $rule->days_before }}" class="form-input" required min="0">
                            </div>
                            <div style="flex: 1;">
                                <label class="form-label">Deduction Percentage (%)</label>
                                <input type="number" name="cancellation_rules[{{ $index }}][deduction_percentage]" value="{{ $rule->deduction_percentage }}" class="form-input" required min="0" max="100" step="0.01">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="addCancellationRule()" class="btn-add" style="background:var(--bg-body); color:var(--text-main); border:2px dashed var(--border); box-shadow:none; margin-top: 1rem;">
                + Add Cancellation Rule
            </button>
        </div>

        <!-- MEDIA -->
        <div class="form-section">
            <h3 class="section-title">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Gallery & Media
            </h3>
            
            <!-- Existing Gallery -->
            <div class="thumbnail-grid">
                @foreach($property->gallery ?? [] as $index => $item)
                    <div class="thumb-item" id="gallery-{{ $index }}">
                        @php $path = is_array($item) ? ($item['image'] ?? '') : $item; @endphp
                        <img src="{{ Storage::url($path) }}">
                        <button type="button" class="btn-delete-img" onclick="deleteGalleryImage('{{ $path }}', {{ $index }})">✕</button>
                        <input type="text" name="existing_alts[{{ $index }}]" value="{{ is_array($item) ? ($item['alt'] ?? '') : '' }}" style="width:100%; font-size:0.75rem; border:none; border-top:1px solid #e2e8f0; padding:0.4rem;" placeholder="Alt text">
                    </div>
                @endforeach
            </div>

            <div class="form-grid" style="margin-top:2.5rem;">
                <div class="form-group">
                    <label class="form-label">Add Images</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">GST %</label>
                    <input type="number" name="gst" value="{{ old('gst', $property->gst) }}" class="form-input" step="0.01">
                </div>
                <div class="form-group">
                    <label class="form-label">Check-in After</label>
                    <input type="time" name="check_in_time" value="{{ old('check_in_time', $property->check_in_time) }}" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Check-out Before</label>
                    <input type="time" name="check_out_time" value="{{ old('check_out_time', $property->check_out_time) }}" class="form-input">
                </div>
                <div class="form-group full-width" style="margin-top: 1rem; border-top: 1px solid var(--border); padding-top: 2rem;">
                    <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; color: var(--primary); font-size: 1rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        Instagram Videos (Thumbnail + Video Link)
                    </label>
                    <div id="insta-links-container">
                        @php $instaVideos = is_array($property->instagram_videos) ? $property->instagram_videos : []; @endphp
                        @foreach($instaVideos as $index => $video)
                            @php
                                $isOldFormat = !is_array($video);
                                $videoLink = $isOldFormat ? $video : ($video['video_link'] ?? '');
                                $videoImage = $isOldFormat ? null : ($video['image'] ?? null);
                            @endphp
                            <div class="insta-video-card" style="background:#f8fafc; border:1px solid var(--border); border-radius:0.75rem; padding:1.25rem; margin-bottom:1.25rem; position:relative;">
                                <button type="button" onclick="this.parentElement.remove()" style="position:absolute; top:10px; right:10px; background:#fee2e2; color:#dc2626; border:none; border-radius:50%; width:24px; height:24px; cursor:pointer;">✖</button>
                                <div style="display:grid; grid-template-columns:140px 1fr; gap:1.5rem;">
                                    <div>
                                        @if($videoImage)
                                            <img src="{{ Storage::url($videoImage) }}" style="width:100%; height:80px; object-fit:cover; border-radius:0.5rem; margin-bottom:0.5rem;">
                                        @endif
                                        <input type="hidden" name="existing_instagram_video_images[{{ $index }}]" value="{{ $videoImage ?? '' }}">
                                        <input type="file" name="instagram_video_images[{{ $index }}]" class="form-input" style="font-size:0.7rem; padding:0.25rem;" accept="image/*">
                                    </div>
                                    <div style="display:grid; align-content:center; gap:0.5rem;">
                                        <label class="form-label" style="font-size:0.75rem;">Video URL <span style="color:var(--danger)">*</span></label>
                                        <input type="url" name="instagram_video_links[{{ $index }}]" value="{{ $videoLink }}" class="form-input" placeholder="https://www.instagram.com/reels/XXXXX/" required>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" onclick="addInstaLink()" style="margin-top: 0.5rem; background: var(--primary); color: white; border: none; padding: 0.6rem 1.25rem; border-radius: 0.5rem; cursor: pointer; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                        Add Instagram Video
                    </button>
                </div>
            </div>
        </div>

        <!-- ATTRACTIONS -->
        <div class="form-section">
            <h3 class="section-title">Nearby Attractions</h3>
            <div id="attractions-container">
                @foreach($property->nearby_attractions ?? [] as $idx => $at)
                    <div class="builder-card">
                        <div class="builder-card-header">
                            <strong>Existing Attraction</strong>
                            <button type="button" onclick="this.parentElement.parentElement.remove()" class="btn-remove">✕ Remove</button>
                        </div>
                        <div class="form-grid">
                            <div class="form-group full-width"><label class="form-label">Heading</label><input type="text" name="attraction_headings[{{ $idx }}]" value="{{ $at['heading'] ?? '' }}" class="form-input"></div>
                            <div class="form-group" style="display:flex; align-items:center; gap:1rem;">
                                @if(isset($at['image'])) <img src="{{ Storage::url($at['image']) }}" style="width:50px; height:50px; border-radius:0.5rem; object-fit:cover;"> @endif
                                <div style="flex:1;"><label class="form-label">Update Image</label><input type="file" name="attraction_images[{{ $idx }}]" class="form-input"></div>
                            </div>
                            <div class="form-group"><label class="form-label">Alt Text</label><input type="text" name="attraction_alts[{{ $idx }}]" value="{{ $at['alt'] ?? '' }}" class="form-input"></div>
                            <div class="form-group full-width"><label class="form-label">Description</label><textarea name="attraction_descriptions[{{ $idx }}]" class="form-input" rows="2">{{ $at['description'] ?? '' }}</textarea></div>
                            <input type="hidden" name="attraction_existing_images[{{ $idx }}]" value="{{ $at['image'] ?? '' }}">
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="addAttraction()" class="btn-add" style="background:var(--bg-body); color:var(--text-main); border:2px dashed var(--border); box-shadow:none;">
                + Add Nearby Attraction
            </button>
        </div>

        <!-- THINGS TO DO -->
        <div class="form-section">
            <h3 class="section-title">Things To Do</h3>
            <div id="things-to-do-container">
                @php $tIdx = 0; @endphp
                @if(is_array($property->things_to_do))
                    @foreach($property->things_to_do as $t)
                        <div class="dynamic-card" style="position: relative;">
                            <button type="button" class="btn-remove" onclick="this.parentElement.remove()" style="position:absolute; top:1rem; right:1rem; width:24px; height:24px; border-radius:50%; background:#ef4444; color:#fff; border:none; display:flex; align-items:center; justify-content:center; cursor:pointer;">&times;</button>
                            <div style="display:grid; grid-template-columns:1fr; gap:1rem;">
                                <div class="form-group full-width">
                                    <label class="form-label">Title <span class="required-star">*</span></label>
                                    <input type="text" name="things_to_do[{{$tIdx}}][title]" value="{{ $t['title'] ?? '' }}" class="form-input" placeholder="Title" required>
                                </div>
                                <div class="form-group full-width">
                                    <label class="form-label">Description</label>
                                    <textarea name="things_to_do[{{$tIdx}}][description]" class="form-input" rows="2" placeholder="Description">{{ $t['description'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        @php $tIdx++; @endphp
                    @endforeach
                @endif
            </div>
            <button type="button" onclick="addThingToDo()" class="btn-add" style="background:var(--bg-body); color:var(--text-main); border:2px dashed var(--border); box-shadow:none;">
                + Add Thing To Do
            </button>
        </div>
        <!-- ROOMS -->
        <div class="form-section">
            <h3 class="section-title">Rooms & Units Builder</h3>
            <div id="rooms-container">
                @foreach($property->rooms as $ridx => $room)
                    <div class="builder-card">
                        <div class="builder-card-header">
                            <strong>Room Unit: {{ $room->title }}</strong>
                            <button type="button" onclick="this.parentElement.parentElement.remove()" class="btn-remove">✕ Remove</button>
                        </div>
                        <input type="hidden" name="existing_rooms[{{ $ridx }}][id]" value="{{ $room->id }}">
                        <div class="form-grid">
                            <div class="form-group full-width"><label class="form-label">Room Title <span class="required">*</span></label><input type="text" name="existing_rooms[{{ $ridx }}][title]" value="{{ $room->title }}" class="form-input" required></div>
                            <div class="form-group">
                                <label class="form-label">Bed Type</label>
                                <select name="existing_rooms[{{ $ridx }}][bed_type]" class="form-input">
                                    @foreach(['Single', 'Double', 'Queen', 'King', 'Twin'] as $b)
                                        <option value="{{ $b }}" {{ $room->bed_type == $b ? 'selected' : '' }}>{{ $b }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group full-width"><label class="form-label">Description</label><textarea name="existing_rooms[{{ $ridx }}][description]" class="form-input" rows="2">{{ $room->description }}</textarea></div>
                            <div class="form-group full-width">
                                <label class="form-label">Meal Plans</label>
                                <div class="amenity-grid">
                                    @php $roomMeals = is_array($room->meals) ? $room->meals : (json_decode($room->meals, true) ?? []); @endphp
                                    @foreach(['Breakfast', 'Lunch', 'Dinner', 'All Inclusive', 'Breakfast & Dinner', 'No Meals', 'Veg', 'Non-Veg', 'Jain'] as $m)
                                        <label class="amenity-item"><input type="checkbox" name="existing_rooms[{{ $ridx }}][meals][]" value="{{ $m }}" {{ in_array($m, $roomMeals) ? 'checked' : '' }}> {{ $m }}</label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label">Add Images</label>
                                <input type="file" name="existing_room_images[{{ $ridx }}][]" multiple class="form-input">
                                @if(is_array($room->images) && count($room->images) > 0)
                                    <div style="display:flex; gap:0.5rem; margin-top:1rem; overflow-x:auto;">
                                        @foreach($room->images as $rimg)
                                            <img src="{{ Storage::url(is_array($rimg) ? ($rimg['path'] ?? '') : $rimg) }}" style="width:60px; height:60px; border-radius:0.5rem; object-fit:cover;">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="addRoom()" class="btn-add">
                + Add New Room Unit
            </button>
        </div>

        <!-- SUBMIT -->
        <div style="display:flex; justify-content:flex-end; gap:1.5rem; margin-bottom:5rem;">
            <a href="{{ route('vendor.properties') }}" style="padding:1rem 2rem; color:var(--text-muted); text-decoration:none; font-weight:600;">Discard</a>
            <button type="submit" class="btn btn-primary" style="padding:1rem 4rem; font-size:1rem;">Update Property</button>
        </div>
    </form>
</div>

<script>
    function deleteGalleryImage(path, index) {
        if(!confirm('Are you sure you want to delete this image?')) return;
        fetch('{{ route('vendor.properties.image.delete', $property) }}', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            body: JSON.stringify({ path: path })
        }).then(res => res.json()).then(data => {
            if(data.success) document.getElementById('gallery-' + index).remove();
            else alert('Failed to delete image');
        });
    }

    let instaIdx = {{ count($instaVideos) }};
    function addInstaLink() {
        const container = document.getElementById('insta-links-container');
        const card = document.createElement('div');
        card.style.cssText = 'background:#f8fafc; border:1px solid var(--border); border-radius:0.75rem; padding:1.25rem; margin-bottom:1.25rem; position:relative;';
        card.innerHTML = `
            <button type="button" onclick="this.parentElement.remove()" style="position:absolute; top:10px; right:10px; background:#fee2e2; color:#dc2626; border:none; border-radius:50%; width:24px; height:24px; cursor:pointer;">✖</button>
            <div style="display:grid; grid-template-columns:140px 1fr; gap:1.5rem;">
                <div>
                    <label class="form-label" style="font-size:0.75rem;">Thumbnail Image</label>
                    <input type="file" name="instagram_video_images[${instaIdx}]" class="form-input" style="font-size:0.7rem; padding:0.25rem;" accept="image/*">
                </div>
                <div style="display:grid; align-content:center; gap:0.5rem;">
                    <label class="form-label" style="font-size:0.75rem;">Video URL <span style="color:var(--danger)">*</span></label>
                    <input type="url" name="instagram_video_links[${instaIdx}]" class="form-input" placeholder="https://www.instagram.com/reels/XXXXX/" required>
                </div>
            </div>
        `;
        container.appendChild(card);
        instaIdx++;
    }

    let cancelIdx = {{ isset($cancellationRules) ? count($cancellationRules) : 0 }};
    function addCancellationRule() {
        const container = document.getElementById('cancellation-rules-container');
        const card = document.createElement('div');
        card.className = 'builder-card';
        card.innerHTML = `
            <div class="builder-card-header" style="margin-bottom:1rem; padding-bottom:0.5rem;">
                <strong>Rule Details</strong>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="btn-remove" style="padding:0.25rem 0.5rem; font-size:0.8rem;">✕</button>
            </div>
            <div style="display: flex; gap: 1.5rem;">
                <div style="flex: 1;">
                    <label class="form-label">Days Before Check-in</label>
                    <input type="number" name="cancellation_rules[${cancelIdx}][days_before]" class="form-input" placeholder="e.g. 5" required min="0">
                </div>
                <div style="flex: 1;">
                    <label class="form-label">Deduction Percentage (%)</label>
                    <input type="number" name="cancellation_rules[${cancelIdx}][deduction_percentage]" class="form-input" placeholder="e.g. 20" required min="0" max="100" step="0.01">
                </div>
            </div>
        `;
        container.appendChild(card);
        cancelIdx++;
    }

    let aIdx = {{ count($property->nearby_attractions ?? []) }};
    function addAttraction() {
        const div = document.createElement('div');
        div.className = 'builder-card';
        div.innerHTML = `
            <div class="builder-card-header">
                <strong>New Spot</strong>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="btn-remove">✕ Remove</button>
            </div>
            <div class="form-grid">
                <div class="form-group full-width"><label class="form-label">Heading</label><input type="text" name="attraction_headings[${aIdx}]" class="form-input"></div>
                <div class="form-group"><label class="form-label">Image</label><input type="file" name="attraction_images[${aIdx}]" class="form-input"></div>
                <div class="form-group"><label class="form-label">Alt Text</label><input type="text" name="attraction_alts[${aIdx}]" class="form-input"></div>
                <div class="form-group full-width"><label class="form-label">Description</label><textarea name="attraction_descriptions[${aIdx}]" class="form-input" rows="2"></textarea></div>
            </div>
        `;
        document.getElementById('attractions-container').appendChild(div);
        aIdx++;
    }

    function addThingToDo() {
        const div = document.createElement('div');
        const idx = Date.now();
        div.className = 'dynamic-card';
        div.style.position = 'relative';
        div.innerHTML = `
            <button type="button" class="btn-remove" onclick="this.parentElement.remove()" style="position:absolute; top:1rem; right:1rem; width:24px; height:24px; border-radius:50%; background:#ef4444; color:#fff; border:none; display:flex; align-items:center; justify-content:center; cursor:pointer;">&times;</button>
            <div style="display:grid; grid-template-columns:1fr; gap:1rem;">
                <div class="form-group full-width"><label class="form-label">Title <span class="required-star">*</span></label><input type="text" name="things_to_do[${idx}][title]" class="form-input" placeholder="Title" required></div>
                <div class="form-group full-width"><label class="form-label">Description</label><textarea name="things_to_do[${idx}][description]" class="form-input" rows="2" placeholder="Description"></textarea></div>
            </div>
        `;
        document.getElementById('things-to-do-container').appendChild(div);
    }

    let rIdx = 0;
    const MEALS = ['Breakfast', 'Lunch', 'Dinner', 'All Inclusive', 'Breakfast & Dinner', 'No Meals', 'Veg', 'Non-Veg', 'Jain'];
    const BEDS = ['Single', 'Double', 'Queen', 'King', 'Twin'];

    function addRoom() {
        const idx = 'new_' + rIdx++;
        const div = document.createElement('div');
        div.className = 'builder-card';
        const mealsHtml = MEALS.map(m => `<label class="amenity-item"><input type="checkbox" name="rooms[${idx}][meals][]" value="${m}"> ${m}</label>`).join('');
        const bedsHtml = BEDS.map(b => `<option value="${b}">${b}</option>`).join('');

        div.innerHTML = `
            <div class="builder-card-header">
                <strong style="color:var(--primary);">New Room Unit</strong>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="btn-remove">✕ Remove</button>
            </div>
            <div class="form-grid">
                <div class="form-group full-width"><label class="form-label">Room Title <span class="required">*</span></label><input type="text" name="rooms[${idx}][title]" class="form-input" required></div>
                <div class="form-group"><label class="form-label">Bed Type</label><select name="rooms[${idx}][bed_type]" class="form-input">${bedsHtml}</select></div>
                <div class="form-group full-width"><label class="form-label">Description</label><textarea name="rooms[${idx}][description]" class="form-input" rows="2"></textarea></div>
                <div class="form-group full-width"><label class="form-label">Meal Plans</label><div class="amenity-grid">${mealsHtml}</div></div>
                <div class="form-group full-width"><label class="form-label">Room Images</label><input type="file" name="room_images[${idx}][]" multiple class="form-input"></div>
            </div>
        `;
        document.getElementById('rooms-container').appendChild(div);
    }
</script>
@endsection
