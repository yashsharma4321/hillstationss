@extends('layouts.admin')

@section('header', 'Promotion Coupons')

@section('content')
<div style="background: white; border: 1px solid var(--border); border-radius: 0.75rem; overflow: hidden;">
    <div style="padding: 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--text-main);">Manage Coupons</h3>
            <p style="font-size: 0.875rem; color: var(--text-muted);">Create and manage discount codes for customers</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" style="background: var(--primary); color: white; padding: 0.6rem 1.25rem; border-radius: 0.5rem; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Add New Coupon
        </a>
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Code</th>
                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Discount</th>
                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Global</th>
                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Status</th>
                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Expiry</th>
                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Usage</th>
                <th style="padding: 1rem 1.5rem; font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($coupons as $coupon)
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 1rem 1.5rem;">
                    <span style="font-family: monospace; background: #eef2ff; color: #4338ca; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-weight: 700; border: 1px dashed #c7d2fe;">{{ $coupon->code }}</span>
                </td>
                <td style="padding: 1rem 1.5rem;">
                    <div style="font-weight: 600; color: var(--text-main);">
                        @if($coupon->type == 'fixed') ₹{{ number_format($coupon->value) }} OFF @else {{ $coupon->value }}% OFF @endif
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Min. ₹{{ number_format($coupon->min_purchase) }}</div>
                </td>
                <td style="padding: 1rem 1.5rem;">
                    @if($coupon->is_global)
                        <span style="background: #ecfdf5; color: #065f46; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Global</span>
                    @else
                        <span style="color: var(--text-muted); font-size: 0.75rem;">Private</span>
                    @endif
                </td>
                <td style="padding: 1rem 1.5rem;">
                    @if($coupon->is_active && (!$coupon->expires_at || $coupon->expires_at->isFuture()))
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; color: #059669; font-weight: 600; font-size: 0.875rem;">
                            <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></span>
                            Active
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; color: #dc2626; font-weight: 600; font-size: 0.875rem;">
                            <span style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></span>
                            Expired/Disabled
                        </span>
                    @endif
                </td>
                <td style="padding: 1rem 1.5rem; font-size: 0.875rem; color: var(--text-main);">
                    {{ $coupon->expires_at ? $coupon->expires_at->format('d M, Y') : 'Never' }}
                </td>
                <td style="padding: 1rem 1.5rem; font-size: 0.875rem; color: var(--text-main);">
                    {{ $coupon->used_count }} / {{ $coupon->usage_limit ?: '∞' }}
                </td>
                <td style="padding: 1rem 1.5rem;">
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" style="padding: 0.4rem; background: #f1f5f9; color: #475569; border-radius: 0.375rem; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; justify-content: center;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Delete this coupon?')" style="display: inline;">
                            @csrf @method('DELETE')
                            <button type="submit" style="padding: 0.4rem; background: #fef2f2; color: #dc2626; border-radius: 0.375rem; border: 1px solid #fee2e2; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="padding: 3rem; text-align: center; color: var(--text-muted);">No coupons found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid var(--border);">
        {{ $coupons->links() }}
    </div>
</div>
@endsection
