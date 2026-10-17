<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\CashTransaction;
use App\Models\DebtPayable;
use App\Models\Expense;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@emas.com')->first();
        $bca = BankAccount::where('bank_name', 'BCA')->first();
        $mandiri = BankAccount::where('bank_name', 'Mandiri')->first();
        $suppliers = Supplier::all();

        // 1. Modal Awal Toko Emas & Saldo Awal (Cash Transactions)
        CashTransaction::updateOrCreate(
            ['transaction_number' => 'CSH-INIT-001'],
            [
                'type' => 'in',
                'category' => 'modal_awal',
                'bank_account_id' => null, // Kasir Tunai
                'amount' => 15000000,
                'transaction_date' => '2026-10-01',
                'description' => 'Modal Awal Kas Tunai Laci Kasir POS Toko Emas',
                'notes' => 'Uang pecahan operasional kembalian kasir.',
                'created_by' => $adminUser ? $adminUser->id : null,
            ]
        );

        if ($bca) {
            CashTransaction::updateOrCreate(
                ['transaction_number' => 'CSH-INIT-002'],
                [
                    'type' => 'in',
                    'category' => 'modal_awal',
                    'bank_account_id' => $bca->id,
                    'amount' => 75000000,
                    'transaction_date' => '2026-10-01',
                    'description' => 'Saldo Modal Rekening Giro Operasional BCA Toko',
                    'notes' => 'Dana cadangan pembelian emas dan pembayaran supplier pabrik.',
                    'created_by' => $adminUser ? $adminUser->id : null,
                ]
            );
        }

        // 2. Pengeluaran Operasional Toko (Expenses)
        $exp1 = Expense::updateOrCreate(
            ['expense_number' => 'EXP-202610-0001'],
            [
                'category' => 'Kemasan & Kotak Emas/Pouch',
                'title' => 'Pengadaan 300 Pcs Box Beludru Cincin & Pouch Emas Eksklusif',
                'amount' => 1850000,
                'expense_date' => '2026-10-05',
                'payment_method' => 'transfer',
                'bank_account_id' => $bca ? $bca->id : null,
                'recipient' => 'CV. Gemilang Box Packaging',
                'notes' => 'Box merah beludru berlogo Toko Mas Permata Abadi.',
                'created_by' => $adminUser ? $adminUser->id : null,
            ]
        );

        $exp2 = Expense::updateOrCreate(
            ['expense_number' => 'EXP-202610-0002'],
            [
                'category' => 'Listrik, Air & Internet',
                'title' => 'Tagihan Listrik PLN Toko & Showcase Lampu Sorot Emas',
                'amount' => 1250000,
                'expense_date' => '2026-10-10',
                'payment_method' => 'transfer',
                'bank_account_id' => $bca ? $bca->id : null,
                'recipient' => 'PLN Persero',
                'notes' => 'Daya 5500VA penerangan etalase kaca perhiasan.',
                'created_by' => $adminUser ? $adminUser->id : null,
            ]
        );

        $exp3 = Expense::updateOrCreate(
            ['expense_number' => 'EXP-202610-0003'],
            [
                'category' => 'Alat & Bahan Tukang Servis Emas',
                'title' => 'Pembelian Air Keras Poles Emas, Bor Mini & Batu Gosok',
                'amount' => 450000,
                'expense_date' => '2026-10-14',
                'payment_method' => 'tunai',
                'bank_account_id' => null,
                'recipient' => 'Toko Alat Emas Senen',
                'notes' => 'Perlengkapan reparasi dan patri tukang perhiasan.',
                'created_by' => $adminUser ? $adminUser->id : null,
            ]
        );

        // Catat pengeluaran di buku kas
        foreach ([$exp1, $exp2, $exp3] as $exp) {
            CashTransaction::updateOrCreate(
                ['reference_type' => Expense::class, 'reference_id' => $exp->id],
                [
                    'transaction_number' => 'CSH-' . $exp->expense_number,
                    'type' => 'out',
                    'category' => 'pengeluaran',
                    'bank_account_id' => $exp->payment_method === 'transfer' ? $exp->bank_account_id : null,
                    'amount' => $exp->amount,
                    'transaction_date' => $exp->expense_date,
                    'description' => "Pengeluaran [{$exp->category}]: {$exp->title}",
                    'notes' => $exp->notes,
                    'created_by' => $adminUser ? $adminUser->id : null,
                ]
            );
        }

        // 3. Outstanding Utang Supplier Emas (Debt Payables)
        if ($suppliers->isNotEmpty()) {
            $sup1 = $suppliers->first();
            DebtPayable::updateOrCreate(
                ['payable_code' => 'UTG-202610-0001'],
                [
                    'supplier_id' => $sup1->id,
                    'total_debt' => 45000000,
                    'paid_amount' => 20000000,
                    'remaining_amount' => 25000000,
                    'due_date' => '2026-10-28',
                    'status' => 'partial',
                    'last_payment_date' => '2026-10-12',
                    'notes' => 'Faktur pengadaan kalung & gelang emas kadar 17K.',
                ]
            );

            if ($suppliers->count() > 1) {
                $sup2 = $suppliers->skip(1)->first();
                DebtPayable::updateOrCreate(
                    ['payable_code' => 'UTG-202610-0002'],
                    [
                        'supplier_id' => $sup2->id,
                        'total_debt' => 28000000,
                        'paid_amount' => 0,
                        'remaining_amount' => 28000000,
                        'due_date' => '2026-11-05',
                        'status' => 'unpaid',
                        'last_payment_date' => null,
                        'notes' => 'Faktur konsinyasi cincin kawin emas putih 750.',
                    ]
                );
            }
        }
    }
}
