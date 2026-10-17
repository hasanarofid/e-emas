<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuybackDeduction;
use App\Models\Category;
use App\Models\GoldRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BuybackDeductionController extends Controller
{
    /**
     * Menampilkan pengaturan data master potongan harga beli (Buy Back) persis Sitokmas
     */
    public function index(): Response
    {
        $deductions = BuybackDeduction::with(['goldRate', 'category'])
            ->latest('id')
            ->get();

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $goldRates = GoldRate::where('is_active', true)->orderByDesc('percentage')->get();

        return Inertia::render('Admin/Purchases/Deductions', [
            'deductions' => $deductions,
            'categories' => $categories,
            'goldRates' => $goldRates,
        ]);
    }

    /**
     * Tambah data aturan potongan baru (+ Tambah Data)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'condition_status' => 'required|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'gold_rate_id' => 'nullable|exists:gold_rates,id',
            'deduction_amount' => 'required|numeric|min:0',
            'deduction_type' => 'required|in:nominal,percentage',
            'deduct_by_weight' => 'boolean',
        ]);

        BuybackDeduction::create($validated);

        return back()->with('success', 'Aturan potongan buyback baru berhasil ditambahkan!');
    }

    /**
     * Simpan / Perbarui aturan potongan baris (Simpan)
     */
    public function update(Request $request, BuybackDeduction $buybackDeduction): RedirectResponse
    {
        $validated = $request->validate([
            'condition_status' => 'required|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'gold_rate_id' => 'nullable|exists:gold_rates,id',
            'deduction_amount' => 'required|numeric|min:0',
            'deduction_type' => 'required|in:nominal,percentage',
            'deduct_by_weight' => 'boolean',
        ]);

        $buybackDeduction->update($validated);

        return back()->with('success', "Aturan potongan \"{$buybackDeduction->condition_status}\" berhasil disimpan!");
    }

    /**
     * Hapus aturan potongan (Hapus - Soft Delete)
     */
    public function destroy(BuybackDeduction $buybackDeduction): RedirectResponse
    {
        $buybackDeduction->delete();

        return back()->with('success', 'Aturan potongan berhasil dihapus (Soft Delete)!');
    }
}
