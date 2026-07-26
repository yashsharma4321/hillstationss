<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountHead;
use App\Models\JournalEntry;

class AccountingController extends Controller
{
    public function index()
    {
        $accounts = AccountHead::with('journalLines')->get();
        
        $assets = $accounts->where('type', 'asset');
        $liabilities = $accounts->where('type', 'liability');
        $equity = $accounts->where('type', 'equity');

        return view('admin.accounting.index', compact('assets', 'liabilities', 'equity'));
    }

    public function create()
    {
        $accounts = AccountHead::where('is_active', true)->get();
        return view('admin.accounting.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'lines' => 'required|array|min:2',
            'lines.*.account_head_id' => 'required|exists:account_heads,id',
            'lines.*.type' => 'required|in:debit,credit',
            'lines.*.amount' => 'required|numeric|min:0.01',
        ]);

        $debits = collect($request->lines)->where('type', 'debit')->sum('amount');
        $credits = collect($request->lines)->where('type', 'credit')->sum('amount');

        if (abs($debits - $credits) > 0.01) {
            return back()->with('error', 'Debit and Credit totals must be equal.');
        }

        $journal = JournalEntry::create([
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
        ]);

        foreach ($request->lines as $line) {
            $journal->lines()->create($line);
        }

        return redirect()->route('admin.accounting.index')->with('success', 'Journal entry created successfully.');
    }

    public function ledger(AccountHead $account)
    {
        $lines = $account->journalLines()->with('journalEntry')->latest()->paginate(20);
        return view('admin.accounting.ledger', compact('account', 'lines'));
    }

    public function trialBalance()
    {
        $accounts = AccountHead::with('journalLines')->where('is_active', true)->get();
        
        $trialBalance = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $debits = $account->journalLines->where('type', 'debit')->sum('amount');
            $credits = $account->journalLines->where('type', 'credit')->sum('amount');

            // Calculate balance based on account type
            $balance = 0;
            $balanceType = '';

            if (in_array($account->type, ['asset', 'expense'])) {
                $balance = $debits - $credits;
                $balanceType = $balance >= 0 ? 'debit' : 'credit';
            } else {
                $balance = $credits - $debits;
                $balanceType = $balance >= 0 ? 'credit' : 'debit';
            }

            $absBalance = abs($balance);

            if ($absBalance > 0) {
                $trialBalance[] = [
                    'account' => $account,
                    'debit' => $balanceType === 'debit' ? $absBalance : 0,
                    'credit' => $balanceType === 'credit' ? $absBalance : 0,
                ];

                if ($balanceType === 'debit') {
                    $totalDebit += $absBalance;
                } else {
                    $totalCredit += $absBalance;
                }
            }
        }

        return view('admin.accounting.trial_balance', compact('trialBalance', 'totalDebit', 'totalCredit'));
    }

    public function profitAndLoss()
    {
        $accounts = AccountHead::with('journalLines')->whereIn('type', ['revenue', 'expense'])->get();

        $revenues = [];
        $expenses = [];
        $totalRevenue = 0;
        $totalExpense = 0;

        foreach ($accounts as $account) {
            $balance = abs($account->balance);
            if ($balance > 0) {
                if ($account->type === 'revenue') {
                    $revenues[] = ['account' => $account, 'balance' => $balance];
                    $totalRevenue += $balance;
                } else if ($account->type === 'expense') {
                    $expenses[] = ['account' => $account, 'balance' => $balance];
                    $totalExpense += $balance;
                }
            }
        }

        $netProfit = $totalRevenue - $totalExpense;

        return view('admin.accounting.profit_loss', compact('revenues', 'expenses', 'totalRevenue', 'totalExpense', 'netProfit'));
    }
}
