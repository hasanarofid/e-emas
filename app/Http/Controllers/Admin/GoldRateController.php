<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldRate;
use App\Models\GoldRateHistory;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GoldRateController extends Controller
{
    /**
     * Menampilkan daftar kadar dan form update harga harian
     */
    public function index(): Response
    {
        $goldRates = GoldRate::with(['updatedBy'])
            ->orderByDesc('percentage')
            ->get();

        $recentHistories = GoldRateHistory::with(['goldRate', 'changedBy'])
            ->latest()
            ->take(20)
            ->get();

        return Inertia::render('Admin/GoldRates/Index', [
            'goldRates' => $goldRates,
            'recentHistories' => $recentHistories,
        ]);
    }

    /**
     * Update cepat harga emas harian (bisa per kadar atau serentak)
     */
    public function updatePrices(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rates' => 'required|array',
            'rates.*.id' => 'required|exists:gold_rates,id',
            'rates.*.buy_price_per_gram' => 'required|numeric|min:0',
            'rates.*.sell_price_per_gram' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $notes = $validated['notes'] ?? 'Update harga harian oleh ' . $user->name;

        foreach ($validated['rates'] as $rateData) {
            $goldRate = GoldRate::findOrFail($rateData['id']);

            // Cek jika harga berubah
            $priceChanged = (float)$goldRate->buy_price_per_gram !== (float)$rateData['buy_price_per_gram'] ||
                            (float)$goldRate->sell_price_per_gram !== (float)$rateData['sell_price_per_gram'];

            if ($priceChanged) {
                $goldRate->update([
                    'buy_price_per_gram' => $rateData['buy_price_per_gram'],
                    'sell_price_per_gram' => $rateData['sell_price_per_gram'],
                    'updated_by' => $user->id,
                ]);

                // Catat log histori
                GoldRateHistory::create([
                    'gold_rate_id' => $goldRate->id,
                    'buy_price_per_gram' => $rateData['buy_price_per_gram'],
                    'sell_price_per_gram' => $rateData['sell_price_per_gram'],
                    'changed_by' => $user->id,
                    'notes' => $notes,
                ]);

                // Update estimasi harga jual pada seluruh perhiasan tersedia dengan kadar ini
                $items = Item::where('gold_rate_id', $goldRate->id)
                    ->where('status', 'available')
                    ->get();

                foreach ($items as $item) {
                    $newPrice = round(((float)$rateData['sell_price_per_gram'] * (float)$item->net_weight) + (float)$item->crafting_fee, 2);
                    $item->update(['total_price' => $newPrice]);
                }
            }
        }

        return redirect()->back()->with('success', 'Harga emas harian berhasil diperbarui dan disinkronkan ke seluruh inventaris.');
    }
}
