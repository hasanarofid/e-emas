<?php

namespace Database\Seeders;

use App\Models\BuybackDeduction;
use App\Models\Category;
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

        // 2. Master Potongan Buyback Persis Sitokmas
        $kalung = Category::where('name', 'like', '%KALUNG%')->first();
        $cincin = Category::where('name', 'like', '%CINCIN%')->first();
        $rate23k = GoldRate::where('name', 'like', '%22K%')->orWhere('name', 'like', '%24K%')->first();

        $sitokmasRules = [
            ['condition_status' => 'Siap Jual', 'category_id' => null, 'gold_rate_id' => null, 'deduction_amount' => 200000, 'deduction_type' => 'nominal', 'deduct_by_weight' => false],
            ['condition_status' => 'Jual Baik', 'category_id' => null, 'gold_rate_id' => null, 'deduction_amount' => 100000, 'deduction_type' => 'nominal', 'deduct_by_weight' => false],
            ['condition_status' => 'Jual Ancur', 'category_id' => null, 'gold_rate_id' => null, 'deduction_amount' => 10, 'deduction_type' => 'percentage', 'deduct_by_weight' => false],
            ['condition_status' => 'Rusak Jual Ancur', 'category_id' => null, 'gold_rate_id' => null, 'deduction_amount' => 15, 'deduction_type' => 'percentage', 'deduct_by_weight' => false],
            ['condition_status' => 'Rusak Total', 'category_id' => null, 'gold_rate_id' => null, 'deduction_amount' => 20, 'deduction_type' => 'percentage', 'deduct_by_weight' => false],
            ['condition_status' => 'Tukar Ancur', 'category_id' => null, 'gold_rate_id' => $rate23k?->id, 'deduction_amount' => 10, 'deduction_type' => 'percentage', 'deduct_by_weight' => false],
            ['condition_status' => 'Tukar Baik', 'category_id' => $kalung?->id, 'gold_rate_id' => null, 'deduction_amount' => 5, 'deduction_type' => 'percentage', 'deduct_by_weight' => false],
            ['condition_status' => 'Rusak Jual Baik', 'category_id' => null, 'gold_rate_id' => null, 'deduction_amount' => 10, 'deduction_type' => 'percentage', 'deduct_by_weight' => false],
            ['condition_status' => 'Potong', 'category_id' => $kalung?->id, 'gold_rate_id' => null, 'deduction_amount' => 10, 'deduction_type' => 'percentage', 'deduct_by_weight' => false],
            ['condition_status' => 'KOMBINASI', 'category_id' => null, 'gold_rate_id' => null, 'deduction_amount' => 5, 'deduction_type' => 'percentage', 'deduct_by_weight' => false],
            ['condition_status' => 'Potong Cincin', 'category_id' => $cincin?->id, 'gold_rate_id' => null, 'deduction_amount' => 20, 'deduction_type' => 'percentage', 'deduct_by_weight' => false],
            ['condition_status' => 'BAIK', 'category_id' => null, 'gold_rate_id' => null, 'deduction_amount' => 0, 'deduction_type' => 'percentage', 'deduct_by_weight' => false],
        ];

        foreach ($sitokmasRules as $rule) {
            BuybackDeduction::updateOrCreate(
                [
                    'condition_status' => $rule['condition_status'],
                    'category_id' => $rule['category_id'],
                    'gold_rate_id' => $rule['gold_rate_id'],
                ],
                [
                    'deduction_amount' => $rule['deduction_amount'],
                    'deduction_type' => $rule['deduction_type'],
                    'deduct_by_weight' => $rule['deduct_by_weight'],
                    'deduction_percentage' => $rule['deduction_type'] === 'percentage' ? $rule['deduction_amount'] : 0,
                    'deduction_fee_per_gram' => $rule['deduction_type'] === 'nominal' ? $rule['deduction_amount'] : 0,
                    'crafting_fee_deducted' => true,
                ]
            );
        }
    }
}
