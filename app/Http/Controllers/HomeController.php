<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GoldRate;
use App\Models\Item;
use App\Models\Setting;
use App\Models\Tray;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Menampilkan Landing Page & Live Ticker Harga Emas Toko
     */
    public function index(Request $request): Response
    {
        $goldRates = GoldRate::where('is_active', true)
            ->orderByDesc('percentage')
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount(['items' => function ($q) {
                $q->where('status', 'available');
            }])
            ->get();

        $featuredItems = Item::with(['category', 'goldRate', 'tray'])
            ->where('status', 'available')
            ->latest()
            ->take(8)
            ->get();

        $trays = Tray::with(['category', 'goldRate'])
            ->where('status', 'active')
            ->get();

        $storeSettings = [
            'store_name' => Setting::get('store_name', 'Toko Mas Permata Abadi'),
            'store_legal_name' => Setting::get('store_legal_name', 'UD. Permata Abadi Gold & Jewelry'),
            'store_phone' => Setting::get('store_phone', '0812-3456-7890'),
            'store_address' => Setting::get('store_address', 'Jl. Malioboro No. 88, Yogyakarta'),
            'store_permit_number' => Setting::get('store_permit_number', 'SIUP: 510/088/EMAS/2026'),
            'primary_color' => Setting::get('primary_color', '#D4AF37'),
        ];

        return Inertia::render('Welcome', [
            'goldRates' => $goldRates,
            'categories' => $categories,
            'featuredItems' => $featuredItems,
            'trays' => $trays,
            'storeSettings' => $storeSettings,
            'auth' => [
                'user' => $request->user(),
            ],
        ]);
    }
}
