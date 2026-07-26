@extends('layouts.vendor')

@section('styles')
<style>
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem; }
    .stat-card { background: white; padding: 1.75rem; border-radius: 1.25rem; border: 1px solid var(--border); box-shadow: var(--shadow); position: relative; overflow: hidden; }
    .stat-label { font-size: 0.875rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem; }
    .stat-value { font-size: 2rem; font-weight: 800; color: var(--text-main); }
    .stat-card::after { content: ''; position: absolute; right: 0; top: 0; bottom: 0; width: 4px; background: currentColor; opacity: 0.1; }
</style>
@endsection

@section('content')
<div class="stats-grid">
    <div class="stat-card" style="border-left: 4px solid var(--success);">
        <div class="stat-label">Total Earned</div>
        <div class="stat-value">₹{{ number_format($stats['total_earned'], 2) }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid var(--primary);">
        <div class="stat-label">Wallet Balance</div>
        <div class="stat-value">₹{{ number_format($stats['current_balance'], 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Bookings</div>
        <div class="stat-value">{{ $stats['total_bookings'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Properties</div>
        <div class="stat-value">{{ $stats['total_properties'] }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Quick Actions</h2>
    </div>
    <div style="padding: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="{{ route('vendor.properties.create') }}" class="btn btn-primary">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Add New Property
        </a>
        <a href="{{ route('vendor.withdrawals.index') }}" class="btn" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            Withdraw Funds
        </a>
    </div>
</div>
@endsection
