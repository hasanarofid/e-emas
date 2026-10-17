<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerPointLog;
use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar data pelanggan
     */
    public function index(Request $request): Response
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $customers = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'level']),
            'stats' => [
                'total_customers' => Customer::count(),
                'total_points' => (int) Customer::sum('points'),
                'total_purchases' => (float) Customer::sum('total_purchased_amount'),
            ],
        ]);
    }

    /**
     * Form tambah pelanggan baru
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Customers/Create', [
            'suggestedCode' => Customer::generateCode(),
        ]);
    }

    /**
     * Simpan pelanggan baru ke database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50|unique:customers,code',
            'name' => 'required|string|max:150',
            'birth_date' => 'nullable|date',
            'identity_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'level' => 'required|in:pelanggan,agen,vip,marketer',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = Customer::generateCode();
        }

        Customer::create($validated);

        return redirect()->route('admin.customers.index')->with('success', "Pelanggan {$validated['name']} berhasil didaftarkan!");
    }

    /**
     * Update data pelanggan
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'birth_date' => 'nullable|date',
            'identity_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string',
            'level' => 'required|in:pelanggan,agen,vip,marketer',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return back()->with('success', "Data pelanggan {$customer->name} berhasil diperbarui!");
    }

    /**
     * Soft delete data pelanggan
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete(); // Menggunakan SoftDeletes

        return back()->with('success', "Pelanggan {$customer->name} berhasil dihapus (soft delete)!");
    }

    /**
     * Ambil data transaksi pelanggan untuk modal Detail Transaksi
     */
    public function transactions(Customer $customer): \Illuminate\Http\JsonResponse
    {
        $sales = Sale::where('customer_id', $customer->id)
            ->orWhere('customer_phone', $customer->phone)
            ->with('items')
            ->latest()
            ->get();

        $pointLogs = $customer->pointLogs()
            ->with('user')
            ->latest()
            ->get();

        return response()->json([
            'customer' => $customer,
            'sales' => $sales,
            'point_logs' => $pointLogs,
        ]);
    }

    /**
     * Ambil hadiah / penukaran poin manual
     */
    public function redeemReward(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1|max:' . $customer->points,
            'reward_name' => 'nullable|string|max:150',
            'notes' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $customer, $request) {
            $pointsToDeduct = (int) $validated['points'];

            // Kurangi saldo poin pelanggan
            $customer->decrement('points', $pointsToDeduct);

            // Catat log mutasi poin
            CustomerPointLog::create([
                'customer_id' => $customer->id,
                'type' => 'redeem',
                'points' => -$pointsToDeduct,
                'reward_name' => $validated['reward_name'] ?? 'Hadiah Fisik',
                'notes' => $validated['notes'],
                'user_id' => $request->user()?->id,
            ]);
        });

        return back()->with('success', "Penukaran hadiah untuk {$customer->name} berhasil diproses!");
    }

    /**
     * Halaman cetak kartu member pelanggan (Member Card Print)
     */
    public function printCard(Customer $customer): Response
    {
        $storeName = Setting::get('store_name', 'Toko Mas Permata Abadi');
        $storePhone = Setting::get('store_phone', '0812-3456-7890');
        $storeAddress = Setting::get('store_address', 'Yogyakarta');

        return Inertia::render('Admin/Customers/PrintCard', [
            'customer' => $customer,
            'store' => [
                'name' => $storeName,
                'phone' => $storePhone,
                'address' => $storeAddress,
            ],
        ]);
    }

    /**
     * Export data pelanggan ke format CSV
     */
    public function export(): HttpResponse
    {
        $customers = Customer::orderBy('name')->get();

        $csvHeader = ['Kode Pelanggan', 'Nama Lengkap', 'No. HP', 'No. Telepon', 'Email', 'Kota', 'Alamat', 'Level', 'Poin', 'Total Item', 'Total Pembelian'];
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $csvHeader);

        foreach ($customers as $c) {
            fputcsv($handle, [
                $c->code,
                $c->name,
                $c->phone,
                $c->telephone,
                $c->email,
                $c->city,
                $c->address,
                strtoupper($c->level),
                $c->points,
                $c->total_items_count,
                $c->total_purchased_amount,
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data-pelanggan-' . date('Ymd') . '.csv"',
        ]);
    }
}
