<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Beban & Pengeluaran Operasional Toko Emas
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
            $table->string('category'); // Operasional, Listrik & Air, Konsumsi & Snack, Kemasan & Kotak Emas, Pajak & Retribusi, Marketing, Perlengkapan Tukang Emas, Lain-lain
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->enum('payment_method', ['tunai', 'transfer'])->default('tunai');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->string('recipient')->nullable();
            $table->string('receipt_photo')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tabel Buku Kas Toko (Arus Kas Masuk & Kas Keluar / Cash Book)
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->enum('type', ['in', 'out']);
            $table->string('category'); // modal_awal, penjualan_emas, buyback, pengeluaran, setoran_kasir, jasa_servis, pinjaman_gadai, tebus_gadai, tarik_tunai, lainnya
            $table->nullableMorphs('reference');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->string('description');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Tabel Utang Supplier & Pembayaran Kredit Emas (Outstanding Payables)
        Schema::create('debt_payables', function (Blueprint $table) {
            $table->id();
            $table->string('payable_code')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
            $table->decimal('total_debt', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2);
            $table->date('due_date');
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->date('last_payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_payables');
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('expenses');
    }
};
