@extends('layouts.admin')

@section('header', 'Contact Inquiries')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2 style="font-size: 1.125rem; font-weight: 600;">Message List</h2>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                    <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Name</th>
                    <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Email</th>
                    <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Phone</th>
                    <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Date</th>
                    <th style="padding: 1rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $contact)
                    <tr style="border-bottom: 1px solid var(--border); transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                        <td style="padding: 1rem; font-size: 0.875rem; font-weight: 500; color: var(--text-main);">{{ $contact->full_name }}</td>
                        <td style="padding: 1rem; font-size: 0.875rem; color: var(--text-muted);">{{ $contact->email }}</td>
                        <td style="padding: 1rem; font-size: 0.875rem; color: var(--text-muted);">{{ $contact->phone }}</td>
                        <td style="padding: 1rem; font-size: 0.875rem; color: var(--text-muted);">{{ $contact->created_at->format('M d, Y') }}</td>
                        <td style="padding: 1rem; text-align: right;">
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <a href="{{ route('admin.contacts.show', $contact) }}" style="padding: 0.4rem 0.8rem; background: var(--border); color: var(--text-main); border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; text-decoration: none;">View</a>
                                <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Delete this message?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="padding: 0.4rem 0.8rem; background: #fee2e2; color: #991b1b; border: none; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 3rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">No messages found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding: 1rem; border-top: 1px solid var(--border);">
        {{ $contacts->links() }}
    </div>
</div>
@endsection
