<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Buyback;
use App\Models\BuybackItem;
use App\Models\GoldRate;
use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BuybackController extends Controller
{
    /**
     * Display a listing of buyback transactions.
     */
    public function index(Request $request)
    {
        $query = Buyback::with(['cashier', 'items.goldRate', 'originalSale'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('buyback_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('original_invoice_number', 'like', "%{$search}%");
            });
        }

        $buybacks = $query->paginate(15)->withQueryString();

        // Aggregated Stats
        $stats = [
            'total_transactions' => Buyback::count(),
            'total_weight_bought' => (float) Buyback::sum('total_weight_actual'),
            'total_cash_out' => (float) Buyback::sum('grand_total'),
            'today_transactions' => Buyback::whereDate('created_at', today())->count(),
            'today_cash_out' => (float) Buyback::whereDate('created_at', today())->sum('grand_total'),
            'today_weight' => (float) Buyback::whereDate('created_at', today())->sum('total_weight_actual'),
        ];

        return Inertia::render('Admin/Buybacks/Index', [
            'buybacks' => $buybacks,
            'stats' => $stats,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show form for creating a buyback transaction.
     */
    public function create(Request $request)
    {
        $goldRates = GoldRate::orderBy('carat', 'desc')->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $storeSettings = Setting::getAll();

        $selectedSale = null;
        if ($request->filled('sale_id')) {
            $selectedSale = Sale::with(['items', 'cashier'])->find($request->sale_id);
        }

        return Inertia::render('Admin/Buybacks/Create', [
            'goldRates' => $goldRates,
            'bankAccounts' => $bankAccounts,
            'storeSettings' => $storeSettings,
            'selectedSale' => $selectedSale,
        ]);
    }

    /**
     * Search old sales by invoice number or customer name/phone.
     */
    public function searchSale(Request $request)
    {
        $search = $request->query('query');
        if (!$search || strlen($search) < 2) {
            return response()->json([]);
        }

        $sales = Sale::with(['items', 'cashier'])
            ->where('invoice_number', 'like', "%{$search}%")
            ->orWhere('customer_name', 'like', "%{$search}%")
            ->orWhere('customer_phone', 'like', "%{$search}%")
            ->latest()
            ->take(8)
            ->get();

        return response()->json($sales);
    }

    /**
     * Store a new buyback transaction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_nik' => 'nullable|string|max:30',
            'original_sale_id' => 'nullable|exists:sales,id',
            'original_invoice_number' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.gold_rate_id' => 'required|exists:gold_rates,id',
            'items.*.weight_actual' => 'required|numeric|min:0.001',
            'items.*.buy_price_per_gram' => 'required|numeric|min:0',
            'items.*.deduction_amount' => 'nullable|numeric|min:0',
            'items.*.condition_notes' => 'nullable|string|max:255',
            'items.*.destination' => 'required|in:stock_secondary,melt,repair',
            'payment_method' => 'required|in:cash,transfer',
            'notes' => 'nullable|string',
        ]);

        $buyback = DB::transaction(function () use ($validated, $request) {
            // Generate BBK-YYYYMMDD-XXXX
            $today = now()->format('Ymd');
            $countToday = Buyback::whereDate('created_at', today())->count() + 1;
            $bbkNumber = 'BBK-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $totalWeightActual = 0;
            $totalDeduction = 0;
            $grandTotal = 0;

            // 1. Create Buyback Header
            $buyback = Buyback::create([
                'buyback_number' => $bbkNumber,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_nik' => $validated['customer_nik'] ?? null,
                'original_sale_id' => $validated['original_sale_id'] ?? null,
                'original_invoice_number' => $validated['original_invoice_number'] ?? null,
                'total_weight_original' => null,
                'total_weight_actual' => 0,
                'total_deduction' => 0,
                'grand_total' => 0,
                'payment_method' => $validated['payment_method'],
                'cashier_id' => $request->user()->id,
                'notes' => $validated['notes'] ?? null,
            ]);

            // If linked to original sale, store original weight
            if (!empty($validated['original_sale_id'])) {
                $origSale = Sale::find($validated['original_sale_id']);
                if ($origSale) {
                    $buyback->update(['total_weight_original' => $origSale->total_weight]);
                }
            }

            // 2. Process Items
            foreach ($validated['items'] as $itemData) {
                $weight = (float) $itemData['weight_actual'];
                $buyPrice = (float) $itemData['buy_price_per_gram'];
                $deduction = (float) ($itemData['deduction_amount'] ?? 0);
                $itemTotal = max(0, ($weight * $buyPrice) - $deduction);

                BuybackItem::create([
                    'buyback_id' => $buyback->id,
                    'item_name' => $itemData['item_name'],
                    'gold_rate_id' => $itemData['gold_rate_id'],
                    'weight_actual' => $weight,
                    'buy_price_per_gram' => $buyPrice,
                    'deduction_amount' => $deduction,
                    'total_amount' => $itemTotal,
                    'condition_notes' => $itemData['condition_notes'] ?? null,
                    'destination' => $itemData['destination'] ?? 'melt',
                ]);

                $totalWeightActual += $weight;
                $totalDeduction += $deduction;
                $grandTotal += $itemTotal;
            }

            // 3. Finalize totals
            $buyback->update([
                'total_weight_actual' => $totalWeightActual,
                'total_deduction' => $totalDeduction,
                'grand_total' => $grandTotal,
            ]);

            return $buyback;
        });

        return redirect()->route('admin.buybacks.receipt', $buyback->id)
            ->with('success', "Transaksi Beli Kembali {$buyback->buyback_number} berhasil diproses!");
    }

    /**
     * Show official buyback receipt / kuitansi pembelian kembali emas.
     */
    public function receipt(Buyback $buyback)
    {
        $buyback->load(['cashier', 'items.goldRate', 'originalSale']);
        $storeSettings = Setting::getAll();

        return Inertia::render('Admin/Buybacks/Receipt', [
            'buyback' => $buyback,
            'storeSettings' => $storeSettings,
        ]);
    }
}
