<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\GoldRate;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Models\Tray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PosController extends Controller
{
    /**
     * Display the POS cashier terminal.
     */
    public function index(Request $request)
    {
        $items = Item::with(['category', 'goldRate', 'tray'])
            ->where('status', 'available')
            ->orderBy('name')
            ->get();

        $goldRates = GoldRate::orderBy('carat', 'desc')->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $trays = Tray::where('status', 'active')->orderBy('code')->get();
        $storeSettings = Setting::getAll();

        $recentSales = Sale::with(['cashier', 'bankAccount', 'items'])
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Admin/Pos/Index', [
            'items' => $items,
            'goldRates' => $goldRates,
            'bankAccounts' => $bankAccounts,
            'trays' => $trays,
            'storeSettings' => $storeSettings,
            'recentSales' => $recentSales,
        ]);
    }

    /**
     * Store a new sale transaction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.gold_price_per_gram' => 'nullable|numeric|min:0',
            'items.*.crafting_fee' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,qris,debit,split',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $sale = DB::transaction(function () use ($validated, $request) {
            // Generate unique invoice number: INV-EMAS-YYYYMMDD-XXXX
            $today = now()->format('Ymd');
            $countToday = Sale::whereDate('created_at', today())->count() + 1;
            $invoiceNumber = 'INV-EMAS-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $totalWeight = 0;
            $subtotal = 0;
            $affectedTrays = [];

            // 1. Create Sale Header
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'total_weight' => 0,
                'subtotal' => 0,
                'discount' => $validated['discount'] ?? 0,
                'tax' => 0,
                'grand_total' => 0,
                'payment_method' => $validated['payment_method'],
                'bank_account_id' => $validated['bank_account_id'] ?? null,
                'amount_paid' => $validated['amount_paid'],
                'change_due' => 0,
                'cashier_id' => $request->user()->id,
                'notes' => $validated['notes'] ?? null,
            ]);

            // 2. Process Items
            foreach ($validated['items'] as $itemData) {
                $item = Item::with(['category', 'goldRate', 'tray'])
                    ->lockForUpdate()
                    ->findOrFail($itemData['id']);

                if ($item->status !== 'available') {
                    throw new \Exception("Perhiasan '{$item->name}' ({$item->sku}) sudah tidak tersedia di etalase.");
                }

                $goldPrice = isset($itemData['gold_price_per_gram']) && (float) $itemData['gold_price_per_gram'] > 0
                    ? (float) $itemData['gold_price_per_gram']
                    : (float) ($item->goldRate?->sell_price_per_gram ?? 0);

                $craftingFee = isset($itemData['crafting_fee'])
                    ? (float) $itemData['crafting_fee']
                    : (float) $item->crafting_fee;

                $itemSubtotal = round(((float) $item->net_weight * $goldPrice) + $craftingFee, 2);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'sku' => $item->sku,
                    'category_name' => $item->category?->name ?? 'Perhiasan',
                    'gold_rate_name' => $item->goldRate?->code ?? 'Kadar Emas',
                    'gross_weight' => $item->gross_weight,
                    'stone_weight' => $item->stone_weight,
                    'net_weight' => $item->net_weight,
                    'gold_price_per_gram' => $goldPrice,
                    'crafting_fee' => $craftingFee,
                    'subtotal' => $itemSubtotal,
                    'image_snapshot' => $item->image,
                ]);

                // Update item status to sold
                $trayId = $item->tray_id;
                $item->update([
                    'status' => 'sold',
                    'tray_id' => null, // keluar dari baki etalase
                ]);

                if ($trayId) {
                    $affectedTrays[$trayId] = true;
                }

                $totalWeight += (float) $item->net_weight;
                $subtotal += $itemSubtotal;
            }

            // 3. Finalize Totals
            $discount = (float) ($validated['discount'] ?? 0);
            $grandTotal = max(0, $subtotal - $discount);
            $amountPaid = (float) $validated['amount_paid'];
            $changeDue = max(0, $amountPaid - $grandTotal);

            $sale->update([
                'total_weight' => $totalWeight,
                'subtotal' => $subtotal,
                'grand_total' => $grandTotal,
                'change_due' => $changeDue,
            ]);

            // 4. Recalculate affected trays
            foreach (array_keys($affectedTrays) as $tId) {
                $t = Tray::find($tId);
                if ($t) {
                    $t->recalculateTotals();
                }
            }

            return $sale;
        });

        return redirect()->route('admin.pos.invoice', $sale->id)
            ->with('success', "Transaksi {$sale->invoice_number} berhasil diproses!");
    }

    /**
     * Print thermal receipt (58mm/80mm).
     */
    public function invoice(Sale $sale)
    {
        $sale->load(['cashier', 'bankAccount', 'items.item']);
        $storeSettings = Setting::getAll();

        return Inertia::render('Admin/Pos/Invoice', [
            'sale' => $sale,
            'storeSettings' => $storeSettings,
        ]);
    }

    /**
     * Print official Gold Guarantee Certificate / Surat Emas (A5 Landscape / Kuarto).
     */
    public function certificate(Sale $sale)
    {
        $sale->load(['cashier', 'bankAccount', 'items.item']);
        $storeSettings = Setting::getAll();

        return Inertia::render('Admin/Pos/Certificate', [
            'sale' => $sale,
            'storeSettings' => $storeSettings,
        ]);
    }
}
