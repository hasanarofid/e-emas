<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PayrollController extends Controller
{
    /**
     * Menampilkan daftar data riwayat penggajian pegawai toko emas
     */
    public function index(Request $request): Response
    {
        $query = Payroll::with(['employee', 'bankAccount', 'creator']);

        if ($request->filled('month')) {
            $query->where('period_month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('period_year', $request->year);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->latest('payment_date')->paginate(10)->withQueryString();
        $employees = Employee::orderBy('name')->select('id', 'name', 'position', 'employee_code')->get();

        return Inertia::render('Admin/Payrolls/Index', [
            'payrolls' => $payrolls,
            'employees' => $employees,
            'filters' => $request->only(['month', 'year', 'employee_id', 'status']),
            'stats' => [
                'total_paid' => (float) Payroll::where('status', 'paid')->sum('net_salary'),
                'total_slips' => Payroll::count(),
                'this_month_total' => (float) Payroll::where('period_month', date('n'))
                    ->where('period_year', date('Y'))
                    ->where('status', 'paid')
                    ->sum('net_salary'),
            ],
        ]);
    }

    /**
     * Form proses hitung & bayar gaji pegawai
     */
    public function create(Request $request): Response
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $payrollCode = 'PAY-' . date('Ym') . '-' . str_pad((string) (Payroll::count() + 1), 4, '0', STR_PAD_LEFT);

        $selectedEmployeeId = $request->query('employee_id');
        $selectedEmployee = null;
        $calculatedCommission = 0;

        if ($selectedEmployeeId) {
            $selectedEmployee = Employee::find($selectedEmployeeId);
            if ($selectedEmployee && $selectedEmployee->commission_rate > 0 && $selectedEmployee->user_id) {
                // Hitung total omset penjualan kasir pada bulan berjalan
                $month = $request->query('month', date('n'));
                $year = $request->query('year', date('Y'));
                $salesTotal = Sale::where('cashier_id', $selectedEmployee->user_id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('grand_total');

                $calculatedCommission = round(($salesTotal * $selectedEmployee->commission_rate) / 100, 2);
            }
        }

        return Inertia::render('Admin/Payrolls/Create', [
            'employees' => $employees,
            'bankAccounts' => $bankAccounts,
            'payrollCode' => $payrollCode,
            'selectedEmployee' => $selectedEmployee,
            'calculatedCommission' => $calculatedCommission,
            'currentMonth' => (int) date('n'),
            'currentYear' => (int) date('Y'),
        ]);
    }

    /**
     * Simpan transaksi slip gaji
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payroll_code' => 'required|string|max:50|unique:payrolls,payroll_code',
            'employee_id' => 'required|exists:employees,id',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020|max:2035',
            'payment_date' => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'commission_amount' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'net_salary' => 'required|numeric|min:0',
            'payment_method' => 'required|in:tunai,transfer',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'status' => 'required|in:draft,paid,cancelled',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        Payroll::create($validated);

        return redirect()->route('admin.payrolls.index')->with('success', 'Slip gaji berhasil diproses dan disimpan.');
    }

    /**
     * Cetak Slip Gaji Pegawai Toko Emas
     */
    public function show(Payroll $payroll): Response
    {
        $payroll->load(['employee', 'bankAccount', 'creator']);

        return Inertia::render('Admin/Payrolls/Show', [
            'payroll' => $payroll,
        ]);
    }

    /**
     * Soft delete slip gaji
     */
    public function destroy(Payroll $payroll): RedirectResponse
    {
        $payroll->delete();

        return redirect()->route('admin.payrolls.index')->with('success', 'Slip gaji berhasil dihapus (soft delete).');
    }
}
