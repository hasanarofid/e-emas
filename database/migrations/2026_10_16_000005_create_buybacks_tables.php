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
        Schema::create('buybacks', function (Blueprint $table) {
            $table->id();
            $table->string('buyback_number')->unique(); // contoh: BBK-20261016-0001
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_nik')->nullable(); // KTP untuk verifikasi barang berharga
            
            $table->foreignId('original_sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('original_invoice_number')->nullable();
            
            $table->decimal('total_weight_original', 10, 3)->nullable(); // berat di nota lama
            $table->decimal('total_weight_actual', 10, 3); // berat nyata hasil timbang ulang
            $table->decimal('total_deduction', 15, 2)->default(0); // potongan susut, batu hilang, atau ongkos
            $table->decimal('grand_total', 15, 2)->default(0); // uang kas yang dibayarkan ke pelanggan
            
            $table->enum('payment_method', ['cash', 'transfer'])->default('cash');
            $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('buyback_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyback_id')->constrained('buybacks')->cascadeOnDelete();
            $table->string('item_name');
            $table->foreignId('gold_rate_id')->constrained('gold_rates')->restrictOnDelete();
            
            $table->decimal('weight_actual', 10, 3); // berat timbangan saat buyback
            $table->decimal('buy_price_per_gram', 15, 2); // harga beli per gram hari ini
            $table->decimal('deduction_amount', 15, 2)->default(0); // nilai potongan
            $table->decimal('total_amount', 15, 2); // (weight * buy_price) - deduction
            
            $table->string('condition_notes')->nullable(); // kondisi fisik (misal: penyok, patah, utuh)
            $table->enum('destination', ['stock_secondary', 'melt', 'repair'])->default('melt'); // barang dilebur atau dijual bekas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyback_items');
        Schema::dropIfExists('buybacks');
    }
};
