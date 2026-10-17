<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\CashTransaction;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    /**
     * Menampilkan daftar data pengeluaran operasional toko emas
     */
    public function index(Request $request): Response
    {
        $query = Expense::with(['bankAccount', 'creator']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->end_date);
        }

        // Filter periode cepat Sitokmas (daily / monthly)
        if ($request->period === 'daily') {
            $query->whereDate('expense_date', date('Y-m-d'));
        } elseif ($request->period === 'monthly') {
            $query->whereMonth('expense_date', date('n'))
                  ->whereYear('expense_date', date('Y'));
        }

        $expenses = $query->orderByDesc('expense_date')->paginate(12)->withQueryString();

        $categories = [
            'Operasional Toko',
            'Listrik, Air & Internet',
            'Konsumsi & Snack Karyawan',
            'Kemasan & Kotak Emas/Pouch',
            'Pajak, Retribusi & Izin Usaha',
            'Marketing, Iklan & Promosi',
            'Alat & Bahan Tukang Servis Emas',
            'Keamanan & Kebersihan',
            'Lain-lain',
        ];

        return Inertia::render('Admin/Finance/Expenses/Index', [
            'expenses' => $expenses,
            'categories' => $categories,
            'filters' => $request->only(['category', 'start_date', 'end_date', 'period']),
            'stats' => [
                'total_expenses' => (float) Expense::sum('amount'),
                'today_expenses' => (float) Expense::whereDate('expense_date', date('Y-m-d'))->sum('amount'),
                'month_expenses' => (float) Expense::whereMonth('expense_date', date('n'))->whereYear('expense_date', date('Y'))->sum('amount'),
            ],
        ]);
    }

    /**
     * Form tambah pengeluaran operasional toko
     */
    public function create(): Response
    {
        $code = 'EXP-' . date('Ym') . '-' . str_pad((string) (Expense::count() + 1), 4, '0', STR_PAD_LEFT);
        $bankAccounts = BankAccount::where('is_active', true)->get();

        $categories = [
            'Operasional Toko',
            'Listrik, Air & Internet',
            'Konsumsi & Snack Karyawan',
            'Kemasan & Kotak Emas/Pouch',
            'Pajak, Retribusi & Izin Usaha',
            'Marketing, Iklan & Promosi',
            'Alat & Bahan Tukang Servis Emas',
            'Keamanan & Kebersihan',
            'Lain-lain',
        ];

        return Inertia::render('Admin/Finance/Expenses/Create', [
            'defaultCode' => $code,
            'bankAccounts' => $bankAccounts,
            'categories' => $categories,
        ]);
    }

    /**
     * Simpan pengeluaran & catat arus keluar ke buku kas toko
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expense_number' => 'required|string|max:50|unique:expenses,expense_number',
            'category' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:tunai,transfer',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'recipient' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $expense = Expense::create($validated);

        // Catat pengeluaran ke Buku Kas Toko (Outflow)
        CashTransaction::create([
            'transaction_number' => 'CSH-OUT-' . substr(md5(uniqid()), 0, 8),
            'type' => 'out',
            'category' => 'pengeluaran',
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
            'bank_account_id' => $expense->payment_method === 'transfer' ? $expense->bank_account_id : null,
            'amount' => $expense->amount,
            'transaction_date' => $expense->expense_date,
            'description' => "Pengeluaran [{$expense->category}]: {$expense->title}",
            'notes' => $expense->notes,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.expenses.index')->with('success', 'Pengeluaran berhasil dicatat dan masuk ke Buku Kas.');
    }

    /**
     * Soft delete pengeluaran
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        // Hapus juga transaksi buku kas yang tertaut
        CashTransaction::where('reference_type', Expense::class)
            ->where('reference_id', $expense->id)
            ->delete();

        $expense->delete();

        return redirect()->route('admin.expenses.index')->with('success', 'Pengeluaran berhasil dihapus (soft delete).');
    }
}
