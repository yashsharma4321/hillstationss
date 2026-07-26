@extends('layouts.admin')

@section('header', 'Profit & Loss Statement')

@section('content')
<div class="content-card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header" style="text-align: center; display: block; border-bottom: 2px solid var(--border);">
        <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Profit & Loss Statement</h2>
        <div style="color: var(--text-muted); font-size: 0.875rem;">Year to Date</div>
    </div>
    
    <div style="padding: 2rem;">
        
        <!-- Revenue Section -->
        <div style="margin-bottom: 2rem;">
            <h3 style="font-size: 1.125rem; color: #1e293b; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin-bottom: 1rem;">Revenue</h3>
            @forelse($revenues as $rev)
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-left: 1rem;">
                    <div style="color: #334155;">{{ $rev['account']->name }}</div>
                    <div style="font-weight: 500;">₹{{ number_format($rev['balance'], 2) }}</div>
                </div>
            @empty
                <div style="padding-left: 1rem; color: var(--text-muted); font-style: italic;">No revenue recorded yet.</div>
            @endforelse
            <div style="display: flex; justify-content: space-between; font-weight: 700; color: var(--primary); background: #f0fdf4; padding: 1rem; border-radius: 0.5rem; margin-top: 1rem;">
                <div>Total Revenue</div>
                <div>₹{{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>

        <!-- Expenses Section -->
        <div style="margin-bottom: 2rem;">
            <h3 style="font-size: 1.125rem; color: #1e293b; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin-bottom: 1rem;">Expenses</h3>
            @forelse($expenses as $exp)
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-left: 1rem;">
                    <div style="color: #334155;">{{ $exp['account']->name }}</div>
                    <div style="font-weight: 500;">₹{{ number_format($exp['balance'], 2) }}</div>
                </div>
            @empty
                <div style="padding-left: 1rem; color: var(--text-muted); font-style: italic;">No expenses recorded yet.</div>
            @endforelse
            <div style="display: flex; justify-content: space-between; font-weight: 700; color: var(--danger); background: #fef2f2; padding: 1rem; border-radius: 0.5rem; margin-top: 1rem;">
                <div>Total Expenses</div>
                <div>₹{{ number_format($totalExpense, 2) }}</div>
            </div>
        </div>

        <!-- Net Profit / Loss -->
        <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800; padding: 1.5rem; background: {{ $netProfit >= 0 ? '#10b981' : '#ef4444' }}; color: white; border-radius: 0.75rem;">
            <div>Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</div>
            <div>₹{{ number_format(abs($netProfit), 2) }}</div>
        </div>

    </div>
</div>
@endsection
