@extends('layouts.app')

@section('content')

@foreach($sections as $section)
    @php $type = $section['type']; @endphp

    {{-- HERO / CAROUSEL --}}
    @if($type === 'carousel')
        <section class="hero-section" style="padding: 0; position: relative; height: 85vh; overflow: hidden; background: #000;">
            @foreach($section['items'] ?? [] as $idx => $item)
                <div class="carousel-slide {{ $idx === 0 ? 'active' : '' }}" style="position: absolute; inset: 0; display: {{ $idx === 0 ? 'block' : 'none' }};">
                    <img src="{{ Storage::url($item['image']) }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.7;">
                    <div class="container" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: center; color: white;">
                        <h1 style="font-family: 'Playfair Display', serif; font-size: 4.5rem; margin-bottom: 1.5rem; line-height: 1.1;">{{ $item['title'] }}</h1>
                        <p style="font-size: 1.25rem; max-width: 600px; opacity: 0.9;">{{ $item['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </section>

    {{-- STATS GRID --}}
    @elseif($type === 'stats_grid')
        <section class="stats-section">
            <div class="container">
                <div class="stats-grid">
                    @foreach($section['items'] ?? [] as $item)
                        <div class="stat-item">
                            @if(isset($item['image']) && $item['image'])
                                <img src="{{ Storage::url($item['image']) }}" style="width: 48px; height: 48px; margin-bottom: 1.5rem;">
                            @endif
                            <span class="stat-val">{{ $item['title'] }}</span>
                            <span class="stat-label">{{ $item['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

    {{-- BEST RATES (Destinations Tabs) --}}
    @elseif($type === 'best_rates')
        <section class="best-rates-section" style="background: #f8fafc;">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">{{ $section['title'] ?? 'Best Rates Destination' }}</h2>
                    <p class="section-subtitle">{{ $section['subtitle'] ?? '' }}</p>
                </div>

                <div class="tabs-header">
                    @foreach($section['data'] ?? [] as $idx => $dest)
                        <button class="tab-btn {{ $idx === 0 ? 'active' : '' }}" onclick="switchTab(this, 'dest-{{ $dest->id }}')">
                            {{ $dest->name }}
                        </button>
                    @endforeach
                </div>

                @foreach($section['data'] ?? [] as $idx => $dest)
                    <div class="tab-content property-grid" id="dest-{{ $dest->id }}" style="display: {{ $idx === 0 ? 'grid' : 'none' }};">
                        @foreach($dest->properties as $property)
                            <div class="property-card">
                                <div class="property-img">
                                    <span class="property-badge">Special Rate</span>
                                    <img src="{{ Storage::url($property->gallery[0]['image'] ?? '') }}" alt="{{ $property->name }}">
                                </div>
                                <div class="property-content">
                                    <h3 class="property-title">{{ $property->name }}</h3>
                                    <div class="property-meta">
                                        <span>{{ $property->total_bedrooms }} BHK</span>
                                        <span>{{ $property->max_guests }} Guests</span>
                                        <span>🏠 {{ $dest->name }}</span>
                                    </div>
                                    <div class="property-price">
                                        <span class="price-label">Starts from</span>
                                        <span class="price-val">₹ 14,999<small style="font-size: 0.6em; color: var(--text-muted); font-weight: 500;"> / night</small></span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </section>

    {{-- FEATURED DESTINATIONS (Dynamic Destinations & their Featured Properties) --}}
    @elseif($type === 'featured_destinations')
        <section class="featured-destinations-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">{{ $section['title'] ?? 'Explore Destinations' }}</h2>
                    <p class="section-subtitle">{{ $section['subtitle'] ?? '' }}</p>
                </div>

                <div class="tabs-header">
                    @foreach($section['data'] ?? [] as $idx => $dest)
                        <button class="tab-btn {{ $idx === 0 ? 'active' : '' }}" onclick="switchTab(this, 'feat-dest-{{ $dest->id }}')">
                            {{ $dest->name }}
                        </button>
                    @endforeach
                </div>

                @foreach($section['data'] ?? [] as $idx => $dest)
                    <div class="tab-content property-grid" id="feat-dest-{{ $dest->id }}" style="display: {{ $idx === 0 ? 'grid' : 'none' }};">
                        @foreach($dest->properties as $property)
                            <div class="property-card">
                                <div class="property-img">
                                    <img src="{{ Storage::url($property->gallery[0]['image'] ?? '') }}" alt="{{ $property->name }}">
                                </div>
                                <div class="property-content">
                                    <h3 class="property-title">{{ $property->name }}</h3>
                                    <div class="property-meta">
                                        <span>{{ $property->total_bedrooms }} BHK</span>
                                        <span>{{ $property->max_guests }} Guests</span>
                                        <span>📍 {{ $dest->name }}</span>
                                    </div>
                                    <div class="property-price">
                                        <span class="price-val">₹ 19,500</span>
                                        <span class="price-label">per night</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </section>

    {{-- FEATURED PROPERTIES (BHK Tabs) --}}
    @elseif($type === 'featured_properties')
        <section class="featured-properties-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">{{ $section['title'] ?? 'Properties for You' }}</h2>
                    <p class="section-subtitle">{{ $section['subtitle'] ?? '' }}</p>
                </div>

                <div class="tabs-header">
                    @php $first = true; @endphp
                    @foreach($section['data'] ?? [] as $bhk => $props)
                        <button class="tab-btn {{ $first ? 'active' : '' }}" onclick="switchTab(this, 'bhk-{{ $bhk }}')">
                            {{ $bhk }} BHK
                        </button>
                        @php $first = false; @endphp
                    @endforeach
                </div>

                @php $first = true; @endphp
                @foreach($section['data'] ?? [] as $bhk => $props)
                    <div class="tab-content property-grid" id="bhk-{{ $bhk }}" style="display: {{ $first ? 'grid' : 'none' }};">
                        @foreach($props as $property)
                            <div class="property-card">
                                <div class="property-img">
                                    <img src="{{ Storage::url($property->gallery[0]['image'] ?? '') }}" alt="{{ $property->name }}">
                                </div>
                                <div class="property-content">
                                    <h3 class="property-title">{{ $property->name }}</h3>
                                    <div class="property-meta">
                                        <span>{{ $property->total_bedrooms }} BHK</span>
                                        <span>{{ $property->total_bathrooms }} Baths</span>
                                        <span>📍 {{ $property->city }}</span>
                                    </div>
                                    <div class="property-price" style="flex-direction: row; justify-content: space-between; align-items: flex-end;">
                                        <div>
                                            <span class="price-label">Per Night</span>
                                            <span class="price-val">₹ 25,000</span>
                                        </div>
                                        <div style="font-size: 0.85rem; font-weight: 600; color: #10b981; background: #ecfdf5; padding: 0.25rem 0.75rem; border-radius: 99px;">
                                            ★ 5.0
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @php $first = false; @endphp
                @endforeach
            </div>
        </section>

    @endif
@endforeach

@endsection

@section('scripts')
<script>
    function switchTab(btn, contentId) {
        // Toggle Buttons
        const parent = btn.parentElement;
        parent.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Toggle Content
        const section = btn.closest('section');
        section.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
        document.getElementById(contentId).style.display = 'grid';
    }
</script>
@endsection
