<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WarehouseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Warehouse::withCount('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $warehouses = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Warehouses/Index', [
            'warehouses' => $warehouses,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:30|unique:warehouses,code',
            'location' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:100',
            'pic_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Warehouse::create($validated);

        return back()->with('success', 'Gudang / lokasi brankas berhasil ditambahkan!');
    }

    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:30|unique:warehouses,code,' . $warehouse->id,
            'location' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:100',
            'pic_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $warehouse->update($validated);

        return back()->with('success', 'Data gudang berhasil diperbarui!');
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        if ($warehouse->items()->exists()) {
            return back()->with('error', 'Gudang tidak dapat dihapus karena masih menampung perhiasan.');
        }

        $warehouse->delete();

        return back()->with('success', 'Gudang berhasil dihapus!');
    }
}
