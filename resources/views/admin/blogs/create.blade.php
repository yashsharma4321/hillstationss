@extends('layouts.admin')

@section('header', 'Create Blog')

@section('styles')
<style>
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; color: var(--text-main); }
    .form-input { width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; font-size: 0.875rem; transition: border-color 0.2s; }
    .form-input:focus { outline: none; border-color: var(--primary); }
    .ck-editor__editable { min-height: 300px; }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">New Blog Post</h2>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div style="padding: 1rem; background: #fee2e2; color: #991b1b; border-radius: 0.5rem; margin-bottom: 2rem;">
                <ul style="margin-bottom: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <div class="main-content">
                    <div class="form-group">
                        <label class="form-label" for="title">Blog Title</label>
                        <input type="text" name="title" id="title" class="form-input" placeholder="Enter blog title" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Short Description</label>
                        <textarea name="description" id="description" class="form-input" rows="3" placeholder="Enter a brief summary..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="content">Blog Content</label>
                        <textarea name="content" id="editor" class="form-input"></textarea>
                    </div>

                    <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                        <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--text-main); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 0 20 15.3 15.3 0 0 1 0-20z"/></svg>
                            SEO Settings
                        </h3>

                        <div class="form-group">
                            <label class="form-label" for="meta_title">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-input" placeholder="SEO Title">
                            <small style="color: var(--text-muted);">Recommended: 50-60 characters.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="meta_description">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" class="form-input" rows="3" placeholder="SEO Description"></textarea>
                            <small style="color: var(--text-muted);">Recommended: 150-160 characters.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="meta_keywords">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="meta_keywords" class="form-input" placeholder="Keywords separated by commas">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="schema">Schema Markup (JSON-LD)</label>
                            <textarea name="schema" id="schema" class="form-input" rows="5" placeholder='{"@context": "https://schema.org", "@type": "BlogPosting", ...}'></textarea>
                            <small style="color: var(--text-muted);">Enter valid JSON schema script.</small>
                        </div>
                    </div>
                </div>

                <div class="sidebar">
                    <div class="form-group">
                        <label class="form-label">Featured Image</label>
                        <div style="border: 2px dashed var(--border); padding: 1rem; border-radius: 0.5rem; text-align: center;">
                            <input type="file" name="image" id="image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                            <label for="image" style="cursor: pointer;">
                                <div id="image-preview" style="margin-bottom: 1rem; display: none;">
                                    <img src="" style="max-width: 100%; border-radius: 0.25rem;">
                                </div>
                                <div id="upload-placeholder">
                                    <div style="font-size: 2rem; color: var(--text-muted); mb-2;">📁</div>
                                    <div style="font-size: 0.875rem; color: var(--text-muted);">Click to upload image</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="image_alt">Image Alt Text</label>
                        <input type="text" name="image_alt" id="image_alt" class="form-input" placeholder="SEO Alt Text">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Other Images (Multiple)</label>
                        <input type="file" name="other_images[]" multiple class="form-input" accept="image/*">
                        <small style="color: var(--text-muted); font-size: 0.75rem;">You can select multiple images.</small>
                    </div>

                    <div class="form-group" style="margin-top: 2rem; padding: 1rem; background: #f8fafc; border-radius: 0.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <input type="checkbox" name="status" value="1" checked style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                            <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">Publish Immediately</span>
                        </label>
                    </div>
                </div>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border); display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('admin.blogs.index') }}" style="padding: 0.75rem 1.5rem; background: white; border: 1px solid var(--border); color: var(--text-main); border-radius: 0.5rem; text-decoration: none; font-weight: 500;">Cancel</a>
                <button type="submit" class="btn-primary" style="padding: 0.75rem 2rem;">Save Blog Post</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor', {
        filebrowserUploadUrl: "{{ route('admin.blogs.upload', ['_token' => csrf_token()]) }}",
        height: 400,
        removeButtons: 'About',
        allowedContent: true
    });

    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('upload-placeholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.querySelector('img').src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
