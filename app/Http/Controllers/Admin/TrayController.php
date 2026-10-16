<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GoldRate;
use App\Models\Item;
use App\Models\Tray;
use App\Models\TrayMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrayController extends Controller
{
    /**
     * Menampilkan overview denah baki etalase
     */
    public function index(Request $request): Response
    {
        $query = Tray::with(['category', 'goldRate'])
            ->withCount(['items' => function ($q) {
                $q->where('status', 'available');
            }]);

        if ($request->filled('location')) {
            $query->where('showcase_location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $trays = $query->orderBy('code')->get();

        $categories = Category::where('is_active', true)->get();
        $goldRates = GoldRate::where('is_active', true)->get();

        return Inertia::render('Admin/Trays/Index', [
            'trays' => $trays,
            'categories' => $categories,
            'goldRates' => $goldRates,
            'filters' => $request->only(['location', 'category_id']),
            'stats' => [
                'total_trays' => $trays->count(),
                'total_items_in_trays' => $trays->sum('total_items'),
                'total_weight_in_trays' => (float)$trays->sum('total_weight'),
            ],
        ]);
    }

    /**
     * Detail baki etalase dan daftar perhiasan di dalamnya
     */
    public function show(Tray $tray): Response
    {
        $tray->load(['category', 'goldRate']);
        
        $items = Item::with(['category', 'goldRate'])
            ->where('tray_id', $tray->id)
            ->where('status', 'available')
            ->latest()
            ->get();

        $availableTrays = Tray::where('id', '!=', $tray->id)
            ->where('status', 'active')
            ->orderBy('code')
            ->get();

        return Inertia::render('Admin/Trays/Show', [
            'tray' => $tray,
            'items' => $items,
            'availableTrays' => $availableTrays,
        ]);
    }

    /**
     * Simpan baki etalase baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:trays,code',
            'name' => 'required|string|max:100',
            'showcase_location' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'gold_rate_id' => 'nullable|exists:gold_rates,id',
            'capacity' => 'required|integer|min:1|max:200',
            'notes' => 'nullable|string',
        ]);

        Tray::create($validated);

        return redirect()->back()->with('success', 'Baki etalase berhasil ditambahkan.');
    }

    /**
     * Update data baki etalase
     */
    public function update(Request $request, Tray $tray): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:trays,code,' . $tray->id,
            'name' => 'required|string|max:100',
            'showcase_location' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'gold_rate_id' => 'nullable|exists:gold_rates,id',
            'capacity' => 'required|integer|min:1|max:200',
            'status' => 'required|in:active,archived,in_audit',
            'notes' => 'nullable|string',
        ]);

        $tray->update($validated);
        $tray->recalculateTotals();

        return redirect()->back()->with('success', 'Data baki berhasil diperbarui.');
    }

    /**
     * Pindahkan perhiasan antar baki (Scan barcode atau pilih baki tujuan)
     */
    public function moveItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_sku' => 'required|string|exists:items,sku',
            'to_tray_id' => 'required|exists:trays,id',
            'reason' => 'nullable|string|max:255',
        ]);

        $item = Item::where('sku', $validated['item_sku'])->firstOrFail();
        $targetTray = Tray::findOrFail($validated['to_tray_id']);
        $sourceTrayId = $item->tray_id;

        if ($sourceTrayId === $targetTray->id) {
            return redirect()->back()->with('error', 'Perhiasan ini sudah berada di baki tersebut.');
        }

        // Cek kapasitas baki tujuan
        if ($targetTray->total_items >= $targetTray->capacity) {
            return redirect()->back()->with('error', "Baki {$targetTray->code} sudah penuh (Kapasitas: {$targetTray->capacity} slot).");
        }

        // Update posisi baki perhiasan
        $item->update(['tray_id' => $targetTray->id]);

        // Catat mutasi baki
        TrayMovement::create([
            'item_id' => $item->id,
            'from_tray_id' => $sourceTrayId,
            'to_tray_id' => $targetTray->id,
            'moved_by' => $request->user()->id,
            'reason' => $validated['reason'] ?? 'Pemindahan etalase',
        ]);

        // Rekalkulasi kedua baki
        if ($sourceTrayId) {
            $sourceTray = Tray::find($sourceTrayId);
            $sourceTray?->recalculateTotals();
        }
        $targetTray->recalculateTotals();

        return redirect()->back()->with('success', "Perhiasan {$item->sku} berhasil dipindahkan ke baki {$targetTray->code}.");
    }

    /**
     * Hapus baki kosong
     */
    public function destroy(Tray $tray): RedirectResponse
    {
        if ($tray->items()->where('status', 'available')->exists()) {
            return redirect()->back()->with('error', 'Baki tidak dapat dihapus karena masih memuat perhiasan aktif.');
        }

        $tray->delete();

        return redirect()->route('admin.trays.index')->with('success', 'Baki berhasil dihapus.');
    }
}
