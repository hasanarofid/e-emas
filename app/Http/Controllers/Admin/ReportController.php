<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Buyback;
use App\Models\Category;
use App\Models\GoldRate;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Display the analytics and reports dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->input('period', 'this_month'); // today, 7_days, this_month, this_year, custom
        $startDate = null;
        $endDate = Carbon::now()->endOfDay();

        switch ($period) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                break;
            case '7_days':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                break;
            case 'custom':
                $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
                $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
                break;
            case 'this_month':
            default:
                $startDate = Carbon::now()->startOfMonth();
                break;
        }

        // 1. Sales Aggregates
        $salesQuery = Sale::whereBetween('created_at', [$startDate, $endDate]);
        $totalSalesRevenue = (float) (clone $salesQuery)->sum('grand_total');
        $totalSalesWeight = (float) (clone $salesQuery)->sum('total_weight');
        $totalSalesCount = (clone $salesQuery)->count();

        // 2. Buyback Aggregates
        $buybacksQuery = Buyback::whereBetween('created_at', [$startDate, $endDate]);
        $totalBuybackExpense = (float) (clone $buybacksQuery)->sum('grand_total');
        $totalBuybackWeight = (float) (clone $buybacksQuery)->sum('total_weight_actual');
        $totalBuybackCount = (clone $buybacksQuery)->count();

        // 3. Net Margin & Net Gold Flow
        $netCashFlow = $totalSalesRevenue - $totalBuybackExpense;
        $netWeightFlow = $totalSalesWeight - $totalBuybackWeight;

        // 4. Daily Sales & Buyback Trend (for interactive charts)
        $dailyTrends = [];
        $current = (clone $startDate)->startOfDay();
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $daySales = (float) Sale::whereDate('created_at', $dateStr)->sum('grand_total');
            $dayBuybacks = (float) Buyback::whereDate('created_at', $dateStr)->sum('grand_total');
            $daySalesWeight = (float) Sale::whereDate('created_at', $dateStr)->sum('total_weight');

            $dailyTrends[] = [
                'date' => $current->format('d M'),
                'full_date' => $dateStr,
                'sales' => $daySales,
                'buybacks' => $dayBuybacks,
                'weight' => $daySalesWeight,
            ];
            $current->addDay();
        }

        // 5. Payment Methods Breakdown
        $paymentBreakdown = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(grand_total) as total'))
            ->groupBy('payment_method')
            ->get();

        // 6. Category Performance
        $categoryBreakdown = SaleItem::whereBetween('sale_items.created_at', [$startDate, $endDate])
            ->select('category_name', DB::raw('count(*) as count'), DB::raw('sum(subtotal) as total'), DB::raw('sum(net_weight) as total_weight'))
            ->groupBy('category_name')
            ->orderBy('total', 'desc')
            ->get();

        // 7. Gold Rate / Karat Performance
        $karatBreakdown = SaleItem::whereBetween('sale_items.created_at', [$startDate, $endDate])
            ->select('gold_rate_name', DB::raw('count(*) as count'), DB::raw('sum(subtotal) as total'), DB::raw('sum(net_weight) as total_weight'))
            ->groupBy('gold_rate_name')
            ->orderBy('total', 'desc')
            ->get();

        $storeSettings = Setting::getAll();

        return Inertia::render('Admin/Reports/Index', [
            'metrics' => [
                'total_revenue' => $totalSalesRevenue,
                'total_sales_weight' => $totalSalesWeight,
                'total_sales_count' => $totalSalesCount,
                'total_buyback_expense' => $totalBuybackExpense,
                'total_buyback_weight' => $totalBuybackWeight,
                'total_buyback_count' => $totalBuybackCount,
                'net_cash_flow' => $netCashFlow,
                'net_weight_flow' => $netWeightFlow,
            ],
            'dailyTrends' => $dailyTrends,
            'paymentBreakdown' => $paymentBreakdown,
            'categoryBreakdown' => $categoryBreakdown,
            'karatBreakdown' => $karatBreakdown,
            'period' => $period,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'storeSettings' => $storeSettings,
        ]);
    }
}
