<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\GoldRate;
use App\Models\PawnTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PawnController extends Controller
{
    /**
     * Menampilkan daftar transaksi gadai perhiasan emas
     */
    public function index(Request $request): Response
    {
        $query = PawnTransaction::with(['customer', 'goldRate', 'cashier']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pawn_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pawns = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Pawns/Index', [
            'pawns' => $pawns,
            'filters' => $request->only(['search', 'status']),
            'stats' => [
                'active_count' => PawnTransaction::where('status', 'active')->count(),
                'active_loan' => (float) PawnTransaction::where('status', 'active')->sum('loan_amount'),
                'redeemed_count' => PawnTransaction::where('status', 'redeemed')->count(),
            ],
        ]);
    }

    /**
     * Form pengajuan gadai baru
     */
    public function create(): Response
    {
        $customers = Customer::orderBy('name')->get();
        $goldRates = GoldRate::where('is_active', true)->orderByDesc('percentage')->get();
        $pawnNumber = 'GD-' . date('Ym') . '-' . str_pad((string) (PawnTransaction::count() + 1), 4, '0', STR_PAD_LEFT);

        return Inertia::render('Admin/Pawns/Create', [
            'customers' => $customers,
            'goldRates' => $goldRates,
            'suggestedNumber' => $pawnNumber,
        ]);
    }

    /**
     * Simpan gadai baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pawn_number' => 'required|string|max:50|unique:pawn_transactions,pawn_number',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'nullable|string|max:50',
            'item_name' => 'required|string|max:150',
            'gold_rate_id' => 'required|exists:gold_rates,id',
            'gross_weight' => 'required|numeric|min:0.001',
            'estimated_value' => 'required|numeric|min:0',
            'loan_amount' => 'required|numeric|min:0',
            'monthly_interest_rate' => 'required|numeric|min:0|max:100',
            'monthly_fee' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $validated['cashier_id'] = $request->user()?->id;
        $validated['status'] = 'active';

        PawnTransaction::create($validated);

        return redirect()->route('admin.pawns.index')
            ->with('success', "Transaksi gadai {$validated['pawn_number']} berhasil diterbitkan!");
    }

    /**
     * Pelunasan / Tebus gadai
     */
    public function redeem(Request $request, PawnTransaction $pawn): RedirectResponse
    {
        $validated = $request->validate([
            'settled_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $pawn->update([
            'status' => 'redeemed',
            'settled_date' => now()->toDateString(),
            'settled_amount' => $validated['settled_amount'],
            'notes' => $pawn->notes . "\n[Ditebus pada " . now()->format('d/m/Y H:i') . ': ' . ($validated['notes'] ?? 'Lunas') . ']',
        ]);

        return back()->with('success', "Gadai {$pawn->pawn_number} berhasil ditebus!");
    }

    /**
     * Hapus transaksi gadai (Soft Delete)
     */
    public function destroy(PawnTransaction $pawn): RedirectResponse
    {
        $pawn->delete();
        return back()->with('success', "Data gadai {$pawn->pawn_number} berhasil diarsipkan (Soft Delete)!");
    }
}
