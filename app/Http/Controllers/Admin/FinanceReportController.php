<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buyback;
use App\Models\Expense;
use App\Models\JewelryService;
use App\Models\Payroll;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FinanceReportController extends Controller
{
    /**
     * Laporan Rekapitulasi Laba Rugi Komprehensif Toko Emas
     */
    public function profitAndLoss(Request $request): Response
    {
        $year = (int) $request->input('year', date('Y'));
        $month = $request->input('month');

        // Query Builder dasar periode
        $applyPeriod = function ($query) use ($year, $month) {
            $query->whereYear('created_at', $year);
            if ($month) {
                $query->whereMonth('created_at', $month);
            }
            return $query;
        };

        // 1. PENDAPATAN (REVENUE)
        $salesQuery = Sale::query();
        $applyPeriod($salesQuery);
        $totalSales = (float) $salesQuery->sum('grand_total');
        $totalCraftingFee = (float) $salesQuery->sum('total_crafting_fee');

        $servicesQuery = JewelryService::where('status', 'selesai');
        $applyPeriod($servicesQuery);
        $totalServices = (float) $servicesQuery->sum('service_fee');

        // Total Omset / Pendapatan Kotor
        $totalRevenue = $totalSales + $totalServices;

        // 2. BEBAN POKOK PENJUALAN (HPP & BUYBACK)
        $purchasesQuery = Purchase::query();
        $applyPeriod($purchasesQuery);
        $totalSupplierPurchases = (float) $purchasesQuery->sum('total_amount');

        $buybacksQuery = Buyback::query();
        $applyPeriod($buybacksQuery);
        $totalBuybackCost = (float) $buybacksQuery->sum('total_payout');

        $totalCogs = $totalSupplierPurchases + $totalBuybackCost;
        $grossProfit = $totalRevenue - $totalCogs;

        // 3. BEBAN OPERASIONAL (EXPENSES & PAYROLL)
        $expenseQuery = Expense::query();
        $expenseQuery->whereYear('expense_date', $year);
        if ($month) {
            $expenseQuery->whereMonth('expense_date', $month);
        }
        $totalOperationalExpenses = (float) $expenseQuery->sum('amount');

        $payrollQuery = Payroll::where('status', 'paid');
        $payrollQuery->where('period_year', $year);
        if ($month) {
            $payrollQuery->where('period_month', $month);
        }
        $totalPayrollExpenses = (float) $payrollQuery->sum('net_salary');

        $totalExpenses = $totalOperationalExpenses + $totalPayrollExpenses;

        // 4. LABA BERSIH (NET PROFIT)
        $netProfit = $grossProfit - $totalExpenses;

        return Inertia::render('Admin/Finance/ProfitLoss', [
            'year' => $year,
            'month' => $month ? (int) $month : null,
            'data' => [
                'total_sales' => $totalSales,
                'total_crafting_fee' => $totalCraftingFee,
                'total_services' => $totalServices,
                'total_revenue' => $totalRevenue,
                'total_supplier_purchases' => $totalSupplierPurchases,
                'total_buyback_cost' => $totalBuybackCost,
                'total_cogs' => $totalCogs,
                'gross_profit' => $grossProfit,
                'total_operational_expenses' => $totalOperationalExpenses,
                'total_payroll_expenses' => $totalPayrollExpenses,
                'total_expenses' => $totalExpenses,
                'net_profit' => $netProfit,
            ],
        ]);
    }

    /**
     * Rekapitulasi Pajak Emas Toko (PPN / PPh 22 Penjualan Emas Perhiasan)
     */
    public function taxReport(Request $request): Response
    {
        $year = (int) $request->input('year', date('Y'));
        $month = $request->input('month', date('n'));

        $sales = Sale::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with('customer')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalOmset = (float) Sale::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('grand_total');

        // Simulasi PMK 48/PMK.03/2023: PPN Emas Perhiasan 1.1% jika ada faktur / NPWP, 1.65% non-NPWP
        $estimatedVat = round($totalOmset * 0.011, 2);
        $estimatedIncomeTax = round($totalOmset * 0.0025, 2); // PPh 22 0.25%

        return Inertia::render('Admin/Finance/TaxReport', [
            'sales' => $sales,
            'year' => $year,
            'month' => (int) $month,
            'stats' => [
                'total_omset' => $totalOmset,
                'estimated_vat' => $estimatedVat,
                'estimated_income_tax' => $estimatedIncomeTax,
                'total_tax' => $estimatedVat + $estimatedIncomeTax,
            ],
        ]);
    }

    /**
     * Rekapitulasi Pembagian Komisi / Fee Marketer Toko Emas
     */
    public function marketerFees(Request $request): Response
    {
        $customers = \App\Models\Customer::orderByDesc('total_purchased_amount')->get();

        $marketers = $customers->map(function ($c) {
            $totalSales = (float) $c->total_purchased_amount;
            $rate = 1.5; // Komisi 1.5%
            $fee = $totalSales * ($rate / 100);
            return [
                'id' => $c->id,
                'name' => $c->name,
                'code' => $c->code,
                'phone' => $c->phone,
                'total_purchased_amount' => $totalSales,
                'commission_rate' => $rate,
                'commission_amount' => $fee,
                'points' => $c->points,
            ];
        });

        $totalFee = $marketers->sum('commission_amount');
        $totalOmset = $marketers->sum('total_purchased_amount');

        return Inertia::render('Admin/Finance/MarketerFees', [
            'marketers' => $marketers,
            'stats' => [
                'total_marketers' => $marketers->count(),
                'total_omset_generated' => $totalOmset,
                'total_commission_fee' => $totalFee,
            ],
        ]);
    }
}
