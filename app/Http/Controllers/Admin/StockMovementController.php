<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\Tray;
use App\Models\TrayMovement;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class StockMovementController extends Controller
{
    /**
     * Menampilkan riwayat mutasi barang (antar baki / etalase)
     */
    public function mutations(Request $request): Response
    {
        $query = TrayMovement::with(['item.category', 'item.goldRate', 'fromTray', 'toTray', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $mutations = $query->latest()->paginate(15)->withQueryString();
        $trays = Tray::where('status', 'active')->orderBy('code')->get();
        $items = Item::where('status', 'available')->orderBy('name')->get(['id', 'sku', 'name', 'tray_id', 'gross_weight', 'net_weight']);

        return Inertia::render('Admin/Inventory/Mutations', [
            'mutations' => $mutations,
            'trays' => $trays,
            'items' => $items,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Menampilkan riwayat keluar masuk barang (stock movements)
     */
    public function movements(Request $request): Response
    {
        $query = StockMovement::with(['item.category', 'item.goldRate', 'warehouse', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('item', function ($iq) use ($search) {
                      $iq->where('sku', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $movements = $query->latest()->paginate(15)->withQueryString();
        $warehouses = Warehouse::where('is_active', true)->get();
        $items = Item::orderBy('name')->get(['id', 'sku', 'name', 'gross_weight', 'net_weight', 'status']);

        return Inertia::render('Admin/Inventory/Movements', [
            'movements' => $movements,
            'warehouses' => $warehouses,
            'items' => $items,
            'filters' => $request->only(['type', 'search']),
        ]);
    }

    /**
     * Input pergerakan barang baru (masuk / keluar non-penjualan)
     */
    public function storeMovement(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:in,out',
            'reason' => 'required|string',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'notes' => 'nullable|string|max:255',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        DB::transaction(function () use ($validated, $item, $request) {
            $refNumber = 'MOV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            StockMovement::create([
                'reference_number' => $refNumber,
                'item_id' => $item->id,
                'type' => $validated['type'],
                'reason' => $validated['reason'],
                'weight' => $item->net_weight,
                'warehouse_id' => $validated['warehouse_id'] ?? $item->warehouse_id,
                'user_id' => $request->user()?->id,
                'notes' => $validated['notes'],
            ]);

            // Update status item sesuai kondisi
            if ($validated['type'] === 'out') {
                if ($validated['reason'] === 'melted') {
                    $item->update(['status' => 'melted', 'tray_id' => null]);
                } elseif ($validated['reason'] === 'repair_out') {
                    $item->update(['status' => 'repair']);
                }
            } elseif ($validated['type'] === 'in') {
                if ($validated['reason'] === 'repair_in') {
                    $item->update(['status' => 'available']);
                }
            }

            if (!empty($validated['warehouse_id'])) {
                $item->update(['warehouse_id' => $validated['warehouse_id']]);
            }
        });

        return back()->with('success', 'Pergerakan keluar/masuk barang berhasil dicatat!');
    }
}
