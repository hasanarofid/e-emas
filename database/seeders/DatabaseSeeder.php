<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\GoldRate;
use App\Models\GoldRateHistory;
use App\Models\Item;
use App\Models\Setting;
use App\Models\Tray;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pengguna Default
        $owner = User::firstOrCreate(
            ['email' => 'admin@emas.com'],
            [
                'name' => 'Owner Toko Emas',
                'phone' => '081234567890',
                'role' => 'owner',
                'is_active' => true,
                'password' => Hash::make('password123'),
            ]
        );

        $cashier = User::firstOrCreate(
            ['email' => 'kasir@emas.com'],
            [
                'name' => 'Kasir Utama',
                'phone' => '081298765432',
                'role' => 'kasir',
                'is_active' => true,
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Pengaturan Toko Emas & Website
        $settings = [
            'site_name' => 'POS Emas',
            'store_name' => 'Toko Mas Permata Abadi',
            'store_legal_name' => 'UD. Permata Abadi Gold & Jewelry',
            'store_phone' => '0812-3456-7890',
            'store_address' => 'Jl. Malioboro No. 88, Danurejan, Yogyakarta 55213',
            'store_permit_number' => 'SIUP: 510/088/EMAS/DIY/2026',
            'primary_color' => '#D4AF37', // Warm Champagne Gold
            'gold_buyback_policy' => "Barang dapat dijual kembali (buyback) dengan menunjukkan surat emas resmi ini.\nPotongan harga mengikuti harga pasaran emas hari berjalan dan kondisi fisik barang.\nOngkos bikin tidak dapat diuangkan kembali saat transaksi buyback.",
            'receipt_footer' => 'Terima kasih atas kunjungan dan kepercayaan Anda. Investasi cerdas perhiasan berkualitas bersama Toko Mas Permata Abadi.',
        ];

        foreach ($settings as $key => $val) {
            Setting::set($key, $val, 'general');
        }

        // 2b. Master Akun Bank Pembayaran (Ref: SITOKMAS)
        \App\Models\BankAccount::firstOrCreate(
            ['bank_name' => 'BCA', 'account_number' => '8921-8899-001'],
            ['account_holder' => 'Toko Mas Permata Abadi', 'is_active' => true, 'notes' => 'Rekening Utama Kasir BCA']
        );
        \App\Models\BankAccount::firstOrCreate(
            ['bank_name' => 'Mandiri', 'account_number' => '137-00-998877-6'],
            ['account_holder' => 'Toko Mas Permata Abadi', 'is_active' => true, 'notes' => 'Rekening EDC Mandiri']
        );
        \App\Models\BankAccount::firstOrCreate(
            ['bank_name' => 'QRIS', 'account_number' => 'NMID: ID1020261998822'],
            ['account_holder' => 'Permata Abadi Gold QRIS', 'is_active' => true, 'notes' => 'QRIS Kasir Standar Nasional']
        );

        // 3. Master Kadar & Harga Emas Harian (Standar Indonesia)
        $goldRatesData = [
            [
                'name' => 'Emas Murni 24K (99.9%)',
                'code' => '24K',
                'carat' => 24.00,
                'percentage' => 99.90,
                'buy_price_per_gram' => 1320000,
                'sell_price_per_gram' => 1450000,
                'description' => 'Emas murni batangan dan perhiasan kadar tertinggi',
            ],
            [
                'name' => 'Emas Tua 22K (91.6%)',
                'code' => '22K',
                'carat' => 22.00,
                'percentage' => 91.60,
                'buy_price_per_gram' => 1180000,
                'sell_price_per_gram' => 1300000,
                'description' => 'Emas tua kadar tinggi khas Arab / Dubai',
            ],
            [
                'name' => 'Emas Tua 18K / 750 (75.0%)',
                'code' => '18K',
                'carat' => 18.00,
                'percentage' => 75.00,
                'buy_price_per_gram' => 960000,
                'sell_price_per_gram' => 1080000,
                'description' => 'Standar internasional perhiasan berlian & rose gold',
            ],
            [
                'name' => 'Emas Tua 17K / 700 (70.0%)',
                'code' => '17K',
                'carat' => 17.00,
                'percentage' => 70.00,
                'buy_price_per_gram' => 890000,
                'sell_price_per_gram' => 1010000,
                'description' => 'Kadar perhiasan emas tua paling populer di Indonesia',
            ],
            [
                'name' => 'Emas Sedang 16K (65.0%)',
                'code' => '16K',
                'carat' => 16.00,
                'percentage' => 65.00,
                'buy_price_per_gram' => 820000,
                'sell_price_per_gram' => 935000,
                'description' => 'Emas sedang tahan banting dan berkilau',
            ],
            [
                'name' => 'Emas Muda 9K / 375 (37.5%)',
                'code' => '9K',
                'carat' => 9.00,
                'percentage' => 37.50,
                'buy_price_per_gram' => 460000,
                'sell_price_per_gram' => 560000,
                'description' => 'Emas muda ekonomis untuk perhiasan harian',
            ],
            [
                'name' => 'Emas Muda 8K / 300 (30.0%)',
                'code' => '8K',
                'carat' => 8.00,
                'percentage' => 30.00,
                'buy_price_per_gram' => 370000,
                'sell_price_per_gram' => 470000,
                'description' => 'Emas muda entry-level harga terjangkau',
            ],
        ];

        $rateModels = [];
        foreach ($goldRatesData as $gr) {
            $model = GoldRate::firstOrCreate(
                ['code' => $gr['code']],
                array_merge($gr, ['updated_by' => $owner->id])
            );
            $rateModels[$gr['code']] = $model;

            GoldRateHistory::create([
                'gold_rate_id' => $model->id,
                'buy_price_per_gram' => $model->buy_price_per_gram,
                'sell_price_per_gram' => $model->sell_price_per_gram,
                'changed_by' => $owner->id,
                'notes' => 'Inisialisasi harga pembukaan sistem',
            ]);
        }

        // 4. Kategori Perhiasan
        $categoriesData = [
            ['name' => 'Cincin', 'slug' => 'cincin', 'icon' => 'CircleDot'],
            ['name' => 'Kalung', 'slug' => 'kalung', 'icon' => 'Sparkles'],
            ['name' => 'Gelang', 'slug' => 'gelang', 'icon' => 'Coins'],
            ['name' => 'Anting / Giwang', 'slug' => 'anting', 'icon' => 'Gem'],
            ['name' => 'Liontin', 'slug' => 'liontin', 'icon' => 'Shield'],
            ['name' => 'Logam Mulia (LM)', 'slug' => 'logam-mulia', 'icon' => 'Layers'],
        ];

        $catModels = [];
        foreach ($categoriesData as $c) {
            $catModels[$c['slug']] = Category::firstOrCreate(['slug' => $c['slug']], $c);
        }

        // 5. Baki / Nampan Etalase
        $traysData = [
            [
                'code' => 'BK-CNC-01',
                'name' => 'Baki Cincin Solitaire & Berlian',
                'showcase_location' => 'Etalase Depan A1',
                'category_id' => $catModels['cincin']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'capacity' => 24,
            ],
            [
                'code' => 'BK-CNC-02',
                'name' => 'Baki Cincin Fashion Emas Muda',
                'showcase_location' => 'Etalase Depan A2',
                'category_id' => $catModels['cincin']->id,
                'gold_rate_id' => $rateModels['9K']->id,
                'capacity' => 24,
            ],
            [
                'code' => 'BK-KLG-01',
                'name' => 'Baki Kalung Rantai & Choker',
                'showcase_location' => 'Etalase Tengah B1',
                'category_id' => $catModels['kalung']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'capacity' => 18,
            ],
            [
                'code' => 'BK-GLG-01',
                'name' => 'Baki Gelang Bangle & Rantai',
                'showcase_location' => 'Etalase Tengah B2',
                'category_id' => $catModels['gelang']->id,
                'gold_rate_id' => $rateModels['18K']->id,
                'capacity' => 16,
            ],
            [
                'code' => 'BK-ANT-01',
                'name' => 'Baki Anting & Giwang Permata',
                'showcase_location' => 'Etalase Kaca Kiri C1',
                'category_id' => $catModels['anting']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'capacity' => 30,
            ],
            [
                'code' => 'BK-LM-01',
                'name' => 'Baki Logam Mulia Antam / UBS',
                'showcase_location' => 'Brankas Utama',
                'category_id' => $catModels['logam-mulia']->id,
                'gold_rate_id' => $rateModels['24K']->id,
                'capacity' => 50,
            ],
        ];

        $trayModels = [];
        foreach ($traysData as $t) {
            $trayModels[$t['code']] = Tray::firstOrCreate(['code' => $t['code']], $t);
        }

        // 6. Contoh Inventaris Perhiasan dengan Barcode
        $itemsData = [
            // Cincin
            [
                'sku' => 'EMAS-202610-001',
                'name' => 'Cincin Solitaire 17K Permata Zircon Swarovski',
                'category_id' => $catModels['cincin']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'tray_id' => $trayModels['BK-CNC-01']->id,
                'gross_weight' => 3.250,
                'stone_weight' => 0.150,
                'net_weight' => 3.100,
                'crafting_fee' => 150000,
            ],
            [
                'sku' => 'EMAS-202610-002',
                'name' => 'Cincin Simple Twist Rose Gold 17K',
                'category_id' => $catModels['cincin']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'tray_id' => $trayModels['BK-CNC-01']->id,
                'gross_weight' => 2.100,
                'stone_weight' => 0.000,
                'net_weight' => 2.100,
                'crafting_fee' => 120000,
            ],
            [
                'sku' => 'EMAS-202610-003',
                'name' => 'Cincin Daun Minimalis Emas Muda 9K',
                'category_id' => $catModels['cincin']->id,
                'gold_rate_id' => $rateModels['9K']->id,
                'tray_id' => $trayModels['BK-CNC-02']->id,
                'gross_weight' => 1.450,
                'stone_weight' => 0.000,
                'net_weight' => 1.450,
                'crafting_fee' => 85000,
            ],
            // Kalung
            [
                'sku' => 'EMAS-202610-004',
                'name' => 'Kalung Italy Santa 17K Halus 42cm',
                'category_id' => $catModels['kalung']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'tray_id' => $trayModels['BK-KLG-01']->id,
                'gross_weight' => 4.820,
                'stone_weight' => 0.000,
                'net_weight' => 4.820,
                'crafting_fee' => 220000,
            ],
            [
                'sku' => 'EMAS-202610-005',
                'name' => 'Kalung Nori Polos 17K Elegan 45cm',
                'category_id' => $catModels['kalung']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'tray_id' => $trayModels['BK-KLG-01']->id,
                'gross_weight' => 6.150,
                'stone_weight' => 0.000,
                'net_weight' => 6.150,
                'crafting_fee' => 250000,
            ],
            // Gelang
            [
                'sku' => 'EMAS-202610-006',
                'name' => 'Gelang Bangle Cartier Look 18K Yellow Gold',
                'category_id' => $catModels['gelang']->id,
                'gold_rate_id' => $rateModels['18K']->id,
                'tray_id' => $trayModels['BK-GLG-01']->id,
                'gross_weight' => 8.500,
                'stone_weight' => 0.000,
                'net_weight' => 8.500,
                'crafting_fee' => 350000,
            ],
            [
                'sku' => 'EMAS-202610-007',
                'name' => 'Gelang Serut Bola-Bola 18K Rose Gold',
                'category_id' => $catModels['gelang']->id,
                'gold_rate_id' => $rateModels['18K']->id,
                'tray_id' => $trayModels['BK-GLG-01']->id,
                'gross_weight' => 5.200,
                'stone_weight' => 0.100,
                'net_weight' => 5.100,
                'crafting_fee' => 280000,
            ],
            // Anting
            [
                'sku' => 'EMAS-202610-008',
                'name' => 'Anting Giwang Mata Satu 17K Zircon Putih',
                'category_id' => $catModels['anting']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'tray_id' => $trayModels['BK-ANT-01']->id,
                'gross_weight' => 1.850,
                'stone_weight' => 0.120,
                'net_weight' => 1.730,
                'crafting_fee' => 110000,
            ],
            // Logam Mulia 24K
            [
                'sku' => 'EMAS-202610-009',
                'name' => 'Logam Mulia 24K Antam CertiCard 5 Gram',
                'category_id' => $catModels['logam-mulia']->id,
                'gold_rate_id' => $rateModels['24K']->id,
                'tray_id' => $trayModels['BK-LM-01']->id,
                'gross_weight' => 5.000,
                'stone_weight' => 0.000,
                'net_weight' => 5.000,
                'crafting_fee' => 100000,
            ],
            [
                'sku' => 'EMAS-202610-010',
                'name' => 'Logam Mulia 24K Antam CertiCard 10 Gram',
                'category_id' => $catModels['logam-mulia']->id,
                'gold_rate_id' => $rateModels['24K']->id,
                'tray_id' => $trayModels['BK-LM-01']->id,
                'gross_weight' => 10.000,
                'stone_weight' => 0.000,
                'net_weight' => 10.000,
                'crafting_fee' => 150000,
            ],
            // Logam Mulia Lainnya
            [
                'sku' => 'EMAS-202610-011',
                'name' => 'Logam Mulia 24K Antam CertiCard 1 Gram',
                'category_id' => $catModels['logam-mulia']->id,
                'gold_rate_id' => $rateModels['24K']->id,
                'tray_id' => $trayModels['BK-LM-01']->id,
                'gross_weight' => 1.000,
                'stone_weight' => 0.000,
                'net_weight' => 1.000,
                'crafting_fee' => 60000,
            ],
            [
                'sku' => 'EMAS-202610-012',
                'name' => 'Logam Mulia 24K Antam CertiCard 2 Gram',
                'category_id' => $catModels['logam-mulia']->id,
                'gold_rate_id' => $rateModels['24K']->id,
                'tray_id' => $trayModels['BK-LM-01']->id,
                'gross_weight' => 2.000,
                'stone_weight' => 0.000,
                'net_weight' => 2.000,
                'crafting_fee' => 75000,
            ],
            // Cincin Tambahan
            [
                'sku' => 'EMAS-202610-013',
                'name' => 'Cincin Kawin Polos Comfort Fit 22K',
                'category_id' => $catModels['cincin']->id,
                'gold_rate_id' => $rateModels['22K']->id,
                'tray_id' => $trayModels['BK-CNC-01']->id,
                'gross_weight' => 4.200,
                'stone_weight' => 0.000,
                'net_weight' => 4.200,
                'crafting_fee' => 180000,
            ],
            [
                'sku' => 'EMAS-202610-014',
                'name' => 'Cincin Eternity Permata Zircon Keliling 18K',
                'category_id' => $catModels['cincin']->id,
                'gold_rate_id' => $rateModels['18K']->id,
                'tray_id' => $trayModels['BK-CNC-01']->id,
                'gross_weight' => 2.850,
                'stone_weight' => 0.250,
                'net_weight' => 2.600,
                'crafting_fee' => 160000,
            ],
            [
                'sku' => 'EMAS-202610-015',
                'name' => 'Cincin Emas Muda 8K Motif Bunga Melati',
                'category_id' => $catModels['cincin']->id,
                'gold_rate_id' => $rateModels['8K']->id,
                'tray_id' => $trayModels['BK-CNC-02']->id,
                'gross_weight' => 1.200,
                'stone_weight' => 0.000,
                'net_weight' => 1.200,
                'crafting_fee' => 70000,
            ],
            // Kalung Tambahan
            [
                'sku' => 'EMAS-202610-016',
                'name' => 'Kalung Tambang Mewah 22K Tradisional 50cm',
                'category_id' => $catModels['kalung']->id,
                'gold_rate_id' => $rateModels['22K']->id,
                'tray_id' => $trayModels['BK-KLG-01']->id,
                'gross_weight' => 10.500,
                'stone_weight' => 0.000,
                'net_weight' => 10.500,
                'crafting_fee' => 380000,
            ],
            [
                'sku' => 'EMAS-202610-017',
                'name' => 'Kalung Cassandra Choker Rose Gold 17K',
                'category_id' => $catModels['kalung']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'tray_id' => $trayModels['BK-KLG-01']->id,
                'gross_weight' => 3.400,
                'stone_weight' => 0.000,
                'net_weight' => 3.400,
                'crafting_fee' => 190000,
            ],
            // Gelang Tambahan
            [
                'sku' => 'EMAS-202610-018',
                'name' => 'Gelang Rantai Sisik Naga 22K Berat',
                'category_id' => $catModels['gelang']->id,
                'gold_rate_id' => $rateModels['22K']->id,
                'tray_id' => $trayModels['BK-GLG-01']->id,
                'gross_weight' => 15.200,
                'stone_weight' => 0.000,
                'net_weight' => 15.200,
                'crafting_fee' => 450000,
            ],
            [
                'sku' => 'EMAS-202610-019',
                'name' => 'Gelang Plat Bayi Polos Kuning 17K',
                'category_id' => $catModels['gelang']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'tray_id' => $trayModels['BK-GLG-01']->id,
                'gross_weight' => 2.250,
                'stone_weight' => 0.000,
                'net_weight' => 2.250,
                'crafting_fee' => 130000,
            ],
            // Anting Tambahan
            [
                'sku' => 'EMAS-202610-020',
                'name' => 'Anting Jepit Huggies Diamond Cut 18K White Gold',
                'category_id' => $catModels['anting']->id,
                'gold_rate_id' => $rateModels['18K']->id,
                'tray_id' => $trayModels['BK-ANT-01']->id,
                'gross_weight' => 2.450,
                'stone_weight' => 0.000,
                'net_weight' => 2.450,
                'crafting_fee' => 140000,
            ],
            [
                'sku' => 'EMAS-202610-021',
                'name' => 'Anting Rumbai Panjang Elegant Tassel 17K',
                'category_id' => $catModels['anting']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'tray_id' => $trayModels['BK-ANT-01']->id,
                'gross_weight' => 3.100,
                'stone_weight' => 0.080,
                'net_weight' => 3.020,
                'crafting_fee' => 175000,
            ],
            // Liontin
            [
                'sku' => 'EMAS-202610-022',
                'name' => 'Liontin Huruf Initial A Berlian Look 17K Rose Gold',
                'category_id' => $catModels['liontin']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'tray_id' => $trayModels['BK-KLG-01']->id,
                'gross_weight' => 1.350,
                'stone_weight' => 0.050,
                'net_weight' => 1.300,
                'crafting_fee' => 95000,
            ],
        ];

        foreach ($itemsData as $itemData) {
            $goldRate = GoldRate::find($itemData['gold_rate_id']);
            $ratePrice = $goldRate ? (float) $goldRate->sell_price_per_gram : 0;
            $calcTotalPrice = round(($ratePrice * (float) $itemData['net_weight']) + (float) $itemData['crafting_fee'], 2);

            Item::firstOrCreate(
                ['sku' => $itemData['sku']],
                array_merge($itemData, [
                    'total_price' => $calcTotalPrice,
                    'status' => 'available',
                ])
            );
        }

        // 7. Hitung Ulang Total Bobot dan Jumlah Item di Setiap Baki
        foreach ($trayModels as $tray) {
            $tray->recalculateTotals();
        }

        // 8. Seeder Pegawai & Riwayat Penggajian (Fase 5)
        $this->call([
            \Database\Seeders\EmployeeAndPayrollSeeder::class,
        ]);
    }
}
