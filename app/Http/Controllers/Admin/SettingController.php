<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    /**
     * Menampilkan form pengaturan identitas toko emas dan kebijakan buyback
     */
    public function index(): Response
    {
        $settings = [
            'store_name' => Setting::get('store_name', 'Toko Mas Permata Abadi'),
            'store_legal_name' => Setting::get('store_legal_name', 'UD. Permata Abadi Gold & Jewelry'),
            'store_phone' => Setting::get('store_phone', '0812-3456-7890'),
            'store_address' => Setting::get('store_address', 'Jl. Malioboro No. 88, Danurejan, Yogyakarta 55213'),
            'store_permit_number' => Setting::get('store_permit_number', 'SIUP: 510/088/EMAS/DIY/2026'),
            'primary_color' => Setting::get('primary_color', '#D4AF37'),
            'gold_buyback_policy' => Setting::get('gold_buyback_policy', "Barang dapat dijual kembali (buyback) dengan menunjukkan surat emas resmi ini.\nPotongan harga mengikuti harga pasaran emas hari berjalan dan kondisi fisik barang.\nOngkos bikin tidak dapat diuangkan kembali saat transaksi buyback."),
            'receipt_footer' => Setting::get('receipt_footer', 'Terima kasih atas kunjungan dan kepercayaan Anda. Investasi cerdas perhiasan berkualitas bersama Toko Mas Permata Abadi.'),
        ];

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Simpan pembaruan pengaturan toko
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:150',
            'store_legal_name' => 'required|string|max:150',
            'store_phone' => 'required|string|max:50',
            'store_address' => 'required|string|max:255',
            'store_permit_number' => 'nullable|string|max:100',
            'primary_color' => 'nullable|string|max:20',
            'gold_buyback_policy' => 'required|string',
            'receipt_footer' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'general');
        }

        return redirect()->back()->with('success', 'Pengaturan identitas toko emas dan surat jaminan berhasil disimpan.');
    }
}
