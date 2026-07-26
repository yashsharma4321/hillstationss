@extends('layouts.admin')

@section('header', 'Trial Balance (CA Report)')

@section('content')
<div class="content-card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Trial Balance as of {{ date('d M Y') }}</h2>
        <button onclick="window.print()" class="btn" style="background: white; border: 1px solid var(--border); color: var(--text-main);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Print Report
        </button>
    </div>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 1rem 2rem; border-bottom: 2px solid var(--border); background: #f8fafc; font-weight: 600;">Account Code & Name</th>
                    <th style="padding: 1rem 2rem; border-bottom: 2px solid var(--border); background: #f8fafc; text-align: right; font-weight: 600;">Debit (Dr)</th>
                    <th style="padding: 1rem 2rem; border-bottom: 2px solid var(--border); background: #f8fafc; text-align: right; font-weight: 600;">Credit (Cr)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trialBalance as $row)
                <tr>
                    <td style="padding: 1rem 2rem; border-bottom: 1px solid var(--border);">
                        <strong>{{ $row['account']->code }}</strong> - {{ $row['account']->name }}
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ ucfirst($row['account']->type) }}</div>
                    </td>
                    <td style="padding: 1rem 2rem; text-align: right; border-bottom: 1px solid var(--border); color: var(--primary); font-weight: 500;">
                        {{ $row['debit'] > 0 ? '₹' . number_format($row['debit'], 2) : '-' }}
                    </td>
                    <td style="padding: 1rem 2rem; text-align: right; border-bottom: 1px solid var(--border); color: var(--danger); font-weight: 500;">
                        {{ $row['credit'] > 0 ? '₹' . number_format($row['credit'], 2) : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 2rem;">No entries available for trial balance.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background: #f1f5f9;">
                    <td style="padding: 1rem 2rem; text-align: right; font-weight: 700; font-size: 1.1rem; border-top: 2px solid var(--border);">Total</td>
                    <td style="padding: 1rem 2rem; text-align: right; font-weight: 700; font-size: 1.1rem; color: var(--primary); border-top: 2px solid var(--border);">₹{{ number_format($totalDebit, 2) }}</td>
                    <td style="padding: 1rem 2rem; text-align: right; font-weight: 700; font-size: 1.1rem; color: var(--danger); border-top: 2px solid var(--border);">₹{{ number_format($totalCredit, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <!-- Balance Check -->
    <div style="padding: 1.5rem 2rem; background: {{ round($totalDebit, 2) === round($totalCredit, 2) ? '#ecfdf5' : '#fef2f2' }}; text-align: center; border-radius: 0 0 1.5rem 1.5rem;">
        @if(round($totalDebit, 2) === round($totalCredit, 2))
            <h3 style="color: #065f46; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                Ledger is Balanced
            </h3>
        @else
            <h3 style="color: #991b1b; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                Ledger is Out of Balance! Difference: ₹{{ number_format(abs($totalDebit - $totalCredit), 2) }}
            </h3>
        @endif
    </div>
</div>
@endsection
