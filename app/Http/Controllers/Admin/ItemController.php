<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GoldRate;
use App\Models\Item;
use App\Models\Setting;
use App\Models\Tray;
use App\Models\TrayMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    /**
     * Menampilkan katalog inventaris perhiasan
     */
    public function index(Request $request): Response
    {
        $query = Item::with(['category', 'goldRate', 'tray']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('gold_rate_id')) {
            $query->where('gold_rate_id', $request->gold_rate_id);
        }

        if ($request->filled('tray_id')) {
            $query->where('tray_id', $request->tray_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'available');
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        $categories = Category::where('is_active', true)->get();
        $goldRates = GoldRate::where('is_active', true)->orderByDesc('percentage')->get();
        $trays = Tray::where('status', 'active')->orderBy('code')->get();

        return Inertia::render('Admin/Items/Index', [
            'items' => $items,
            'categories' => $categories,
            'goldRates' => $goldRates,
            'trays' => $trays,
            'filters' => $request->only(['search', 'category_id', 'gold_rate_id', 'tray_id', 'status']),
            'stats' => [
                'total_available' => Item::where('status', 'available')->count(),
                'total_weight' => (float)Item::where('status', 'available')->sum('gross_weight'),
                'total_valuation' => (float)Item::where('status', 'available')->sum('total_price'),
            ],
        ]);
    }

    /**
     * Simpan perhiasan baru ke database dan baki
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => 'nullable|string|max:50|unique:items,sku',
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'gold_rate_id' => 'required|exists:gold_rates,id',
            'tray_id' => 'nullable|exists:trays,id',
            'gross_weight' => 'required|numeric|min:0.001',
            'stone_weight' => 'nullable|numeric|min:0',
            'crafting_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Auto-generate SKU unik jika tidak diisi kasir
        if (empty($validated['sku'])) {
            $goldRate = GoldRate::findOrFail($validated['gold_rate_id']);
            $validated['sku'] = 'EMAS-' . $goldRate->code . '-' . strtoupper(Str::random(5));
        }

        $gross = (float)$validated['gross_weight'];
        $stone = (float)($validated['stone_weight'] ?? 0);
        $net = max(0.001, round($gross - $stone, 3));
        $fee = (float)($validated['crafting_fee'] ?? 0);

        // Hitung total harga dasar jual berdasarkan live gold rate
        $goldRate = GoldRate::findOrFail($validated['gold_rate_id']);
        $totalPrice = round(((float)$goldRate->sell_price_per_gram * $net) + $fee, 2);

        $item = Item::create([
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'gold_rate_id' => $validated['gold_rate_id'],
            'tray_id' => $validated['tray_id'] ?? null,
            'gross_weight' => $gross,
            'stone_weight' => $stone,
            'net_weight' => $net,
            'crafting_fee' => $fee,
            'total_price' => $totalPrice,
            'status' => 'available',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Catat mutasi baki awal & rekalkulasi baki
        if ($item->tray_id) {
            TrayMovement::create([
                'item_id' => $item->id,
                'from_tray_id' => null,
                'to_tray_id' => $item->tray_id,
                'moved_by' => $request->user()->id,
                'reason' => 'Pemasukan stok awal',
            ]);

            $tray = Tray::find($item->tray_id);
            $tray?->recalculateTotals();
        }

        return redirect()->back()->with('success', "Perhiasan {$item->sku} berhasil ditambahkan ke inventaris.");
    }

    /**
     * Update data perhiasan
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:items,sku,' . $item->id,
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'gold_rate_id' => 'required|exists:gold_rates,id',
            'tray_id' => 'nullable|exists:trays,id',
            'gross_weight' => 'required|numeric|min:0.001',
            'stone_weight' => 'nullable|numeric|min:0',
            'crafting_fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,sold,buyback,repair,melted',
            'notes' => 'nullable|string',
        ]);

        $oldTrayId = $item->tray_id;
        $newTrayId = $validated['tray_id'] ?? null;

        $gross = (float)$validated['gross_weight'];
        $stone = (float)($validated['stone_weight'] ?? 0);
        $net = max(0.001, round($gross - $stone, 3));
        $fee = (float)($validated['crafting_fee'] ?? 0);

        $goldRate = GoldRate::findOrFail($validated['gold_rate_id']);
        $totalPrice = round(((float)$goldRate->sell_price_per_gram * $net) + $fee, 2);

        $item->update([
            'sku' => $validated['sku'],
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'gold_rate_id' => $validated['gold_rate_id'],
            'tray_id' => $newTrayId,
            'gross_weight' => $gross,
            'stone_weight' => $stone,
            'net_weight' => $net,
            'crafting_fee' => $fee,
            'total_price' => $totalPrice,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Cek jika baki berpindah
        if ($oldTrayId !== $newTrayId) {
            if ($newTrayId) {
                TrayMovement::create([
                    'item_id' => $item->id,
                    'from_tray_id' => $oldTrayId,
                    'to_tray_id' => $newTrayId,
                    'moved_by' => $request->user()->id,
                    'reason' => 'Perubahan baki dari edit item',
                ]);
            }

            if ($oldTrayId) {
                $oldTray = Tray::find($oldTrayId);
                $oldTray?->recalculateTotals();
            }
            if ($newTrayId) {
                $newTray = Tray::find($newTrayId);
                $newTray?->recalculateTotals();
            }
        } elseif ($newTrayId) {
            $tray = Tray::find($newTrayId);
            $tray?->recalculateTotals();
        }

        return redirect()->back()->with('success', "Data perhiasan {$item->sku} berhasil diperbarui.");
    }

    /**
     * Hapus perhiasan
     */
    public function destroy(Item $item): RedirectResponse
    {
        if ($item->status === 'sold') {
            return redirect()->back()->with('error', 'Perhiasan yang sudah terjual tidak dapat dihapus karena tercatat pada faktur penjualan.');
        }

        $trayId = $item->tray_id;
        $sku = $item->sku;
        $item->delete();

        if ($trayId) {
            $tray = Tray::find($trayId);
            $tray?->recalculateTotals();
        }

        return redirect()->back()->with('success', "Perhiasan {$sku} berhasil dihapus dari inventaris.");
    }

    /**
     * Preview cetak stiker barcode tag perhiasan (Jewelry Tag Halter)
     */
    public function printTag(Item $item): Response
    {
        $item->load(['category', 'goldRate', 'tray']);
        
        $storeSettings = [
            'store_name' => Setting::get('store_name', 'Toko Mas Permata Abadi'),
            'store_phone' => Setting::get('store_phone', '0812-3456-7890'),
        ];

        return Inertia::render('Admin/Items/PrintTag', [
            'item' => $item,
            'storeSettings' => $storeSettings,
        ]);
    }
}
