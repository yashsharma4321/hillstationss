@extends('layouts.admin')

@section('header', 'General Ledger: ' . $account->name)

@section('content')
<div class="content-card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2>{{ $account->name }}</h2>
            <div style="font-size: 0.875rem; color: var(--text-muted);">Account Code: {{ $account->code }} | Type: {{ ucfirst($account->type) }}</div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 0.875rem; color: var(--text-muted);">Current Balance</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: {{ $account->balance >= 0 ? 'var(--primary)' : 'var(--danger)' }};">
                ₹{{ number_format($account->balance, 2) }}
            </div>
        </div>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Description</th>
                    <th>Debit (Dr)</th>
                    <th>Credit (Cr)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lines as $line)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($line->journalEntry->transaction_date)->format('d M, Y') }}</td>
                    <td>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">
                            @if($line->journalEntry->reference_type)
                                {{ class_basename($line->journalEntry->reference_type) }} #{{ $line->journalEntry->reference_id }}
                            @else
                                Manual Entry
                            @endif
                        </span>
                    </td>
                    <td>{{ $line->journalEntry->description }}</td>
                    <td style="font-weight: 600; color: var(--primary);">
                        {{ $line->type === 'debit' ? '₹' . number_format($line->amount, 2) : '-' }}
                    </td>
                    <td style="font-weight: 600; color: var(--danger);">
                        {{ $line->type === 'credit' ? '₹' . number_format($line->amount, 2) : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        No entries found in this ledger.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 1.5rem;">
        {{ $lines->links() }}
    </div>
</div>
@endsection
