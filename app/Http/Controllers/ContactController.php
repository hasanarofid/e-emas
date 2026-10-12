<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    /**
     * Menampilkan halaman kontak dan form konsultasi RAB.
     */
    public function index(): Response
    {
        $contactInfo = [
            'brand_name' => 'Cahaya Ornamen',
            'phone' => '+62 813-1605-3870',
            'whatsapp_number' => '6281316053870',
            'whatsapp_url' => 'https://wa.me/6281316053870?text=Halo%20Cahaya%20Ornamen,%20saya%20ingin%20konsultasi%20kebutuhan%20ornamen%20arsitektur',
            'workshop_address' => 'Jl. Kedung Rahmat, Kauman, Sepande, Kec. Candi, Kabupaten Sidoarjo, Jawa Timur 61271',
            'maps_url' => 'https://maps.app.goo.gl/F8mZvh4tVoo7KQYJ8',
            'working_hours' => 'Senin - Sabtu: 08.00 - 17.00 WIB',
        ];

        return Inertia::render('Contact', [
            'contactInfo' => $contactInfo,
        ]);
    }

    /**
     * Menyimpan submission formulir konsultasi / permintaan penawaran.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'project_type' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'estimated_volume' => 'nullable|string|max:100',
            'message' => 'required|string|max:2000',
        ]);

        Inquiry::create($validated);

        return back()->with('success', 'Permintaan konsultasi Anda berhasil dikirim. Tim estimator Cahaya Ornamen akan segera menghubungi Anda.');
    }
}
