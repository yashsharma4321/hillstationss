@extends('layouts.admin')

@section('header', 'Create Manual Journal Entry')

@section('content')
<div class="content-card">
    <div class="card-header">
        <h2>New Journal Entry</h2>
    </div>
    <div style="padding: 2rem;">
        <form action="{{ route('admin.accounting.store') }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; margin-bottom: 2rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Transaction Date</label>
                    <input type="date" name="transaction_date" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">Description</label>
                    <input type="text" name="description" required placeholder="Reason for this entry..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;">
                <h3 style="font-size: 1rem; margin-bottom: 1rem;">Entry Lines</h3>
                
                <table id="lines-table" style="width: 100%; margin-bottom: 1rem;">
                    <thead>
                        <tr>
                            <th style="padding: 0.5rem; background: transparent; border:none; text-transform: none;">Account</th>
                            <th style="padding: 0.5rem; background: transparent; border:none; text-transform: none;">Type</th>
                            <th style="padding: 0.5rem; background: transparent; border:none; text-transform: none;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 0.5rem; border:none;">
                                <select name="lines[0][account_head_id]" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                                    <option value="">Select Account...</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->code }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="padding: 0.5rem; border:none;">
                                <select name="lines[0][type]" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                                    <option value="debit">Debit (Dr)</option>
                                    <option value="credit">Credit (Cr)</option>
                                </select>
                            </td>
                            <td style="padding: 0.5rem; border:none;">
                                <input type="number" step="0.01" name="lines[0][amount]" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;" placeholder="0.00">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0.5rem; border:none;">
                                <select name="lines[1][account_head_id]" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                                    <option value="">Select Account...</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->code }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="padding: 0.5rem; border:none;">
                                <select name="lines[1][type]" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                                    <option value="credit">Credit (Cr)</option>
                                    <option value="debit">Debit (Dr)</option>
                                </select>
                            </td>
                            <td style="padding: 0.5rem; border:none;">
                                <input type="number" step="0.01" name="lines[1][amount]" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;" placeholder="0.00">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Ensure total debits equal total credits before submitting.</p>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="{{ route('admin.accounting.index') }}" class="btn" style="background: white; border: 1px solid var(--border); color: var(--text-main);">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Journal Entry</button>
            </div>
        </form>
    </div>
</div>
@endsection
