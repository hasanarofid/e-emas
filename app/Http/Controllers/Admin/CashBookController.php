<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\CashTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CashBookController extends Controller
{
    /**
     * Menampilkan Buku Kas Toko Emas (Arus Kas Masuk & Kas Keluar)
     */
    public function index(Request $request): Response
    {
        $query = CashTransaction::with(['bankAccount', 'creator']);

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('bank_account_id')) {
            if ($request->bank_account_id === 'cash') {
                $query->whereNull('bank_account_id');
            } else {
                $query->where('bank_account_id', $request->bank_account_id);
            }
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $transactions = $query->orderByDesc('transaction_date')->orderByDesc('id')->paginate(15)->withQueryString();

        // Hitung Saldo & Mutasi
        $totalIn = (float) CashTransaction::where('type', 'in')->sum('amount');
        $totalOut = (float) CashTransaction::where('type', 'out')->sum('amount');
        $currentBalance = $totalIn - $totalOut;

        // Saldo Kasir Tunai vs Bank
        $cashDrawerIn = (float) CashTransaction::where('type', 'in')->whereNull('bank_account_id')->sum('amount');
        $cashDrawerOut = (float) CashTransaction::where('type', 'out')->whereNull('bank_account_id')->sum('amount');
        $cashDrawerBalance = $cashDrawerIn - $cashDrawerOut;

        $bankBalance = $currentBalance - $cashDrawerBalance;

        $bankAccounts = BankAccount::where('is_active', true)->get();

        return Inertia::render('Admin/Finance/CashBook/Index', [
            'transactions' => $transactions,
            'bankAccounts' => $bankAccounts,
            'filters' => $request->only(['start_date', 'end_date', 'type', 'bank_account_id', 'category']),
            'stats' => [
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'current_balance' => $currentBalance,
                'cash_drawer_balance' => $cashDrawerBalance,
                'bank_balance' => $bankBalance,
            ],
        ]);
    }

    /**
     * Form tambah transaksi arus kas toko (Kas Masuk / Kas Keluar)
     */
    public function create(): Response
    {
        $code = 'CSH-' . date('Ym') . '-' . str_pad((string) (CashTransaction::count() + 1), 4, '0', STR_PAD_LEFT);
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return Inertia::render('Admin/Finance/CashBook/Create', [
            'defaultCode' => $code,
            'bankAccounts' => $bankAccounts,
        ]);
    }

    /**
     * Simpan transaksi buku kas
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'transaction_number' => 'required|string|max:50|unique:cash_transactions,transaction_number',
            'type' => 'required|in:in,out',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        CashTransaction::create($validated);

        return redirect()->route('admin.cash-book.index')->with('success', 'Transaksi arus kas berhasil dicatat ke Buku Kas.');
    }

    /**
     * Soft delete transaksi kas
     */
    public function destroy(CashTransaction $cashTransaction): RedirectResponse
    {
        $cashTransaction->delete();

        return redirect()->route('admin.cash-book.index')->with('success', 'Transaksi kas berhasil dihapus (soft delete).');
    }
}
