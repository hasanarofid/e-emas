<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Brand::withCount('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $brands = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Brands/Index', [
            'brands' => $brands,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:brands,code',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Brand::create($validated);

        return back()->with('success', 'Brand / Merk perhiasan berhasil ditambahkan!');
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:brands,code,' . $brand->id,
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $brand->update($validated);

        return back()->with('success', 'Brand berhasil diperbarui!');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->items()->exists()) {
            return back()->with('error', 'Brand tidak dapat dihapus karena masih digunakan oleh perhiasan dalam inventaris.');
        }

        $brand->delete();

        return back()->with('success', 'Brand berhasil dihapus!');
    }
}
