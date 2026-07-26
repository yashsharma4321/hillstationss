@extends('layouts.vendor')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
    <div>
        <div class="card">
            <div class="card-header">
                <h2>Withdrawal History</h2>
            </div>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Bank Details</th>
                            <th>Status</th>
                            <th>Admin Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <tr>
                            <td>{{ $req->created_at->format('d M, Y') }}</td>
                            <td>₹{{ number_format($req->amount, 2) }}</td>
                            <td>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                    {{ $req->bank_details['bank_name'] ?? 'N/A' }}<br>
                                    {{ $req->bank_details['account_number'] ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                @if($req->status == 'pending')
                                    <span class="badge badge-pending">Pending</span>
                                @elseif($req->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @else
                                    <span class="badge" style="background: #fef2f2; color: #dc2626;">Rejected</span>
                                @endif
                            </td>
                            <td>{{ $req->admin_notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">No withdrawal requests found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding: 1rem;">
                {{ $requests->links() }}
            </div>
        </div>
    </div>

    <div>
        <div class="card" style="position: sticky; top: 2rem;">
            <div class="card-header">
                <h2>Request Withdrawal</h2>
            </div>
            <div style="padding: 1.5rem;">
                <div style="background: var(--primary-light); padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                    <div style="font-size: 0.875rem; color: var(--primary-hover); font-weight: 600;">Available Balance</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary-hover);">₹{{ number_format($wallet->balance, 2) }}</div>
                </div>

                <form action="{{ route('vendor.withdrawals.store') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Amount to Withdraw (₹)</label>
                        <input type="number" name="amount" min="500" max="{{ $wallet->balance }}" required 
                               style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;"
                               placeholder="Min ₹500">
                    </div>

                    @if($errors->any())
                        <div style="color: #dc2626; font-size: 0.75rem; margin-bottom: 1rem;">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;" {{ $wallet->balance < 500 ? 'disabled' : '' }}>
                        Submit Request
                    </button>
                    
                    @if($wallet->balance < 500)
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem; text-align: center;"> Minimum ₹500 required to withdraw. </p>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
