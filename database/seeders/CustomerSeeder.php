<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerPointLog;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'code' => 'P92604281558',
                'name' => 'Badawi Bogor',
                'identity_number' => '3201012304850001',
                'birth_date' => '1985-04-23',
                'phone' => '081234954995',
                'email' => 'badawibgr123@gmail.com',
                'city' => 'Bogor',
                'address' => 'Jl. Pajajaran No. 45, Baranangsiang, Bogor Timur',
                'level' => 'pelanggan',
                'points' => 260,
                'total_items_count' => 2,
                'total_purchased_amount' => 266710200,
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
                'level' => 'pelanggan',
                'points' => 450,
                'total_items_count' => 37,
                'total_purchased_amount' => 445199304,
            ],
            [
                'code' => 'P92603139436',
                'name' => 'Amelia Permatasari',
                'identity_number' => '3471015508920002',
                'birth_date' => '1992-08-15',
                'phone' => '08888812345',
                'email' => 'amelia.gold@gmail.com',
                'city' => 'Yogyakarta',
                'address' => 'Jl. Kaliurang KM 5 No. 8, Depok, Sleman',
                'level' => 'agen',
                'points' => 75,
                'total_items_count' => 1,
                'total_purchased_amount' => 7300000,
            ],
            [
                'code' => 'P92607024930',
                'name' => 'Budi Santoso',
                'identity_number' => '3210021005880004',
                'birth_date' => '1988-05-10',
                'phone' => '08119516880',
                'telephone' => '02338291698',
                'email' => 'budi.santoso@gmail.com',
                'city' => 'Majalengka',
                'address' => 'Jl. KH Abdul Halim No. 120, Majalengka Kulon',
                'level' => 'pelanggan',
                'points' => 25,
                'total_items_count' => 1,
                'total_purchased_amount' => 2700000,
            ],
            [
                'code' => 'P92607212431',
                'name' => 'Contoh Musterman',
                'identity_number' => '3671042001950005',
                'birth_date' => '1995-01-20',
                'phone' => '0823888999',
                'email' => 'contoh.musterman@sitokmas.id',
                'city' => 'Tangerang',
                'address' => 'Jl. Daan Mogot KM 19, Batuceper',
                'level' => 'pelanggan',
                'points' => 12,
                'total_items_count' => 2,
                'total_purchased_amount' => 1221567,
            ],
            [
                'code' => 'P92603292361',
                'name' => 'Darmi Handayani',
                'identity_number' => '3311034502800001',
                'birth_date' => '1980-02-15',
                'phone' => '081237496991',
                'email' => 'darmi.h@yahoo.com',
                'city' => 'Sukoharjo',
                'address' => 'Desa Taro, Sukoharjo, Jawa Tengah',
                'level' => 'pelanggan',
                'points' => 70,
                'total_items_count' => 1,
                'total_purchased_amount' => 7200000,
            ],
        ];

        foreach ($customers as $c) {
            $customer = Customer::firstOrCreate(['code' => $c['code']], $c);

            // Tambahkan contoh riwayat poin awal
            if ($customer->pointLogs()->count() === 0) {
                CustomerPointLog::create([
                    'customer_id' => $customer->id,
                    'type' => 'earn',
                    'points' => $customer->points,
                    'notes' => 'Reward poin akumulasi transaksi perhiasan emas',
                ]);
            }
        }
    }
}
