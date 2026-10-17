<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GoldRate;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseController extends Controller
{
    /**
     * Menampilkan daftar faktur pembelian dari supplier
     */
    public function index(Request $request): Response
    {
        $query = Purchase::with(['supplier', 'warehouse', 'user', 'items']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('supplier_invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $purchases = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Purchases/Index', [
            'purchases' => $purchases,
            'filters' => $request->only(['search', 'payment_status']),
            'stats' => [
                'total_purchases' => Purchase::count(),
                'total_expenditure' => (float) Purchase::sum('grand_total'),
                'total_weight_purchased' => (float) Purchase::sum('total_weight'),
            ],
        ]);
    }

    /**
     * Form pembuatan faktur pembelian supplier baru
     */
    public function create(): Response
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $goldRates = GoldRate::where('is_active', true)->orderByDesc('percentage')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $invoiceNumber = 'PB-' . date('Ym') . '-' . str_pad((string) (Purchase::count() + 1), 4, '0', STR_PAD_LEFT);

        return Inertia::render('Admin/Purchases/Create', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'goldRates' => $goldRates,
            'categories' => $categories,
            'suggestedInvoice' => $invoiceNumber,
        ]);
    }

    /**
     * Simpan faktur pembelian dan update stok
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:50|unique:purchases,invoice_number',
            'supplier_invoice_number' => 'nullable|string|max:50',
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'payment_method' => 'required|in:cash,transfer,tempo',
            'payment_status' => 'required|in:paid,partial,unpaid',
            'amount_paid' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:150',
            'items.*.gold_rate_id' => 'required|exists:gold_rates,id',
            'items.*.category_id' => 'required|exists:categories,id',
            'items.*.weight' => 'required|numeric|min:0.001',
            'items.*.price_per_gram' => 'required|numeric|min:0',
            'items.*.crafting_fee' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $totalWeight = 0;
            $subtotal = 0;

            foreach ($validated['items'] as $it) {
                $w = (float) $it['weight'];
                $p = (float) $it['price_per_gram'];
                $f = (float) ($it['crafting_fee'] ?? 0);
                $totalWeight += $w;
                $subtotal += ($w * $p) + $f;
            }

            $discount = (float) ($validated['discount'] ?? 0);
            $tax = (float) ($validated['tax'] ?? 0);
            $grandTotal = max(0, $subtotal - $discount + $tax);
            $amountPaid = (float) $validated['amount_paid'];
            $remaining = max(0, $grandTotal - $amountPaid);

            $purchase = Purchase::create([
                'invoice_number' => $validated['invoice_number'],
                'supplier_invoice_number' => $validated['supplier_invoice_number'],
                'supplier_id' => $validated['supplier_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'total_items' => count($validated['items']),
                'total_weight' => $totalWeight,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'grand_total' => $grandTotal,
                'payment_status' => $validated['payment_status'],
                'amount_paid' => $amountPaid,
                'remaining_balance' => $remaining,
                'payment_method' => $validated['payment_method'],
                'warehouse_id' => $validated['warehouse_id'],
                'user_id' => $request->user()?->id,
                'notes' => $validated['notes'],
            ]);

            foreach ($validated['items'] as $itemData) {
                $w = (float) $itemData['weight'];
                $p = (float) $itemData['price_per_gram'];
                $f = (float) ($itemData['crafting_fee'] ?? 0);
                $lineSubtotal = ($w * $p) + $f;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'gold_rate_id' => $itemData['gold_rate_id'],
                    'category_id' => $itemData['category_id'],
                    'item_name' => $itemData['item_name'],
                    'weight' => $w,
                    'price_per_gram' => $p,
                    'crafting_fee' => $f,
                    'subtotal' => $lineSubtotal,
                ]);
            }
        });

        return redirect()->route('admin.purchases.index')->with('success', "Faktur pembelian {$validated['invoice_number']} berhasil disimpan!");
    }

    /**
     * Soft delete faktur pembelian
     */
    public function destroy(Purchase $purchase): RedirectResponse
    {
        $purchase->delete(); // SoftDeletes

        return back()->with('success', "Faktur pembelian {$purchase->invoice_number} berhasil dihapus (soft delete)!");
    }
}
