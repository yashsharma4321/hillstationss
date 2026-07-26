@extends('layouts.admin')

@section('header', 'Rooms: ' . $property->name)

@section('styles')
<style>
    .page-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .btn-primary { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.4rem; background: var(--primary); color: white; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.875rem; }
    .btn-secondary { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.4rem; background: white; border: 1px solid var(--border); color: var(--text-main); border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.875rem; }
    .rooms-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
    .room-card { background: white; border: 1px solid var(--border); border-radius: 0.75rem; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.05); transition: box-shadow 0.2s; }
    .room-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
    .room-img { width: 100%; height: 180px; object-fit: cover; }
    .room-img-placeholder { width: 100%; height: 140px; background: linear-gradient(135deg,#e0e7ff,#ede9fe); display: flex; align-items: center; justify-content: center; }
    .room-body { padding: 1.25rem; }
    .room-title { font-weight: 700; font-size: 1rem; margin-bottom: 0.35rem; }
    .room-meta { font-size: 0.8rem; color: var(--text-muted); display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem; }
    .badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 600; }
    .badge-green { background: #dcfce7; color: #15803d; }
    .badge-red { background: #fee2e2; color: #dc2626; }
    .badge-indigo { background: #e0e7ff; color: #4338ca; }
    .room-actions { display: flex; gap: 0.5rem; padding: 0.75rem 1.25rem; border-top: 1px solid var(--border); background: #fafafa; }
    .btn-sm { flex: 1; text-align: center; padding: 0.45rem; border-radius: 0.4rem; font-size: 0.8rem; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
    .btn-edit { background: var(--primary); color: white; }
    .btn-delete { background: #fee2e2; color: #dc2626; }
    .images-strip { display: flex; gap: 0.35rem; padding: 0.5rem 1.25rem; overflow-x: auto; border-bottom: 1px solid var(--border); }
    .images-strip img { width: 48px; height: 48px; object-fit: cover; border-radius: 0.3rem; flex-shrink: 0; }
    .images-strip-empty { padding: 0.5rem 1.25rem; font-size: 0.75rem; color: var(--text-muted); border-bottom: 1px solid var(--border); }
</style>
@endsection

@section('content')

@if(session('success'))
    <div style="background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:0.875rem 1.25rem; border-radius:0.5rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem;">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ session('success') }}
    </div>
@endif

<div class="page-actions">
    <div>
        <a href="{{ route('admin.properties.edit', $property) }}" class="btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"></path></svg>
            Back to Property
        </a>
    </div>
    <a href="{{ route('admin.properties.rooms.create', $property) }}" class="btn-primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
        Add New Room
    </a>
</div>

{{-- Property summary bar --}}
<div style="background:white; border:1px solid var(--border); border-radius:0.75rem; padding:1rem 1.5rem; margin-bottom:1.5rem; display:flex; gap:2rem; align-items:center; flex-wrap:wrap; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div>
        <div style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;">Property</div>
        <div style="font-weight:700; font-size:0.95rem;">{{ $property->name }}</div>
    </div>
    <div>
        <div style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;">Total Rooms</div>
        <div style="font-weight:700; font-size:0.95rem;">{{ $rooms->count() }}</div>
    </div>
    <div>
        <div style="font-size:0.7rem; text-transform:uppercase; color:var(--text-muted); font-weight:600;">City</div>
        <div style="font-weight:700; font-size:0.95rem;">{{ $property->city }}, {{ $property->state }}</div>
    </div>
</div>

@if($rooms->isEmpty())
    <div style="text-align:center; padding:4rem 2rem; background:white; border:1px solid var(--border); border-radius:0.75rem;">
        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem; color:#a5b4fc;">
            <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:0.5rem; color:var(--text-main);">No rooms added yet</h3>
        <p style="color:var(--text-muted); margin-bottom:1.5rem;">Add rooms to this property with images, bed types and meal plans.</p>
        <a href="{{ route('admin.properties.rooms.create', $property) }}" class="btn-primary" style="display:inline-flex;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Add First Room
        </a>
    </div>
@else
    <div class="rooms-grid">
        @foreach($rooms as $room)
            @php
                $imgs = collect($room->images ?? [])
                    ->map(fn($img) => is_array($img) ? $img : ['path' => $img, 'alt' => '']);
                $firstImg = $imgs->first();
            @endphp
            <div class="room-card">
                {{-- Cover image --}}
                @if($firstImg && $firstImg['path'])
                    <img class="room-img" src="{{ Storage::url($firstImg['path']) }}" alt="{{ $firstImg['alt'] }}">
                @else
                    <div class="room-img-placeholder">
                        <svg width="40" height="40" fill="none" stroke="#a5b4fc" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                @endif

                {{-- Thumbnail strip (skip first, already shown above) --}}
                @if($imgs->count() > 1)
                    <div class="images-strip">
                        @foreach($imgs->skip(1) as $img)
                            <img src="{{ Storage::url($img['path']) }}" alt="{{ $img['alt'] }}" title="{{ $img['alt'] }}">
                        @endforeach
                    </div>
                @elseif($imgs->count() === 0)
                    <div class="images-strip-empty">No images uploaded</div>
                @endif

                <div class="room-body">
                    <div class="room-title">{{ $room->title }}</div>
                    <div class="room-meta">
                        @if($room->bed_type)
                            <span>🛏 {{ $room->bed_type }}</span>
                        @endif
                        <span>
                            <span class="badge {{ $room->is_active ? 'badge-green' : 'badge-red' }}">
                                {{ $room->is_active ? '✓ Active' : '✗ Inactive' }}
                            </span>
                        </span>
                        @if($imgs->count())
                            <span class="badge badge-indigo">{{ $imgs->count() }} image(s)</span>
                        @endif
                    </div>

                    @if(!empty($room->meals))
                        <div style="margin-bottom:0.75rem; display:flex; flex-wrap:wrap; gap:0.35rem;">
                            @foreach($room->meals as $meal)
                                <span style="background:#fef3c7; color:#92400e; font-size:0.72rem; padding:0.2rem 0.55rem; border-radius:999px; font-weight:600;">
                                    🍽 {{ $meal }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @if($room->description)
                        <p style="font-size:0.8rem; color:var(--text-muted); line-height:1.5; margin:0;">
                            {{ Str::limit($room->description, 90) }}
                        </p>
                    @endif
                </div>

                <div class="room-actions">
                    <a href="{{ route('admin.rooms.edit', $room) }}" class="btn-sm btn-edit">Edit</a>
                    <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST"
                          onsubmit="return confirm('Delete room: {{ $room->title }}?')" style="flex:1; margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-sm btn-delete" style="width:100%;">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
