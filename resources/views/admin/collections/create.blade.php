@extends('layouts.admin')

@section('header', 'Add New Collection')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Collection Details</h2>
        <a href="{{ route('admin.collections.index') }}" style="color: var(--secondary); text-decoration: none; font-size: 0.875rem;">← Back to List</a>
    </div>

    <form action="{{ route('admin.collections.store') }}" method="POST" enctype="multipart/form-data" style="padding: 2rem;">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <!-- Left Side: Basic Content -->
            <div>
                <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--primary);">Main Content</h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Heading <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="heading" id="heading" value="{{ old('heading') }}" required 
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;"
                        placeholder="e.g. Summer Special, Luxury Villas">
                    @error('heading') <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Slug <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required 
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;"
                        placeholder="summer-special">
                    @error('slug') <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Collection Image</label>
                    <input type="file" name="image" accept="image/*" 
                        style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">
                    @error('image') <p style="color: var(--danger); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p> @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Description</label>
                    <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Right Side: SEO Content -->
            <div>
                <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--primary);">SEO Optimization</h3>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Meta Keywords</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Meta Description</label>
                    <textarea name="meta_description" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem;">{{ old('meta_description') }}</textarea>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main);">Meta Schema (JSON-LD)</label>
                    <textarea name="meta_schema" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.875rem; font-family: monospace;">{{ old('meta_schema') }}</textarea>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <a href="{{ route('admin.collections.index') }}" style="padding: 0.75rem 1.5rem; background: white; border: 1px solid var(--border); color: var(--text-main); border-radius: 0.5rem; font-weight: 500; text-decoration: none;">Cancel</a>
            <button type="submit" style="padding: 0.75rem 2rem; background: var(--primary); border: none; color: white; border-radius: 0.5rem; font-weight: 600; cursor: pointer;">Save Collection</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('heading').addEventListener('input', function() {
        let slug = this.value.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
        document.getElementById('slug').value = slug;
    });
</script>
@endsection
