@extends('layouts.admin')

@section('header', 'Content Pages')

@section('styles')
<style>
    .page-card:hover { background-color: #f8fafc; }
    .action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        transition: all 0.2s;
        border: 1px solid var(--border);
        background: white;
        cursor: pointer;
        color: #64748b;
    }
    .action-btn:hover { background: #f1f5f9; border-color: var(--primary); color: var(--primary); }
    .action-btn.delete:hover { border-color: var(--danger); color: var(--danger); }
</style>
@endsection

@section('content')
<div class="content-card">
    <div class="card-header" style="background: white;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">Manage Pages</h2>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">Create and edit static pages for your website.</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" 
           style="background: var(--primary); color: white; padding: 0.75rem 1.5rem; border-radius: 0.625rem; text-decoration: none; font-size: 0.875rem; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2); transition: all 0.2s;">
           + Create Page
        </a>
    </div>

    @if(session('success'))
        <div style="margin: 1.5rem; padding: 1rem; background: #ecfdf5; color: #065f46; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span style="font-size: 0.875rem; font-weight: 500;">{{ session('success') }}</span>
        </div>
    @endif

    <div style="padding: 0 1.5rem 1.5rem 1.5rem;">
        <div style="overflow-x: auto; border: 1px solid var(--border); border-radius: 0.75rem;">
            <table style="min-width: 800px;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="border-bottom: 1px solid var(--border); width: 100px;">ID</th>
                        <th style="border-bottom: 1px solid var(--border);">Page Preview</th>
                        <th style="border-bottom: 1px solid var(--border);">Date Created</th>
                        <th style="border-bottom: 1px solid var(--border); text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                        <tr class="page-card" style="transition: background 0.2s;">
                            <td style="padding: 1.25rem 1.5rem; font-weight: 600; color: #94a3b8;">#{{ $page->id }}</td>
                            <td style="padding: 1.25rem 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    @if($page->banner_image)
                                        <img src="{{ Storage::url($page->banner_image) }}" style="width: 44px; height: 44px; object-fit: cover; border-radius: 0.5rem; background: #f1f5f9;">
                                    @else
                                        <div style="width: 44px; height: 44px; background: #f1f5f9; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: #cbd5e1;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight: 700; color: #1e293b;">{{ $page->title }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">/{{ $page->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1.25rem 1.5rem; color: #475569; font-size: 0.875rem;">
                                {{ $page->created_at->format('M d, Y') }}
                            </td>
                            <td style="padding: 1.25rem 1.5rem; text-align: right;">
                                <div style="display: inline-flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="action-btn" title="Edit Page">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Delete this page?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete Page">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 4rem 1.5rem;">
                                <p style="color: var(--text-muted);">No pages found.</p>
                                <a href="{{ route('admin.pages.create') }}" style="color: var(--primary); font-weight: 600; text-decoration: none; margin-top: 0.5rem; display: inline-block;">Create your first page</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pages->hasPages())
        <div style="margin-top: 1.5rem;">
            {{ $pages->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

