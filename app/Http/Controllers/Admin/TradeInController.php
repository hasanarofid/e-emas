<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\GoldRate;
use App\Models\Item;
use App\Models\TradeIn;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TradeInController extends Controller
{
    /**
     * 1. Data Tukar Tambah (List Transaksi)
     */
    public function index(Request $request): Response
    {
        $query = TradeIn::with(['customer', 'oldGoldRate', 'cashier', 'bankAccount']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('trade_in_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('old_item_name', 'like', "%{$search}%")
                  ->orWhere('new_item_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $tradeIns = $query->latest('transaction_date')->latest('id')->paginate(10)->withQueryString();

        $stats = [
            'total_transactions' => TradeIn::count(),
            'total_old_weight' => (float) TradeIn::sum('old_weight'),
            'total_new_weight' => (float) TradeIn::sum('new_weight'),
            'total_difference_cash_in' => (float) TradeIn::sum('difference_amount'),
        ];

        return Inertia::render('Admin/TradeIns/Index', [
            'tradeIns' => $tradeIns,
            'filters' => $request->only(['search', 'payment_method']),
            'stats' => $stats,
        ]);
    }

    /**
     * 2. Form Transaksi Tukar Tambah Baru
     */
    public function create(): Response
    {
        $customers = Customer::orderBy('name')->get();
        $goldRates = GoldRate::where('is_active', true)->orderByDesc('percentage')->get();
        $items = Item::with(['category', 'goldRate', 'tray'])
            ->where('status', 'available')
            ->orderBy('name')
            ->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        $suggestedNumber = 'TT-' . date('Ym') . '-' . str_pad((string) (TradeIn::count() + 1), 4, '0', STR_PAD_LEFT);

        return Inertia::render('Admin/TradeIns/Create', [
            'customers' => $customers,
            'goldRates' => $goldRates,
            'items' => $items,
            'bankAccounts' => $bankAccounts,
            'suggestedNumber' => $suggestedNumber,
        ]);
    }

    /**
     * Simpan transaksi tukar tambah baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'trade_in_number' => 'required|string|max:50|unique:trade_ins,trade_in_number',
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'nullable|string|max:50',
            'customer_id' => 'nullable|exists:customers,id',
            'old_item_name' => 'required|string|max:200',
            'old_gold_rate_id' => 'nullable|exists:gold_rates,id',
            'old_weight' => 'required|numeric|min:0.001',
            'old_estimated_price' => 'required|numeric|min:0',
            'new_item_id' => 'nullable|exists:items,id',
            'new_item_name' => 'required|string|max:200',
            'new_weight' => 'required|numeric|min:0.001',
            'new_price' => 'required|numeric|min:0',
            'difference_amount' => 'required|numeric',
            'payment_method' => 'required|in:cash,transfer,qris',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'amount_paid' => 'required|numeric|min:0',
            'change_due' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['transaction_date'] = Carbon::today()->toDateString();
        $validated['cashier_id'] = $request->user()?->id;

        // Jika item baru dipilih dari inventaris, tandai sebagai sold
        if (!empty($validated['new_item_id'])) {
            Item::where('id', $validated['new_item_id'])->update(['status' => 'sold']);
        }

        TradeIn::create($validated);

        return redirect()->route('admin.trade-ins.index')
            ->with('success', "Transaksi tukar tambah {$validated['trade_in_number']} berhasil diproses!");
    }

    /**
     * 3. Tukar Tambah Harian
     */
    public function daily(Request $request): Response
    {
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));

        $tradeInsToday = TradeIn::with(['customer', 'oldGoldRate'])
            ->whereDate('transaction_date', $selectedDate)
            ->get();

        $stats = [
            'count' => $tradeInsToday->count(),
            'old_weight_in' => (float) $tradeInsToday->sum('old_weight'),
            'new_weight_out' => (float) $tradeInsToday->sum('new_weight'),
            'total_difference_paid' => (float) $tradeInsToday->sum('difference_amount'),
        ];

        return Inertia::render('Admin/TradeIns/Daily', [
            'selectedDate' => $selectedDate,
            'tradeIns' => $tradeInsToday,
            'stats' => $stats,
        ]);
    }

    /**
     * 4. Tukar Tambah Bulanan
     */
    public function monthly(Request $request): Response
    {
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $calendarDays = [];

        $totalDifference = 0;
        $totalOldWeight = 0;
        $totalNewWeight = 0;
        $totalCount = 0;

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $dayTradeIns = TradeIn::whereDate('transaction_date', $currentDate)->get();

            $diff = (float) $dayTradeIns->sum('difference_amount');
            $oldW = (float) $dayTradeIns->sum('old_weight');
            $newW = (float) $dayTradeIns->sum('new_weight');
            $cnt = $dayTradeIns->count();

            $calendarDays[] = [
                'date' => $currentDate,
                'day' => $d,
                'count' => $cnt,
                'old_weight' => $oldW,
                'new_weight' => $newW,
                'difference' => $diff,
            ];

            $totalDifference += $diff;
            $totalOldWeight += $oldW;
            $totalNewWeight += $newW;
            $totalCount += $cnt;
        }

        return Inertia::render('Admin/TradeIns/Monthly', [
            'year' => $year,
            'month' => $month,
            'calendarDays' => $calendarDays,
            'stats' => [
                'total_difference' => $totalDifference,
                'total_old_weight' => $totalOldWeight,
                'total_new_weight' => $totalNewWeight,
                'total_count' => $totalCount,
                'gold_surplus_deficit' => $totalNewWeight - $totalOldWeight, // Selisih perputaran gramasi toko
            ],
        ]);
    }

    /**
     * 5. Laporan Tukar Tambah
     */
    public function reports(Request $request): Response
    {
        $totalDiff = (float) TradeIn::sum('difference_amount');
        $totalOld = (float) TradeIn::sum('old_weight');
        $totalNew = (float) TradeIn::sum('new_weight');
        $count = TradeIn::count();

        $byPayment = [
            ['method' => 'Tunai Kasir (Cash)', 'count' => TradeIn::where('payment_method', 'cash')->count(), 'amount' => TradeIn::where('payment_method', 'cash')->sum('difference_amount')],
            ['method' => 'Transfer Bank', 'count' => TradeIn::where('payment_method', 'transfer')->count(), 'amount' => TradeIn::where('payment_method', 'transfer')->sum('difference_amount')],
            ['method' => 'QRIS Toko', 'count' => TradeIn::where('payment_method', 'qris')->count(), 'amount' => TradeIn::where('payment_method', 'qris')->sum('difference_amount')],
        ];

        return Inertia::render('Admin/TradeIns/Reports', [
            'stats' => [
                'total_transactions' => $count,
                'total_difference' => $totalDiff,
                'total_old_weight' => $totalOld,
                'total_new_weight' => $totalNew,
                'gold_net_flow' => $totalNew - $totalOld,
            ],
            'byPayment' => $byPayment,
        ]);
    }
}
