<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Setting;
use App\Models\StockAudit;
use App\Models\StockAuditItem;
use App\Models\Tray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StockAuditController extends Controller
{
    /**
     * Display a listing of stock audits.
     */
    public function index(Request $request)
    {
        $audits = StockAudit::with(['tray', 'auditor'])
            ->latest()
            ->paginate(15);

        $trays = Tray::where('status', 'active')->orderBy('code')->get();

        return Inertia::render('Admin/StockAudits/Index', [
            'audits' => $audits,
            'trays' => $trays,
        ]);
    }

    /**
     * Start a new audit session for a tray.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tray_id' => 'required|exists:trays,id',
            'notes' => 'nullable|string',
        ]);

        $tray = Tray::with('items')->findOrFail($validated['tray_id']);

        $audit = DB::transaction(function () use ($tray, $validated, $request) {
            $today = now()->format('Ymd');
            $countToday = StockAudit::whereDate('created_at', today())->count() + 1;
            $auditCode = 'AUD-' . $today . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

            $availableItems = $tray->items()->where('status', 'available')->get();

            $audit = StockAudit::create([
                'audit_code' => $auditCode,
                'tray_id' => $tray->id,
                'auditor_id' => $request->user()->id,
                'status' => 'draft',
                'total_expected_items' => $availableItems->count(),
                'total_scanned_items' => 0,
                'total_missing_items' => $availableItems->count(),
                'total_mismatched_items' => 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($availableItems as $item) {
                StockAuditItem::create([
                    'stock_audit_id' => $audit->id,
                    'item_id' => $item->id,
                    'sku' => $item->sku,
                    'scan_status' => 'missing',
                ]);
            }

            return $audit;
        });

        return redirect()->route('admin.stock-audits.show', $audit->id)
            ->with('success', "Sesi Stock Opname {$audit->audit_code} untuk Baki {$tray->code} berhasil dimulai!");
    }

    /**
     * Show interactive audit scanning session and reconciliation report.
     */
    public function show(StockAudit $stockAudit)
    {
        $stockAudit->load(['tray.category', 'tray.goldRate', 'auditor', 'items.item.goldRate']);
        $storeSettings = Setting::getAll();

        return Inertia::render('Admin/StockAudits/Show', [
            'audit' => $stockAudit,
            'storeSettings' => $storeSettings,
        ]);
    }

    /**
     * Handle live barcode scan in audit session.
     */
    public function scan(Request $request, StockAudit $stockAudit)
    {
        if ($stockAudit->status === 'completed') {
            return back()->with('error', 'Sesi stock opname ini sudah selesai.');
        }

        $validated = $request->validate([
            'barcode' => 'required|string',
        ]);

        $barcode = trim($validated['barcode']);
        $item = Item::where('sku', $barcode)
            ->orWhere('id', $barcode)
            ->first();

        if (!$item) {
            return back()->with('error', "Barcode/SKU '{$barcode}' tidak ditemukan dalam database.");
        }

        // Check if item belongs to this tray
        $auditItem = StockAuditItem::where('stock_audit_id', $stockAudit->id)
            ->where('item_id', $item->id)
            ->first();

        if ($auditItem) {
            if ($auditItem->scan_status === 'matched') {
                return back()->with('info', "Perhiasan '{$item->name}' ({$item->sku}) sudah pernah di-scan.");
            }

            $auditItem->update([
                'scan_status' => 'matched',
                'scanned_at' => now(),
            ]);
        } else {
            // Item belongs to another tray or unexpected!
            StockAuditItem::create([
                'stock_audit_id' => $stockAudit->id,
                'item_id' => $item->id,
                'sku' => $item->sku,
                'scan_status' => 'unexpected',
                'scanned_at' => now(),
            ]);
        }

        // Recalculate audit statistics
        $matchedCount = StockAuditItem::where('stock_audit_id', $stockAudit->id)->where('scan_status', 'matched')->count();
        $missingCount = StockAuditItem::where('stock_audit_id', $stockAudit->id)->where('scan_status', 'missing')->count();
        $unexpectedCount = StockAuditItem::where('stock_audit_id', $stockAudit->id)->where('scan_status', 'unexpected')->count();

        $stockAudit->update([
            'total_scanned_items' => $matchedCount + $unexpectedCount,
            'total_missing_items' => $missingCount,
            'total_mismatched_items' => $unexpectedCount,
        ]);

        return back()->with('success', "Item '{$item->name}' berhasil tercatat!");
    }

    /**
     * Finalize and complete the audit session.
     */
    public function complete(Request $request, StockAudit $stockAudit)
    {
        $stockAudit->update([
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $request->input('notes', $stockAudit->notes),
        ]);

        return back()->with('success', "Stock Opname {$stockAudit->audit_code} berhasil diselesaikan dan diarsipkan!");
    }
}
