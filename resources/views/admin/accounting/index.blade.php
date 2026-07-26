@extends('layouts.admin')

@section('header', 'Chart of Accounts & Ledgers')

@section('content')
<div class="content-card mb-4" style="margin-bottom: 2rem;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Financial Overview</h2>
        <a href="{{ route('admin.accounting.create') }}" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Manual Entry
        </a>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
    <!-- Assets -->
    <div class="stat-card" style="border-top: 4px solid var(--primary);">
        <div class="stat-label" style="font-size: 1.1rem; color: #1e293b;">Assets</div>
        <div style="margin-top: 1rem;">
            @foreach($assets as $asset)
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);">
                <div>
                    <div style="font-weight: 600; color: #334155;">{{ $asset->name }} <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">({{ $asset->code }})</span></div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: 700; color: var(--primary);">₹{{ number_format($asset->balance, 2) }}</div>
                    <a href="{{ route('admin.accounting.ledger', $asset) }}" style="font-size: 0.75rem; color: var(--primary); text-decoration: none;">View Ledger &rarr;</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Liabilities -->
    <div class="stat-card" style="border-top: 4px solid var(--danger);">
        <div class="stat-label" style="font-size: 1.1rem; color: #1e293b;">Liabilities</div>
        <div style="margin-top: 1rem;">
            @foreach($liabilities as $liability)
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);">
                <div>
                    <div style="font-weight: 600; color: #334155;">{{ $liability->name }} <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">({{ $liability->code }})</span></div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: 700; color: var(--danger);">₹{{ number_format($liability->balance, 2) }}</div>
                    <a href="{{ route('admin.accounting.ledger', $liability) }}" style="font-size: 0.75rem; color: var(--primary); text-decoration: none;">View Ledger &rarr;</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Equity -->
    <div class="stat-card" style="border-top: 4px solid var(--warning);">
        <div class="stat-label" style="font-size: 1.1rem; color: #1e293b;">Equity & Revenue</div>
        <div style="margin-top: 1rem;">
            @foreach($equity as $eq)
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);">
                <div>
                    <div style="font-weight: 600; color: #334155;">{{ $eq->name }} <span style="font-size: 0.75rem; color: #94a3b8; font-weight: normal;">({{ $eq->code }})</span></div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: 700; color: var(--warning);">₹{{ number_format($eq->balance, 2) }}</div>
                    <a href="{{ route('admin.accounting.ledger', $eq) }}" style="font-size: 0.75rem; color: var(--primary); text-decoration: none;">View Ledger &rarr;</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
