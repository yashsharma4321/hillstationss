@extends('layouts.admin')

@section('header', 'Manage Collections')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 style="font-size: 1.125rem; font-weight: 600;">All Collections</h2>
        <a href="{{ route('admin.collections.create') }}" class="btn-primary" style="padding: 0.6rem 1.25rem;">+ Add New Collection</a>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="text-align: left; background: var(--bg-body); border-bottom: 2px solid var(--border);">
                    <th style="padding: 1rem 1.5rem; color: var(--text-muted); font-weight: 600;">Image</th>
                    <th style="padding: 1rem 1.5rem; color: var(--text-muted); font-weight: 600;">Heading</th>
                    <th style="padding: 1rem 1.5rem; color: var(--text-muted); font-weight: 600;">Slug</th>
                    <th style="padding: 1rem 1.5rem; color: var(--text-muted); font-weight: 600; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($collections as $collection)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem 1.5rem;">
                            @if($collection->image)
                                <img src="{{ Storage::url($collection->image) }}" alt="{{ $collection->heading }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.5rem;">
                            @else
                                <div style="width: 50px; height: 50px; background: #eee; border-radius: 0.5rem;"></div>
                            @endif
                        </td>
                        <td style="padding: 1rem 1.5rem; font-weight: 500;">{{ $collection->heading }}</td>
                        <td style="padding: 1rem 1.5rem; color: var(--text-muted);">{{ $collection->slug }}</td>
                        <td style="padding: 1rem 1.5rem; text-align: right;">
                            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                                <a href="{{ route('admin.collections.edit', $collection) }}" style="color: var(--primary); text-decoration: none;">Edit</a>
                                <form action="{{ route('admin.collections.destroy', $collection) }}" method="POST" onsubmit="return confirm('Delete this collection?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: var(--danger); font-family: inherit; font-size: inherit; cursor: pointer; padding: 0;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 3rem; text-align: center; color: var(--text-muted);">No collections found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding: 1.5rem;">
        {{ $collections->links() }}
    </div>
</div>
@endsection
