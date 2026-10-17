<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BankAccountController extends Controller
{
    /**
     * Menampilkan daftar rekening bank & transfer toko emas
     */
    public function index(Request $request): Response
    {
        $query = BankAccount::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bank_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('account_holder', 'like', "%{$search}%");
            });
        }

        $bankAccounts = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Settings/BankTransfer', [
            'bankAccounts' => $bankAccounts,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Simpan rekening bank baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:100',
            'account_holder' => 'required|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:255',
        ]);

        BankAccount::create($validated);

        return redirect()->back()->with('success', 'Rekening bank / metode transfer berhasil ditambahkan.');
    }

    /**
     * Update data rekening bank
     */
    public function update(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:100',
            'account_holder' => 'required|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:255',
        ]);

        $bankAccount->update($validated);

        return redirect()->back()->with('success', 'Rekening bank / metode transfer berhasil diperbarui.');
    }

    /**
     * Hapus rekening bank
     */
    public function destroy(BankAccount $bankAccount): RedirectResponse
    {
        $bankAccount->delete();

        return redirect()->back()->with('success', 'Rekening bank / metode transfer berhasil dihapus.');
    }
}
