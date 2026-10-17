<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\JewelryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JewelryServiceController extends Controller
{
    /**
     * Daftar reparasi dan servis perhiasan emas
     */
    public function index(Request $request): Response
    {
        $query = JewelryService::with(['customer', 'cashier']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('service_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%")
                  ->orWhere('service_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $services = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Services/Index', [
            'services' => $services,
            'filters' => $request->only(['search', 'status']),
            'stats' => [
                'total_services' => JewelryService::count(),
                'in_progress_count' => JewelryService::whereIn('status', ['received', 'in_progress'])->count(),
                'ready_count' => JewelryService::where('status', 'ready')->count(),
                'picked_up_count' => JewelryService::where('status', 'picked_up')->count(),
            ],
        ]);
    }

    /**
     * Form order servis baru
     */
    public function create(): Response
    {
        $customers = Customer::orderBy('name')->get();
        $serviceNumber = 'SRV-' . date('Ym') . '-' . str_pad((string) (JewelryService::count() + 1), 4, '0', STR_PAD_LEFT);

        return Inertia::render('Admin/Services/Create', [
            'customers' => $customers,
            'suggestedNumber' => $serviceNumber,
        ]);
    }

    /**
     * Simpan order servis baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_number' => 'required|string|max:50|unique:jewelry_services,service_number',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'nullable|string|max:50',
            'item_name' => 'required|string|max:150',
            'weight_before' => 'required|numeric|min:0.001',
            'service_type' => 'required|string|max:100',
            'damage_description' => 'nullable|string',
            'estimated_cost' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0',
            'received_date' => 'required|date',
            'completion_target_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $cost = (float) $validated['estimated_cost'];
        $dp = (float) $validated['down_payment'];
        $validated['remaining_cost'] = max(0, $cost - $dp);
        $validated['cashier_id'] = $request->user()?->id;
        $validated['status'] = 'received';

        JewelryService::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', "Order servis {$validated['service_number']} berhasil didaftarkan!");
    }

    /**
     * Update status pengerjaan atau pengambilan servis
     */
    public function updateStatus(Request $request, JewelryService $service): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:received,in_progress,ready,picked_up,cancelled',
            'weight_after' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'picked_up') {
            $service->remaining_cost = 0; // Pelunasan sisa saat diambil
        }

        $service->status = $validated['status'];
        if (isset($validated['weight_after'])) {
            $service->weight_after = $validated['weight_after'];
        }
        if (!empty($validated['notes'])) {
            $service->notes = $service->notes . "\n[Update " . now()->format('d/m/Y') . ': ' . $validated['notes'] . ']';
        }
        $service->save();

        return back()->with('success', "Status servis {$service->service_number} diperbarui menjadi {$service->status}!");
    }

    /**
     * Hapus order servis (Soft Delete)
     */
    public function destroy(JewelryService $service): RedirectResponse
    {
        $service->delete();
        return back()->with('success', "Data servis {$service->service_number} berhasil diarsipkan (Soft Delete)!");
    }

    /**
     * Laporan Servis Perhiasan Emas
     */
    public function reports(Request $request): Response
    {
        $services = JewelryService::with(['customer', 'cashier'])->latest()->get();

        $stats = [
            'total_services' => $services->count(),
            'total_fee' => (float) $services->sum('cost'),
            'completed_count' => $services->where('status', 'picked_up')->count(),
            'in_progress_count' => $services->whereIn('status', ['received', 'in_progress', 'ready'])->count(),
        ];

        return Inertia::render('Admin/Services/Reports', [
            'services' => $services,
            'stats' => $stats,
        ]);
    }
}
