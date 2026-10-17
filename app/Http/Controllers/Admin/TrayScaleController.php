<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tray;
use App\Models\TrayScaleLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrayScaleController extends Controller
{
    /**
     * Menampilkan riwayat penimbangan baki harian
     */
    public function index(Request $request): Response
    {
        $query = TrayScaleLog::with(['tray', 'user']);

        if ($request->filled('date')) {
            $query->where('scale_date', $request->date);
        }

        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }

        $logs = $query->latest('scale_date')->latest('id')->paginate(15)->withQueryString();

        return Inertia::render('Admin/TrayScales/Index', [
            'logs' => $logs,
            'filters' => $request->only(['date', 'session']),
            'trays' => Tray::where('status', 'active')->orderBy('code')->get(),
        ]);
    }

    /**
     * Form penimbangan baki baru (Pagi / Sore)
     */
    public function create(): Response
    {
        $trays = Tray::withCount(['items' => function ($q) {
            $q->where('status', 'available');
        }])
        ->withSum(['items' => function ($q) {
            $q->where('status', 'available');
        }], 'gross_weight')
        ->where('status', 'active')
        ->orderBy('code')
        ->get();

        return Inertia::render('Admin/TrayScales/Create', [
            'trays' => $trays,
            'today' => now()->toDateString(),
        ]);
    }

    /**
     * Simpan penimbangan baki
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tray_id' => 'required|exists:trays,id',
            'scale_date' => 'required|date',
            'session' => 'required|in:morning,evening',
            'item_count_real' => 'required|integer|min:0',
            'weight_real' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $tray = Tray::findOrFail($validated['tray_id']);
        $itemCountSystem = $tray->items()->where('status', 'available')->count();
        $weightSystem = (float) $tray->items()->where('status', 'available')->sum('gross_weight');

        $weightReal = (float) $validated['weight_real'];
        $diff = round($weightReal - $weightSystem, 3);
        $status = ($diff == 0.000 && $itemCountSystem == $validated['item_count_real']) ? 'matched' : 'discrepancy';

        TrayScaleLog::create([
            'tray_id' => $tray->id,
            'scale_date' => $validated['scale_date'],
            'session' => $validated['session'],
            'item_count_system' => $itemCountSystem,
            'weight_system' => $weightSystem,
            'item_count_real' => $validated['item_count_real'],
            'weight_real' => $weightReal,
            'difference_weight' => $diff,
            'status' => $status,
            'user_id' => $request->user()?->id,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.tray-scales.index')
            ->with('success', "Timbang baki {$tray->code} berhasil dicatat!");
    }
}
