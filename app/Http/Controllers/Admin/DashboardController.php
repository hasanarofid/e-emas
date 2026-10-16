<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoldRate;
use App\Models\Item;
use App\Models\Sale;
use App\Models\Tray;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Manajemen Toko Emas
     */
    public function index(): Response
    {
        $today = now()->startOfDay();

        $stats = [
            'total_gold_weight' => (float) Item::where('status', 'available')->sum('gross_weight'),
            'total_gold_valuation' => (float) Item::where('status', 'available')->sum('total_price'),
            'available_items_count' => Item::where('status', 'available')->count(),
            'active_trays_count' => Tray::where('status', 'active')->count(),
            'today_sales_count' => Sale::where('created_at', '>=', $today)->count(),
            'today_sales_revenue' => (float) Sale::where('created_at', '>=', $today)->sum('grand_total'),
            'users_count' => User::count(),
        ];

        $goldRates = GoldRate::where('is_active', true)
            ->orderByDesc('percentage')
            ->get();

        $trays = Tray::with(['category', 'goldRate'])
            ->where('status', 'active')
            ->take(6)
            ->get();

        $recentSales = Sale::with('cashier')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'goldRates' => $goldRates,
            'trays' => $trays,
            'recentSales' => $recentSales,
        ]);
    }
}
