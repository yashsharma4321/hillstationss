@extends('layouts.vendor')

@section('header', 'My Bookings')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Property Booking History</h2>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Property</th>
                    <th>Dates</th>
                    <th>My Earning</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td>
                        <span style="font-weight: 700; color: var(--primary);">{{ $booking->booking_number }}</span>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $booking->created_at->format('d M, Y') }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $booking->customer->name ?? 'Guest' }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $booking->property->name ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <div style="font-size: 0.875rem;">
                            {{ Carbon\Carbon::parse($booking->check_in)->format('d M') }} - {{ Carbon\Carbon::parse($booking->check_out)->format('d M, Y') }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--primary);">₹{{ number_format($booking->vendor_amount, 2) }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Total: ₹{{ number_format($booking->final_amount, 2) }}</div>
                    </td>
                    <td>
                        @if($booking->status == 'confirmed')
                            <span class="badge badge-success">Confirmed</span>
                        @elseif($booking->status == 'cancelled')
                            <span class="badge" style="background: #fef2f2; color: #dc2626;">Cancelled</span>
                        @else
                            <span class="badge badge-pending">{{ ucfirst($booking->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">No bookings yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 1.5rem;">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
