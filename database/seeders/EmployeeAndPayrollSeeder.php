<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeAndPayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@emas.com')->first();
        $kasirUser = User::where('email', 'kasir@emas.com')->first();
        $bankAccount = BankAccount::first();

        // 1. Siti Rahmawati (Kepala Toko)
        $emp1 = Employee::updateOrCreate(
            ['employee_code' => 'PEG-001'],
            [
                'user_id' => $adminUser ? $adminUser->id : null,
                'name' => 'Siti Rahmawati, S.E.',
                'id_card_number' => '3201124508920001',
                'phone' => '081298765432',
                'email' => 'siti.rahma@emas.com',
                'address' => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
                'position' => 'Kepala Toko',
                'status' => 'tetap',
                'join_date' => '2023-01-15',
                'base_salary' => 6500000,
                'allowance' => 1000000,
                'commission_rate' => 0.20,
                'bank_name' => 'BCA',
                'bank_account_number' => '8730129841',
                'bank_account_holder' => 'Siti Rahmawati',
                'is_active' => true,
                'notes' => 'Penanggung jawab operasional harian toko emas.',
            ]
        );

        // 2. Budi Santoso (Kasir POS Utama)
        $emp2 = Employee::updateOrCreate(
            ['employee_code' => 'PEG-002'],
            [
                'user_id' => $kasirUser ? $kasirUser->id : null,
                'name' => 'Budi Santoso',
                'id_card_number' => '3201124508920002',
                'phone' => '081387654321',
                'email' => 'kasir@emas.com',
                'address' => 'Jl. Cempaka Putih Timur No. 45, Jakarta Pusat',
                'position' => 'Kasir',
                'status' => 'tetap',
                'join_date' => '2023-06-01',
                'base_salary' => 4500000,
                'allowance' => 600000,
                'commission_rate' => 0.50, // 0.5% komisi omset penjualan
                'bank_name' => 'Mandiri',
                'bank_account_number' => '1370019283741',
                'bank_account_holder' => 'Budi Santoso',
                'is_active' => true,
                'notes' => 'Kasir utama loket kasir POS 1.',
            ]
        );

        // 3. Dewi Lestari (Sales Counter & Jewellery Advisor)
        $emp3 = Employee::updateOrCreate(
            ['employee_code' => 'PEG-003'],
            [
                'user_id' => null,
                'name' => 'Dewi Lestari',
                'id_card_number' => '3201124508920003',
                'phone' => '082199887766',
                'email' => 'dewi.lestari@gmail.com',
                'address' => 'Jl. Tebet Raya No. 89, Jakarta Selatan',
                'position' => 'Sales & Counter Jewelry',
                'status' => 'tetap',
                'join_date' => '2024-02-10',
                'base_salary' => 3800000,
                'allowance' => 500000,
                'commission_rate' => 0.75, // 0.75% komisi perhiasan
                'bank_name' => 'BCA',
                'bank_account_number' => '6041289301',
                'bank_account_holder' => 'Dewi Lestari',
                'is_active' => true,
                'notes' => 'Spesialis counter cincin nikah & kalung berlian.',
            ]
        );

        // 4. Joko Prayitno (Tukang Servis & Cuci Emas)
        $emp4 = Employee::updateOrCreate(
            ['employee_code' => 'PEG-004'],
            [
                'user_id' => null,
                'name' => 'Joko Prayitno',
                'id_card_number' => '3201124508920004',
                'phone' => '085711223344',
                'email' => 'joko.prayitno@gmail.com',
                'address' => 'Jl. Pulo Gadung No. 23, Jakarta Timur',
                'position' => 'Tukang Servis & Cuci Emas',
                'status' => 'kontrak',
                'join_date' => '2024-05-01',
                'base_salary' => 4200000,
                'allowance' => 500000,
                'commission_rate' => 0.00,
                'bank_name' => 'BRI',
                'bank_account_number' => '020601009823501',
                'bank_account_holder' => 'Joko Prayitno',
                'is_active' => true,
                'notes' => 'Pengrajin reparasi, lebur, patri, dan poles emas.',
            ]
        );

        // Contoh Slip Penggajian Bulan September & Oktober
        Payroll::updateOrCreate(
            ['payroll_code' => 'PAY-202609-0001'],
            [
                'employee_id' => $emp2->id,
                'period_month' => 9,
                'period_year' => 2026,
                'payment_date' => '2026-09-30',
                'base_salary' => 4500000,
                'allowance' => 600000,
                'commission_amount' => 450000,
                'bonus' => 200000,
                'deductions' => 0,
                'net_salary' => 5750000,
                'payment_method' => 'transfer',
                'bank_account_id' => $bankAccount ? $bankAccount->id : null,
                'status' => 'paid',
                'notes' => 'Gaji periode September 2026 + komisi penjualan kasir.',
                'created_by' => $adminUser ? $adminUser->id : null,
            ]
        );

        Payroll::updateOrCreate(
            ['payroll_code' => 'PAY-202609-0002'],
            [
                'employee_id' => $emp1->id,
                'period_month' => 9,
                'period_year' => 2026,
                'payment_date' => '2026-09-30',
                'base_salary' => 6500000,
                'allowance' => 1000000,
                'commission_amount' => 250000,
                'bonus' => 500000,
                'deductions' => 0,
                'net_salary' => 8250000,
                'payment_method' => 'transfer',
                'bank_account_id' => $bankAccount ? $bankAccount->id : null,
                'status' => 'paid',
                'notes' => 'Gaji Kepala Toko September 2026.',
                'created_by' => $adminUser ? $adminUser->id : null,
            ]
        );
    }
}
