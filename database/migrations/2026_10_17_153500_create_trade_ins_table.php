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
        Schema::create('trade_ins', function (Blueprint $table) {
            $table->id();
            $table->string('trade_in_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            
            // Emas Lama yang Diserahkan Pelanggan
            $table->string('old_item_name');
            $table->foreignId('old_gold_rate_id')->nullable()->constrained('gold_rates')->nullOnDelete();
            $table->decimal('old_weight', 10, 3)->default(0);
            $table->decimal('old_estimated_price', 15, 2)->default(0);

            // Emas Baru yang Diambil dari Etalase Toko
            $table->foreignId('new_item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->string('new_item_name');
            $table->decimal('new_weight', 10, 3)->default(0);
            $table->decimal('new_price', 15, 2)->default(0);

            // Perhitungan Selisih Pembayaran
            $table->decimal('difference_amount', 15, 2)->default(0); // Positif: pelanggan bayar toko, Negatif: toko kembalikan uang
            $table->enum('payment_method', ['cash', 'transfer', 'qris'])->default('cash');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('change_due', 15, 2)->default(0);

            $table->date('transaction_date');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_ins');
    }
};
