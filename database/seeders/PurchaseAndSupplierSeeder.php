<?php

namespace Database\Seeders;

use App\Models\BuybackDeduction;
use App\Models\GoldRate;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class PurchaseAndSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data Supplier / Produsen Emas
        $suppliers = [
            [
                'code' => 'SUP-ANTAM',
                'name' => 'Marketing Logam Mulia',
                'company_name' => 'PT Aneka Tambang Tbk (Antam)',
                'phone' => '081122334455',
                'telephone' => '021-29941000',
                'email' => 'sales@antamgold.com',
                'city' => 'Jakarta Timur',
                'address' => 'Gedung Aneka Tambang Tower A, Jl. TB Simatupang No. 1',
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '1270001234567',
                'bank_account_holder' => 'PT Aneka Tambang Tbk',
                'notes' => 'Pemasok resmi emas batangan kemasan CertiCard.',
            ],
            [
                'code' => 'SUP-UBS',
                'name' => 'Divisi Perhiasan Emas',
                'company_name' => 'PT Untung Bersama Sejahtera (UBS)',
                'phone' => '081299887766',
                'telephone' => '031-8700000',
                'email' => 'order@ubsgold.com',
                'city' => 'Surabaya',
                'address' => 'Jl. Kenjeran No. 395-399, Gading, Tambaksari',
                'bank_name' => 'BCA',
                'bank_account_number' => '0188997766',
                'bank_account_holder' => 'PT Untung Bersama Sejahtera',
                'notes' => 'Pemasok perhiasan fashion modern, cincin nikah, dan rantai kalung.',
            ],
            [
                'code' => 'SUP-LOTUS',
                'name' => 'Sales Representative',
                'company_name' => 'PT Lotus Archi',
                'phone' => '081388776655',
                'telephone' => '021-5790000',
                'email' => 'sales@lotusgold.co.id',
                'city' => 'Jakarta Pusat',
                'address' => 'Menara Rajawali Lt. 19, Mega Kuningan',
                'bank_name' => 'Bank Danamon',
                'bank_account_number' => '3590012345',
                'bank_account_holder' => 'PT Lotus Archi',
                'notes' => 'Emas batangan kemasan merah dan perhiasan kadar 750.',
            ],
            [
                'code' => 'SUP-HALA',
                'name' => 'Distributor Hala Gold',
                'company_name' => 'PT Hala Gold Indonesia',
                'phone' => '081234991122',
                'email' => 'info@halagold.com',
                'city' => 'Jakarta Barat',
                'address' => 'Komplek Pertokoan Glodok Jaya No. 15',
                'bank_name' => 'BCA',
                'bank_account_number' => '0245678910',
                'bank_account_holder' => 'PT Hala Gold Indonesia',
                'notes' => 'Spesialis perhiasan emas kadar pas 75.5% (18 Karat).',
            ],
            [
                'code' => 'SUP-LOKAL',
                'name' => 'Pak Joko Pengrajin',
                'company_name' => 'Paguyuban Pengrajin Emas Kotagede',
                'phone' => '081233445566',
                'city' => 'Yogyakarta',
                'address' => 'Jl. Mondorakan No. 28, Kotagede, Yogyakarta',
                'bank_name' => 'BPD DIY',
                'bank_account_number' => '0012345678',
                'bank_account_holder' => 'Joko Nugroho',
                'notes' => 'Pengrajin pesanan cincin kawin custom, patri, dan tatah perhiasan.',
            ],
        ];

        foreach ($suppliers as $s) {
            Supplier::firstOrCreate(['code' => $s['code']], $s);
        }

        // 2. Master Potongan Buyback Per Kadar Emas
        $rates = GoldRate::all();
        $defaultRules = [
            '24K' => ['pct' => 2.50, 'fee' => 10000, 'notes' => 'Potongan 2.5% + Rp 10.000/gr dari harga pasar beli kembali hari berjalan.'],
            '22K' => ['pct' => 3.00, 'fee' => 15000, 'notes' => 'Potongan 3% + susut lebur Rp 15.000/gr. Ongkos pembuatan hangus.'],
            '17K' => ['pct' => 4.00, 'fee' => 20000, 'notes' => 'Kadar 700/750. Potongan 4% + susut Rp 20.000/gr.'],
            '16K' => ['pct' => 5.00, 'fee' => 25000, 'notes' => 'Kadar tua 700. Potongan 5% + susut Rp 25.000/gr.'],
            '9K'  => ['pct' => 7.00, 'fee' => 30000, 'notes' => 'Kadar muda 375. Potongan 7% + susut Rp 30.000/gr.'],
        ];

        foreach ($rates as $rate) {
            $rule = $defaultRules[$rate->code] ?? ['pct' => 5.00, 'fee' => 20000, 'notes' => 'Standar potongan buyback emas resmi.'];
            
            BuybackDeduction::firstOrCreate(
                ['gold_rate_id' => $rate->id],
                [
                    'deduction_percentage' => $rule['pct'],
                    'deduction_fee_per_gram' => $rule['fee'],
                    'crafting_fee_deducted' => true,
                    'condition_notes' => $rule['notes'],
                ]
            );
        }
    }
}
