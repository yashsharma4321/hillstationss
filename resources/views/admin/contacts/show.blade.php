@extends('layouts.admin')

@section('header', 'Message Details')

@section('content')
<div style="max-width: 800px;">
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('admin.contacts.index') }}" style="text-decoration: none; color: var(--primary); font-size: 0.875rem; font-weight: 600;">&larr; Back to Messages</a>
    </div>

    <div class="content-card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.125rem; font-weight: 600;">Inquiry from {{ $contact->full_name }}</h2>
            <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $contact->created_at->format('M d, Y h:i A') }}</span>
        </div>

        <div style="padding: 1.5rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Email</label>
                    <a href="mailto:{{ $contact->email }}" style="color: var(--primary); text-decoration: none; font-size: 1rem;">{{ $contact->email }}</a>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Phone</label>
                    <a href="tel:{{ $contact->phone }}" style="color: var(--text-main); text-decoration: none; font-size: 1rem;">{{ $contact->phone }}</a>
                </div>
            </div>

            <div style="background: #f8fafc; padding: 1.5rem; border-radius: 0.5rem; border: 1px solid var(--border);">
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1rem;">Message Content</label>
                <div style="line-height:1.6; color: var(--text-main); font-size: 1rem; white-space: pre-wrap;">{{ $contact->message }}</div>
            </div>
        </div>

        <div style="padding: 1rem; background: #fafafa; border-top: 1px solid var(--border); text-align: right;">
            <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Delete this message?')">
                @csrf
                @method('DELETE')
                <button type="submit" style="padding: 0.6rem 1.2rem; background: #fee2e2; color: #991b1b; border: none; border-radius: 0.25rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">Delete Message</button>
            </form>
        </div>
    </div>
</div>
@endsection
