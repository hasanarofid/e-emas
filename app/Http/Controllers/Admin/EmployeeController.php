<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    /**
     * Menampilkan daftar data pegawai toko emas
     */
    public function index(Request $request): Response
    {
        $query = Employee::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Employees/Index', [
            'employees' => $employees,
            'filters' => $request->only(['search', 'position', 'status']),
            'stats' => [
                'total_employees' => Employee::count(),
                'active_employees' => Employee::where('is_active', true)->count(),
                'total_base_payroll' => (float) Employee::where('is_active', true)->sum('base_salary'),
            ],
            'positions' => [
                'Kepala Toko',
                'Kasir',
                'Sales & Counter Jewelry',
                'Tukang Servis & Cuci Emas',
                'Admin Gudang',
            ],
        ]);
    }

    /**
     * Form penambahan pegawai baru
     */
    public function create(): Response
    {
        $employeeCount = Employee::withTrashed()->count();
        $code = 'PEG-' . str_pad((string) ($employeeCount + 1), 3, '0', STR_PAD_LEFT);
        $users = User::orderBy('name')->select('id', 'name', 'email')->get();

        return Inertia::render('Admin/Employees/Create', [
            'defaultCode' => $code,
            'users' => $users,
            'positions' => [
                'Kepala Toko',
                'Kasir',
                'Sales & Counter Jewelry',
                'Tukang Servis & Cuci Emas',
                'Admin Gudang',
            ],
        ]);
    }

    /**
     * Simpan data pegawai baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'employee_code' => 'required|string|max:50|unique:employees,employee_code',
            'name' => 'required|string|max:255',
            'id_card_number' => 'nullable|string|max:50',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'position' => 'required|string|max:100',
            'status' => 'required|in:tetap,kontrak,magang',
            'join_date' => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'bank_name' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        Employee::create($validated);

        return redirect()->route('admin.employees.index')->with('success', 'Data pegawai berhasil didaftarkan.');
    }

    /**
     * Form edit pegawai
     */
    public function edit(Employee $employee): Response
    {
        $users = User::orderBy('name')->select('id', 'name', 'email')->get();

        return Inertia::render('Admin/Employees/Edit', [
            'employee' => $employee,
            'users' => $users,
            'positions' => [
                'Kepala Toko',
                'Kasir',
                'Sales & Counter Jewelry',
                'Tukang Servis & Cuci Emas',
                'Admin Gudang',
            ],
        ]);
    }

    /**
     * Update data pegawai
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'employee_code' => 'required|string|max:50|unique:employees,employee_code,' . $employee->id,
            'name' => 'required|string|max:255',
            'id_card_number' => 'nullable|string|max:50',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'position' => 'required|string|max:100',
            'status' => 'required|in:tetap,kontrak,magang',
            'join_date' => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'bank_name' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_holder' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $employee->update($validated);

        return redirect()->route('admin.employees.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    /**
     * Soft delete pegawai
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Data pegawai berhasil dihapus (soft delete).');
    }
}
