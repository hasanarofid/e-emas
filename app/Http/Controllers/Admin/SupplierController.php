<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    /**
     * Menampilkan katalog supplier / pabrik emas
     */
    public function index(Request $request): Response
    {
        $query = Supplier::withCount('purchases');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Simpan supplier baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:suppliers,code',
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_holder' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Supplier::create($validated);

        return back()->with('success', "Supplier {$validated['company_name']} berhasil ditambahkan!");
    }

    /**
     * Update data supplier
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:suppliers,code,' . $supplier->id,
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_holder' => 'nullable|string|max:150',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $supplier->update($validated);

        return back()->with('success', "Data supplier {$supplier->name} berhasil diperbarui!");
    }

    /**
     * Soft delete supplier
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete(); // SoftDeletes

        return back()->with('success', "Supplier {$supplier->name} berhasil dihapus (soft delete)!");
    }
}
