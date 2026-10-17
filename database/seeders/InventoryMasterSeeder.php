<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\Item;
use Illuminate\Database\Seeder;

class InventoryMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Brands (Pabrikan Emas & Perhiasan Populer di Indonesia)
        $brands = [
            ['name' => 'Antam (Logam Mulia)', 'code' => 'ANTAM', 'description' => 'PT Aneka Tambang Tbk - Emas batangan certicard resmi.'],
            ['name' => 'UBS Gold', 'code' => 'UBS', 'description' => 'PT Untung Bersama Sejahtera - Perhiasan & Logam Mulia.'],
            ['name' => 'Lotus Archi', 'code' => 'LOTUS', 'description' => 'Emas batangan & perhiasan presisi tinggi.'],
            ['name' => 'Hala Gold', 'code' => 'HALA', 'description' => 'Perhiasan emas kadar pas 75.5% (18K) bersertifikat.'],
            ['name' => 'King Halim', 'code' => 'KH', 'description' => 'Produsen rantai kalung & gelang emas terkemuka.'],
            ['name' => 'Hartadinata Abadi', 'code' => 'HRTA', 'description' => 'Perhiasan emas modis & koin emas murni.'],
            ['name' => 'Lokal / Pengrajin', 'code' => 'LOKAL', 'description' => 'Perhiasan karya pengrajin emas lokal.'],
        ];

        foreach ($brands as $b) {
            Brand::firstOrCreate(['code' => $b['code']], $b);
        }

        // 2. Units (Satuan Barang)
        $units = [
            ['name' => 'Gram', 'symbol' => 'gr', 'description' => 'Satuan standar berat emas (presisi 3 desimal 0.001gr).'],
            ['name' => 'Pieces / Buah', 'symbol' => 'pcs', 'description' => 'Satuan perhiasan utuh atau emas certicard.'],
            ['name' => 'Butir', 'symbol' => 'btr', 'description' => 'Satuan permata / berlian / batu zircon.'],
            ['name' => 'Set / Pasang', 'symbol' => 'set', 'description' => 'Satuan pasang (anting, cincin kawin couple).'],
        ];

        foreach ($units as $u) {
            Unit::firstOrCreate(['symbol' => $u['symbol']], $u);
        }

        // 3. Warehouses (Gudang & Lokasi Brankas)
        $warehouses = [
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

        foreach ($warehouses as $w) {
            Warehouse::firstOrCreate(['code' => $w['code']], $w);
        }

        // Hubungkan item yang sudah ada ke brand & warehouse default jika kosong
        $defaultBrand = Brand::where('code', 'UBS')->first();
        $defaultUnit = Unit::where('symbol', 'gr')->first();
        $defaultWarehouse = Warehouse::where('code', 'ETL-DPN')->first();

        Item::whereNull('brand_id')->update([
            'brand_id' => $defaultBrand?->id,
            'unit_id' => $defaultUnit?->id,
            'warehouse_id' => $defaultWarehouse?->id,
        ]);
    }
}
