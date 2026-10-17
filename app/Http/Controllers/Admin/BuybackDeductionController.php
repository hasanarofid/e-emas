<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuybackDeduction;
use App\Models\GoldRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BuybackDeductionController extends Controller
{
    /**
     * Menampilkan pengaturan master potongan buyback per kadar emas
     */
    public function index(): Response
    {
        $deductions = BuybackDeduction::with('goldRate')
            ->join('gold_rates', 'buyback_deductions.gold_rate_id', '=', 'gold_rates.id')
            ->orderByDesc('gold_rates.percentage')
            ->select('buyback_deductions.*')
            ->get();

        return Inertia::render('Admin/Purchases/Deductions', [
            'deductions' => $deductions,
        ]);
    }

    /**
     * Update aturan potongan buyback
     */
    public function update(Request $request, BuybackDeduction $buybackDeduction): RedirectResponse
    {
        $validated = $request->validate([
            'deduction_percentage' => 'required|numeric|min:0|max:100',
            'deduction_fee_per_gram' => 'required|numeric|min:0',
            'crafting_fee_deducted' => 'boolean',
            'condition_notes' => 'nullable|string',
        ]);

        $buybackDeduction->update($validated);

        return back()->with('success', "Aturan potongan buyback untuk {$buybackDeduction->goldRate->name} berhasil diperbarui!");
    }
}
