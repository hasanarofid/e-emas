<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UnitController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Unit::withCount('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
            });
        }

        $units = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Units/Index', [
            'units' => $units,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:20|unique:units,symbol',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Unit::create($validated);

        return back()->with('success', 'Satuan barang berhasil ditambahkan!');
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:20|unique:units,symbol,' . $unit->id,
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $unit->update($validated);

        return back()->with('success', 'Satuan barang berhasil diperbarui!');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->items()->exists()) {
            return back()->with('error', 'Satuan tidak dapat dihapus karena masih digunakan oleh perhiasan dalam inventaris.');
        }

        $unit->delete();

        return back()->with('success', 'Satuan barang berhasil dihapus!');
    }
}
