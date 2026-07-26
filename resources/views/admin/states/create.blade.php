@extends('layouts.admin')

@section('header', 'Create State')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 style="font-size: 1.125rem; font-weight: 600;">State Details</h2>
        <a href="{{ route('admin.states.index') }}" style="color: var(--secondary); text-decoration: none; font-size: 0.875rem;">← Back to List</a>
    </div>

    <form action="{{ route('admin.states.store') }}" method="POST" style="padding: 2rem; max-width: 800px;">
        @csrf
        
        <div style="margin-bottom: 2rem;">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">State Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required 
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;"
                    placeholder="e.g. Maharashtra">
                @error('name')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Status</label>
                <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <a href="{{ route('admin.states.index') }}" style="padding: 0.75rem 1.5rem; background: white; border: 1px solid var(--border); color: var(--text-main); border-radius: 0.5rem; font-weight: 500; text-decoration: none;">Cancel</a>
            <button type="submit" style="padding: 0.75rem 2rem; background: var(--primary); border: none; color: white; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">Save State</button>
        </div>
    </form>
</div>
@endsection
