@extends('layouts.admin')

@section('header', 'Withdrawal Requests')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2>Vendor Payout Requests</h2>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Amount</th>
                    <th>Bank Details</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ $req->vendor->business_name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $req->vendor->user->name }}</div>
                    </td>
                    <td style="font-weight: 700; color: var(--text-main);">₹{{ number_format($req->amount, 2) }}</td>
                    <td>
                        <div style="font-size: 0.8125rem; line-height: 1.4;">
                            <strong>{{ $req->bank_details['bank_name'] ?? 'N/A' }}</strong><br>
                            A/C: {{ $req->bank_details['account_number'] ?? 'N/A' }}<br>
                            IFSC: {{ $req->bank_details['ifsc_code'] ?? 'N/A' }}<br>
                            Name: {{ $req->bank_details['account_holder'] ?? 'N/A' }}
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
                    <td>{{ $req->created_at->format('d M, Y H:i') }}</td>
                    <td>
                        @if($req->status == 'pending')
                        <div style="display: flex; gap: 0.5rem;">
                            <button onclick="openModal('approve-{{ $req->id }}')" class="btn btn-success" style="padding: 0.4rem 0.875rem; font-size: 0.75rem;">Approve</button>
                            <button onclick="openModal('reject-{{ $req->id }}')" class="btn" style="background: #fef2f2; color: #dc2626; padding: 0.4rem 0.875rem; font-size: 0.75rem;">Reject</button>
                        </div>

                        <!-- Approve Modal -->
                        <div id="modal-approve-{{ $req->id }}" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center;">
                            <div style="background:white; padding:2rem; border-radius:1rem; width:400px; position:relative;">
                                <h3>Confirm Approval</h3>
                                <p style="margin: 1rem 0; font-size: 0.875rem; color: var(--text-muted);">Are you sure you want to approve this ₹{{ number_format($req->amount) }} payout? This will deduct the amount from vendor's wallet.</p>
                                <form action="{{ route('admin.withdrawals.update', $req) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <textarea name="admin_notes" placeholder="Admin notes (optional)" style="width:100%; padding:0.5rem; margin-bottom:1rem; border:1px solid var(--border); border-radius:0.5rem;"></textarea>
                                    <div style="display:flex; justify-content:flex-end; gap:1rem;">
                                        <button type="button" onclick="closeModal('approve-{{ $req->id }}')" class="btn" style="background:#f1f5f9;">Cancel</button>
                                        <button type="submit" class="btn btn-success">Yes, Approve</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        <div id="modal-reject-{{ $req->id }}" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center;">
                            <div style="background:white; padding:2rem; border-radius:1rem; width:400px; position:relative;">
                                <h3>Reject Request</h3>
                                <p style="margin: 1rem 0; font-size: 0.875rem; color: var(--text-muted);">Please provide a reason for rejection.</p>
                                <form action="{{ route('admin.withdrawals.update', $req) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <textarea name="admin_notes" required placeholder="Reason for rejection" style="width:100%; padding:0.5rem; margin-bottom:1rem; border:1px solid var(--border); border-radius:0.5rem;"></textarea>
                                    <div style="display:flex; justify-content:flex-end; gap:1rem;">
                                        <button type="button" onclick="closeModal('reject-{{ $req->id }}')" class="btn" style="background:#f1f5f9;">Cancel</button>
                                        <button type="submit" class="btn" style="background:#dc2626; color:white;">Reject Request</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @else
                            <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $req->admin_notes }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">No payout requests found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById('modal-' + id).style.display = 'flex';
    }
    function closeModal(id) {
        document.getElementById('modal-' + id).style.display = 'none';
    }
</script>
@endsection
