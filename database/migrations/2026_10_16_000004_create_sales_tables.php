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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // contoh: INV-EMAS-20261016-0001
            $table->string('customer_name'); // Nama pembeli untuk surat emas resmi
            $table->string('customer_phone')->nullable(); // Nomor WhatsApp untuk kirim struk digital
            $table->text('customer_address')->nullable();
            
            $table->decimal('total_weight', 10, 3)->default(0.000); // total berat gramasi emas dalam invoice
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            
            $table->enum('payment_method', ['cash', 'transfer', 'qris', 'debit', 'split'])->default('cash');
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('change_due', 15, 2)->default(0); // uang kembalian
            
            $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            
            // Snapshot data saat transaksi terjadi agar surat emas historis tetap akurat
            $table->string('item_name');
            $table->string('sku');
            $table->string('category_name');
            $table->string('gold_rate_name');
            $table->decimal('gross_weight', 10, 3);
            $table->decimal('stone_weight', 10, 3)->default(0.000);
            $table->decimal('net_weight', 10, 3);
            $table->decimal('gold_price_per_gram', 15, 2); // harga emas per gram saat transaksi
            $table->decimal('crafting_fee', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->string('image_snapshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
