<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Brand;
use App\Models\Buyback;
use App\Models\BuybackDeduction;
use App\Models\BuybackItem;
use App\Models\CashTransaction;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerPointLog;
use App\Models\DebtPayable;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\GoldRate;
use App\Models\GoldRateHistory;
use App\Models\Item;
use App\Models\JewelryService;
use App\Models\PawnTransaction;
use App\Models\Payroll;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockAudit;
use App\Models\StockAuditItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Tray;
use App\Models\TrayMovement;
use App\Models\TrayScaleLog;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ComprehensiveDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Pengguna (Owner & Kasir)
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

        // 2. Akun Bank Kasir
        $bca = BankAccount::firstOrCreate(
            ['bank_name' => 'BCA', 'account_number' => '8921-8899-001'],
            ['account_holder' => 'Toko Mas Permata Abadi', 'is_active' => true, 'notes' => 'Rekening Utama Kasir BCA']
        );
        $mandiri = BankAccount::firstOrCreate(
            ['bank_name' => 'Mandiri', 'account_number' => '137-00-998877-6'],
            ['account_holder' => 'Toko Mas Permata Abadi', 'is_active' => true, 'notes' => 'Rekening EDC Mandiri']
        );
        $qris = BankAccount::firstOrCreate(
            ['bank_name' => 'QRIS', 'account_number' => 'NMID: ID1020261998822'],
            ['account_holder' => 'Permata Abadi Gold QRIS', 'is_active' => true, 'notes' => 'QRIS Kasir Standar Nasional']
        );

        // 3. Master Brand & Merk
        $brandsData = [
            ['name' => 'Antam (Logam Mulia)', 'code' => 'ANTAM', 'description' => 'PT Aneka Tambang Tbk - Emas batangan certicard resmi.'],
            ['name' => 'UBS Gold', 'code' => 'UBS', 'description' => 'PT Untung Bersama Sejahtera - Perhiasan & Logam Mulia.'],
            ['name' => 'Lotus Archi', 'code' => 'LOTUS', 'description' => 'Emas batangan & perhiasan presisi tinggi.'],
            ['name' => 'Hala Gold', 'code' => 'HALA', 'description' => 'Perhiasan emas kadar pas 75.5% (18K) bersertifikat.'],
            ['name' => 'King Halim', 'code' => 'KH', 'description' => 'Produsen rantai kalung & gelang emas terkemuka.'],
            ['name' => 'Hartadinata Abadi', 'code' => 'HRTA', 'description' => 'Perhiasan emas modis & koin emas murni.'],
            ['name' => 'Lokal / Pengrajin', 'code' => 'LOKAL', 'description' => 'Perhiasan karya pengrajin emas lokal.'],
        ];
        $brandModels = [];
        foreach ($brandsData as $b) {
            $brandModels[$b['code']] = Brand::firstOrCreate(['code' => $b['code']], $b);
        }

        // 4. Master Satuan Barang (UoM)
        $unitsData = [
            ['name' => 'Gram', 'symbol' => 'gr', 'description' => 'Satuan standar berat emas (presisi 3 desimal 0.001gr).'],
            ['name' => 'Pieces / Buah', 'symbol' => 'pcs', 'description' => 'Satuan perhiasan utuh atau emas certicard.'],
            ['name' => 'Butir', 'symbol' => 'btr', 'description' => 'Satuan permata / berlian / batu zircon.'],
            ['name' => 'Set / Pasang', 'symbol' => 'set', 'description' => 'Satuan pasang (anting, cincin kawin couple).'],
        ];
        $unitModels = [];
        foreach ($unitsData as $u) {
            $unitModels[$u['symbol']] = Unit::firstOrCreate(['symbol' => $u['symbol']], $u);
        }

        // 5. Master Gudang & Brankas
        $warehousesData = [
            [
                'code' => 'GD-UTAMA',
                'name' => 'Gudang Utama & Brankas Pusat',
                'location' => 'Lantai 2 Ruang Khusus Khazanah',
                'pic_name' => 'H. Agung Wijaya',
                'pic_phone' => '081234567890',
                'notes' => 'Penyimpanan stok baru, cadangan batangan, dan barang belum display.',
            ],
            [
                'code' => 'ETL-DPN',
                'name' => 'Etalase Toko Depan',
                'location' => 'Lantai 1 Area Penjualan Kasir',
                'pic_name' => 'Kasir Utama',
                'pic_phone' => '081298765432',
                'notes' => 'Semua perhiasan yang terpajang aktif di baki etalase kasir.',
            ],
            [
                'code' => 'BK-LEBUR',
                'name' => 'Brankas Penampungan Buyback / Siap Lebur',
                'location' => 'Lantai 1 Ruang Kasir Dalam',
                'pic_name' => 'Kepala Kasir',
                'pic_phone' => '081298765432',
                'notes' => 'Emas bekas buyback dari pelanggan yang disiapkan untuk cuci ulang atau lebur.',
            ],
        ];
        $whModels = [];
        foreach ($warehousesData as $w) {
            $whModels[$w['code']] = Warehouse::firstOrCreate(['code' => $w['code']], $w);
        }

        // 6. Master Kadar & Kurs Emas
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
            $rateModels[$gr['code']] = GoldRate::firstOrCreate(
                ['code' => $gr['code']],
                array_merge($gr, ['updated_by' => $owner->id])
            );
        }

        // 7. Master Kategori & Baki Etalase
        $categoriesData = [
            ['name' => 'CINCIN', 'code' => 'CCN', 'slug' => 'cincin', 'description' => 'Koleksi cincin pria, wanita, dan cincin nikah'],
            ['name' => 'GELANG', 'code' => 'GLG', 'slug' => 'gelang', 'description' => 'Gelang rantai, bangle kaku, dan gelang serut'],
            ['name' => 'KALUNG', 'code' => 'KLG', 'slug' => 'kalung', 'description' => 'Kalung rantai, kalung chocker, dan variasi'],
            ['name' => 'ANTING', 'code' => 'ANT', 'slug' => 'anting', 'description' => 'Anting jepit, anting tusuk, dan giwang mutiara'],
            ['name' => 'LIONTIN', 'code' => 'LTN', 'slug' => 'liontin', 'description' => 'Bandul kalung inisial, batu mulia, dan religi'],
            ['name' => 'LOGAM MULIA', 'code' => 'LM', 'slug' => 'logam-mulia', 'description' => 'Emas batangan murni certicard Antam/UBS'],
        ];
        $catModels = [];
        foreach ($categoriesData as $c) {
            $catModels[$c['slug']] = Category::firstOrCreate(['slug' => $c['slug']], $c);
        }

        $traysData = [
            ['code' => 'BK-CCN-01', 'name' => 'Baki Cincin Dewasa 17K-18K', 'category_id' => $catModels['cincin']->id, 'capacity' => 24, 'total_items' => 0, 'total_weight' => 0],
            ['code' => 'BK-GLG-01', 'name' => 'Baki Gelang Bangle & Rantai 17K', 'category_id' => $catModels['gelang']->id, 'capacity' => 15, 'total_items' => 0, 'total_weight' => 0],
            ['code' => 'BK-KLG-01', 'name' => 'Baki Kalung Fashion & Italy 18K', 'category_id' => $catModels['kalung']->id, 'capacity' => 18, 'total_items' => 0, 'total_weight' => 0],
            ['code' => 'BK-ANT-01', 'name' => 'Baki Anting & Giwang Solitaire', 'category_id' => $catModels['anting']->id, 'capacity' => 30, 'total_items' => 0, 'total_weight' => 0],
            ['code' => 'BK-LM-01', 'name' => 'Baki Emas Batangan Logam Mulia', 'category_id' => $catModels['logam-mulia']->id, 'capacity' => 50, 'total_items' => 0, 'total_weight' => 0],
        ];
        $trayModels = [];
        foreach ($traysData as $t) {
            $trayModels[$t['code']] = Tray::firstOrCreate(['code' => $t['code']], $t);
        }

        // 8. Master Item Perhiasan Emas
        $itemsData = [
            [
                'sku' => 'EMAS-202610-001',
                'name' => 'Cincin Solitaire Zirconia 17K Rose Gold',
                'category_id' => $catModels['cincin']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'tray_id' => $trayModels['BK-CCN-01']->id,
                'brand_id' => $brandModels['HALA']->id,
                'unit_id' => $unitModels['gr']->id,
                'warehouse_id' => $whModels['ETL-DPN']->id,
                'gross_weight' => 2.350,
                'stone_weight' => 0.050,
                'net_weight' => 2.300,
                'crafting_fee' => 125000,
            ],
            [
                'sku' => 'EMAS-202610-002',
                'name' => 'Gelang Rantai Nori Fancy 18K Yellow Gold',
                'category_id' => $catModels['gelang']->id,
                'gold_rate_id' => $rateModels['18K']->id,
                'tray_id' => $trayModels['BK-GLG-01']->id,
                'brand_id' => $brandModels['UBS']->id,
                'unit_id' => $unitModels['gr']->id,
                'warehouse_id' => $whModels['ETL-DPN']->id,
                'gross_weight' => 6.200,
                'stone_weight' => 0.000,
                'net_weight' => 6.200,
                'crafting_fee' => 220000,
            ],
            [
                'sku' => 'EMAS-202610-003',
                'name' => 'Kalung Rantai Milimeter Halus 17K White Gold',
                'category_id' => $catModels['kalung']->id,
                'gold_rate_id' => $rateModels['17K']->id,
                'tray_id' => $trayModels['BK-KLG-01']->id,
                'brand_id' => $brandModels['KH']->id,
                'unit_id' => $unitModels['gr']->id,
                'warehouse_id' => $whModels['ETL-DPN']->id,
                'gross_weight' => 4.150,
                'stone_weight' => 0.000,
                'net_weight' => 4.150,
                'crafting_fee' => 160000,
            ],
            [
                'sku' => 'EMAS-202610-004',
                'name' => 'Anting Jepit Huggies Diamond Cut 18K',
                'category_id' => $catModels['anting']->id,
                'gold_rate_id' => $rateModels['18K']->id,
                'tray_id' => $trayModels['BK-ANT-01']->id,
                'brand_id' => $brandModels['UBS']->id,
                'unit_id' => $unitModels['gr']->id,
                'warehouse_id' => $whModels['ETL-DPN']->id,
                'gross_weight' => 2.450,
                'stone_weight' => 0.000,
                'net_weight' => 2.450,
                'crafting_fee' => 140000,
            ],
            [
                'sku' => 'EMAS-202610-005',
                'name' => 'Logam Mulia Antam Certicard 5 Gram (99.9%)',
                'category_id' => $catModels['logam-mulia']->id,
                'gold_rate_id' => $rateModels['24K']->id,
                'tray_id' => $trayModels['BK-LM-01']->id,
                'brand_id' => $brandModels['ANTAM']->id,
                'unit_id' => $unitModels['gr']->id,
                'warehouse_id' => $whModels['ETL-DPN']->id,
                'gross_weight' => 5.000,
                'stone_weight' => 0.000,
                'net_weight' => 5.000,
                'crafting_fee' => 75000,
            ],
        ];

        $createdItems = [];
        foreach ($itemsData as $itemData) {
            $goldRate = GoldRate::find($itemData['gold_rate_id']);
            $ratePrice = $goldRate ? (float) $goldRate->sell_price_per_gram : 0;
            $calcTotalPrice = round(($ratePrice * (float) $itemData['net_weight']) + (float) $itemData['crafting_fee'], 2);

            $createdItems[] = Item::firstOrCreate(
                ['sku' => $itemData['sku']],
                array_merge($itemData, [
                    'total_price' => $calcTotalPrice,
                    'status' => 'available',
                ])
            );
        }

        foreach ($trayModels as $tray) {
            $tray->recalculateTotals();
        }

        // 9. Mutasi Baki Barang (TrayMovement)
        if (count($createdItems) >= 2) {
            TrayMovement::firstOrCreate(
                ['item_id' => $createdItems[0]->id, 'reason' => 'Display penataan ulang etalase kaca depan'],
                [
                    'from_tray_id' => $trayModels['BK-CCN-01']->id,
                    'to_tray_id' => $trayModels['BK-CCN-01']->id,
                    'moved_by' => $owner->id,
                ]
            );
        }

        // 10. Stok Opname Baki (StockAudit)
        $audit = StockAudit::firstOrCreate(
            ['audit_code' => 'AUD-202610-001'],
            [
                'tray_id' => $trayModels['BK-CCN-01']->id,
                'auditor_id' => $owner->id,
                'status' => 'completed',
                'total_expected_items' => 8,
                'total_scanned_items' => 8,
                'total_missing_items' => 0,
                'total_mismatched_items' => 0,
                'completed_at' => Carbon::now(),
                'notes' => 'Audit rutin harian baki cincin. Semua item cocok dan akurat.',
            ]
        );

        // 10b. Keluar Masuk Barang (StockMovement)
        if (count($createdItems) >= 3) {
            StockMovement::firstOrCreate(
                ['reference_number' => 'MOV-IN-202610-001'],
                [
                    'item_id' => $createdItems[0]->id,
                    'type' => 'in',
                    'reason' => 'purchase_supplier',
                    'weight' => $createdItems[0]->gross_weight,
                    'warehouse_id' => $whModels['GD-UTAMA']->id,
                    'user_id' => $owner->id,
                    'notes' => 'Penerimaan stok perhiasan baru dari pengrajin/pabrik UBS. Barang masuk kondisi baru, lengkap dengan sertifikat keaslian kadar.',
                ]
            );

            StockMovement::firstOrCreate(
                ['reference_number' => 'MOV-OUT-202610-001'],
                [
                    'item_id' => $createdItems[1]->id,
                    'type' => 'out',
                    'reason' => 'melted',
                    'weight' => $createdItems[1]->gross_weight,
                    'warehouse_id' => $whModels['GD-UTAMA']->id,
                    'user_id' => $cashier->id,
                    'notes' => 'Pengeluaran barang perhiasan model lama untuk proses peleburan batangan.',
                ]
            );
        }

        // 11. Pelanggan & Point Log
        $customersData = [
            [
                'code' => 'P92604281558',
                'name' => 'Hj. Siti Aminah',
                'identity_number' => '3201012304850001',
                'birth_date' => '1985-04-23',
                'phone' => '081234954995',
                'email' => 'siti.aminah@gmail.com',
                'city' => 'Yogyakarta',
                'address' => 'Jl. Kaliurang KM 7.5 No. 42, Sleman',
                'level' => 'pelanggan',
                'points' => 350,
                'total_items_count' => 4,
                'total_purchased_amount' => 18500000,
            ],
            [
                'code' => 'P92510099774',
                'name' => 'Dimas Septian Irawan',
                'identity_number' => '3273051409900003',
                'birth_date' => '1990-09-14',
                'phone' => '08996260597',
                'email' => 'dimas@gmail.com',
                'city' => 'Bandung',
                'address' => 'Perumahan Cijati Asri Blok B No. 12',
                'level' => 'agen',
                'points' => 520,
                'total_items_count' => 12,
                'total_purchased_amount' => 45000000,
            ],
        ];

        $custModels = [];
        foreach ($customersData as $cd) {
            $custModels[] = Customer::firstOrCreate(['code' => $cd['code']], $cd);
        }

        // 12. Supplier & Faktur Pembelian (Purchase)
        $suppliersData = [
            [
                'code' => 'SUP-ANTAM',
                'name' => 'Marketing Logam Mulia',
                'company_name' => 'PT Aneka Tambang Tbk (Antam)',
                'phone' => '081122334455',
                'email' => 'sales@antamgold.com',
                'city' => 'Jakarta Timur',
                'address' => 'Gedung Aneka Tambang Tower A, Jl. TB Simatupang No. 1',
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '1270001234567',
                'bank_account_holder' => 'PT Aneka Tambang Tbk',
            ],
            [
                'code' => 'SUP-UBS',
                'name' => 'Divisi Perhiasan Emas',
                'company_name' => 'PT Untung Bersama Sejahtera (UBS)',
                'phone' => '081299887766',
                'email' => 'order@ubsgold.com',
                'city' => 'Surabaya',
                'address' => 'Jl. Kenjeran No. 395-399',
                'bank_name' => 'BCA',
                'bank_account_number' => '0188997766',
                'bank_account_holder' => 'PT Untung Bersama Sejahtera',
            ],
        ];
        $suppModels = [];
        foreach ($suppliersData as $sd) {
            $suppModels[] = Supplier::firstOrCreate(['code' => $sd['code']], $sd);
        }

        // Faktur Pembelian Supplier
        $purchase = Purchase::firstOrCreate(
            ['invoice_number' => 'PUR-202610-0001'],
            [
                'supplier_invoice_number' => 'INV-UBS/X/2026/8991',
                'supplier_id' => $suppModels[1]->id,
                'invoice_date' => Carbon::now()->subDays(5)->toDateString(),
                'due_date' => Carbon::now()->addDays(25)->toDateString(),
                'total_items' => 10,
                'total_weight' => 52.400,
                'subtotal' => 54500000,
                'tax' => 179850, // PPN 0.33%
                'discount' => 0,
                'grand_total' => 54679850,
                'payment_status' => 'partial',
                'amount_paid' => 25000000,
                'remaining_balance' => 29679850,
                'payment_method' => 'transfer',
                'warehouse_id' => $whModels['GD-UTAMA']->id,
                'user_id' => $owner->id,
                'notes' => 'Pasokan rantai kalung dan cincin emas 18K dari pabrik UBS.',
            ]
        );

        // 13. Potongan Master Buyback (Sitokmas Schema)
        $buybackRules = [
            ['condition_status' => 'Siap Jual', 'category_id' => null, 'gold_rate_id' => null, 'deduction_amount' => 200000, 'deduction_type' => 'nominal', 'deduct_by_weight' => false],
            ['condition_status' => 'Jual Baik', 'category_id' => null, 'gold_rate_id' => null, 'deduction_amount' => 100000, 'deduction_type' => 'nominal', 'deduct_by_weight' => false],
            ['condition_status' => 'Barang Rusak/Patah', 'category_id' => null, 'gold_rate_id' => null, 'deduction_amount' => 50000, 'deduction_type' => 'nominal', 'deduct_by_weight' => true],
            ['condition_status' => 'Surat Hilang/Non-Toko', 'category_id' => null, 'gold_rate_id' => null, 'deduction_amount' => 75000, 'deduction_type' => 'nominal', 'deduct_by_weight' => true],
        ];
        foreach ($buybackRules as $br) {
            BuybackDeduction::firstOrCreate(['condition_status' => $br['condition_status']], $br);
        }

        // 14. Transaksi Penjualan POS (Sale & SaleItem)
        $sale = Sale::firstOrCreate(
            ['invoice_number' => 'INV-202610-0001'],
            [
                'customer_id' => $custModels[0]->id,
                'customer_name' => $custModels[0]->name,
                'customer_phone' => $custModels[0]->phone,
                'customer_address' => $custModels[0]->address,
                'total_weight' => 2.300,
                'subtotal' => 2448000,
                'discount' => 0,
                'tax' => 8078, // PPN Emas
                'grand_total' => 2456078,
                'payment_method' => 'qris',
                'bank_account_id' => $qris->id,
                'amount_paid' => 2456078,
                'change_due' => 0,
                'cashier_id' => $cashier->id,
                'notes' => 'Penjualan cincin emas 17K via Kasir POS',
                'created_at' => Carbon::now()->subHours(4),
            ]
        );

        SaleItem::firstOrCreate(
            ['sale_id' => $sale->id, 'item_id' => $createdItems[0]->id],
            [
                'item_name' => $createdItems[0]->name,
                'sku' => $createdItems[0]->sku,
                'category_name' => 'CINCIN',
                'gold_rate_name' => '17K',
                'gross_weight' => 2.350,
                'stone_weight' => 0.050,
                'net_weight' => 2.300,
                'gold_price_per_gram' => 1010000,
                'crafting_fee' => 125000,
                'subtotal' => 2448000,
            ]
        );

        // 15. Transaksi Pembelian Cepat / Beli Kembali (Buyback & BuybackItem)
        $buyback = Buyback::firstOrCreate(
            ['buyback_number' => 'BB-202610-0001'],
            [
                'customer_name' => 'Hj. Siti Aminah',
                'customer_phone' => '081234954995',
                'customer_nik' => '3201012304850001',
                'original_invoice_number' => 'INV-202609-0891',
                'total_weight_original' => 3.100,
                'total_weight_actual' => 3.080,
                'total_deduction' => 100000,
                'grand_total' => 2641200,
                'payment_method' => 'cash',
                'cashier_id' => $cashier->id,
                'notes' => 'Buyback kalung emas kuning 17K surat lengkap kondisi baik.',
                'created_at' => Carbon::now()->subDays(1),
            ]
        );

        BuybackItem::firstOrCreate(
            ['buyback_id' => $buyback->id, 'item_name' => 'Kalung Rantai Milimeter 17K'],
            [
                'gold_rate_id' => $rateModels['17K']->id,
                'weight_actual' => 3.080,
                'buy_price_per_gram' => 890000,
                'deduction_amount' => 100000,
                'total_amount' => 2641200,
                'condition_notes' => 'Jual Baik, surat pembelian lengkap',
                'destination' => 'stock_secondary',
            ]
        );

        // 16. Gadai Perhiasan (PawnTransaction)
        PawnTransaction::firstOrCreate(
            ['pawn_number' => 'GD-202610-0001'],
            [
                'customer_id' => $custModels[0]->id,
                'customer_name' => $custModels[0]->name,
                'customer_phone' => $custModels[0]->phone,
                'item_name' => 'Gelang Emas Bangle Kaku 22K',
                'gold_rate_id' => $rateModels['22K']->id,
                'gross_weight' => 7.850,
                'estimated_value' => 9263000,
                'loan_amount' => 7000000,
                'monthly_interest_rate' => 1.50,
                'monthly_fee' => 105000,
                'start_date' => Carbon::now()->subDays(10)->toDateString(),
                'due_date' => Carbon::now()->addDays(20)->toDateString(),
                'status' => 'active',
                'cashier_id' => $cashier->id,
                'notes' => 'Emas mulus, surat lengkap, pinjaman 30 hari.',
            ]
        );

        // 17. Servis Perhiasan (JewelryService)
        JewelryService::firstOrCreate(
            ['service_number' => 'SRV-202610-0001'],
            [
                'customer_id' => $custModels[0]->id,
                'customer_name' => $custModels[0]->name,
                'customer_phone' => $custModels[0]->phone,
                'item_name' => 'Cincin Kawin Emas Putih & Kuning',
                'weight_before' => 3.420,
                'service_type' => 'Cuci Kilap Sepuh Ulang & Patri Sambung',
                'damage_description' => 'Bagian sambungan patah sedikit, warna agak kusam',
                'estimated_cost' => 180000,
                'down_payment' => 50000,
                'remaining_cost' => 130000,
                'received_date' => Carbon::now()->subDays(2)->toDateString(),
                'completion_target_date' => Carbon::now()->addDays(2)->toDateString(),
                'status' => 'in_progress',
                'cashier_id' => $cashier->id,
                'notes' => 'Sepuh kombinasi two-tone gold & white gold.',
            ]
        );

        // 18. Timbang Baki Harian (TrayScaleLog)
        TrayScaleLog::firstOrCreate(
            [
                'tray_id' => $trayModels['BK-CCN-01']->id,
                'scale_date' => Carbon::now()->toDateString(),
                'session' => 'morning',
            ],
            [
                'item_count_system' => 8,
                'item_count_real' => 8,
                'weight_system' => 24.500,
                'weight_real' => 24.500,
                'difference_weight' => 0.000,
                'status' => 'matched',
                'user_id' => $cashier->id,
                'notes' => 'Timbang baki buka toko pagi. Gramasi baki cincin 100% klop.',
            ]
        );

        // 19 & 20. Pegawai, Penggajian, Buku Kas, Pengeluaran & Utang (Employee & Finance)
        $this->call([
            EmployeeAndPayrollSeeder::class,
            FinanceSeeder::class,
        ]);
    }
}
