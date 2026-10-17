<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\CashTransaction;
use App\Models\DebtPayable;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DebtController extends Controller
{
    /**
     * Menampilkan daftar utang supplier emas & outstanding kredit
     */
    public function index(Request $request): Response
    {
        $query = DebtPayable::with(['supplier', 'purchase']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $debts = $query->orderBy('due_date')->paginate(10)->withQueryString();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return Inertia::render('Admin/Finance/Debts/Index', [
            'debts' => $debts,
            'suppliers' => $suppliers,
            'bankAccounts' => $bankAccounts,
            'filters' => $request->only(['supplier_id', 'status']),
            'stats' => [
                'total_outstanding' => (float) DebtPayable::whereIn('status', ['unpaid', 'partial'])->sum('remaining_amount'),
                'total_paid' => (float) DebtPayable::sum('paid_amount'),
                'unpaid_count' => DebtPayable::whereIn('status', ['unpaid', 'partial'])->count(),
            ],
        ]);
    }

    /**
     * Pembayaran cicilan / pelunasan utang ke supplier pabrik emas
     */
    public function pay(Request $request, DebtPayable $debt): RedirectResponse
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:1|max:' . $debt->remaining_amount,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:tunai,transfer',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'notes' => 'nullable|string',
        ]);

        $newPaidAmount = $debt->paid_amount + $validated['payment_amount'];
        $newRemaining = $debt->total_debt - $newPaidAmount;
        $status = $newRemaining <= 0 ? 'paid' : 'partial';

        $debt->update([
            'paid_amount' => $newPaidAmount,
            'remaining_amount' => max(0, $newRemaining),
            'status' => $status,
            'last_payment_date' => $validated['payment_date'],
        ]);

        // Catat pengeluaran di Buku Kas
        CashTransaction::create([
            'transaction_number' => 'CSH-PAY-' . substr(md5(uniqid()), 0, 8),
            'type' => 'out',
            'category' => 'pembayaran_utang',
            'reference_type' => DebtPayable::class,
            'reference_id' => $debt->id,
            'bank_account_id' => $validated['payment_method'] === 'transfer' ? $validated['bank_account_id'] : null,
            'amount' => $validated['payment_amount'],
            'transaction_date' => $validated['payment_date'],
            'description' => "Bayar Utang Emas ke Supplier [{$debt->supplier->name}] - {$debt->payable_code}",
            'notes' => $validated['notes'],
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.debts.index')->with('success', 'Pembayaran utang supplier berhasil dicatat dan dipotong dari kas toko.');
    }

    /**
     * Soft delete utang
     */
    public function destroy(DebtPayable $debt): RedirectResponse
    {
        $debt->delete();

        return redirect()->route('admin.debts.index')->with('success', 'Data utang berhasil diarsipkan (soft delete).');
    }
}
