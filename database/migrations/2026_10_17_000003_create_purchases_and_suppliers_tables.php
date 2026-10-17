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
        // 1. Tabel Supplier / Pabrikan Emas
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // contoh: SUP-ANTAM, SUP-UBS, SUP-LOTUS
            $table->string('name'); // Nama kontak / PIC
            $table->string('company_name')->nullable(); // Nama PT / Pabrik
            $table->string('phone', 50)->nullable();
            $table->string('telephone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. Tabel Faktur Pembelian Supplier (Purchases)
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // contoh: PB-202610-0001
            $table->string('supplier_invoice_number')->nullable(); // No faktur dari pabrik/supplier
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->date('invoice_date');
            $table->date('due_date')->nullable(); // Tanggal jatuh tempo utang
            
            $table->integer('total_items')->default(0);
            $table->decimal('total_weight', 10, 3)->default(0.000); // total gramasi emas
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            
            $table->enum('payment_status', ['paid', 'partial', 'unpaid'])->default('paid');
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('remaining_balance', 15, 2)->default(0);
            $table->enum('payment_method', ['cash', 'transfer', 'tempo'])->default('cash');
            
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 3. Tabel Detail Item Faktur Pembelian (Purchase Items)
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('gold_rate_id')->nullable()->constrained('gold_rates')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('item_name');
            $table->decimal('weight', 10, 3); // berat gramasi perhiasan
            $table->decimal('price_per_gram', 15, 2);
            $table->decimal('crafting_fee', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->foreignId('created_item_id')->nullable()->constrained('items')->nullOnDelete(); // jika langsung digenerate ke stok inventaris
            $table->timestamps();
        });

        // 4. Tabel Master Potongan Buyback Per Kadar Emas
        Schema::create('buyback_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gold_rate_id')->unique()->constrained('gold_rates')->cascadeOnDelete();
            $table->decimal('deduction_percentage', 5, 2)->default(0.00); // persentase potongan harga pasar (misal: 2%, 5%)
            $table->decimal('deduction_fee_per_gram', 15, 2)->default(0.00); // potongan susut fisik per gram (misal: Rp 15.000/gr)
            $table->boolean('crafting_fee_deducted')->default(true); // ongkos pembuatan hangus
            $table->text('condition_notes')->nullable(); // catatan syarat buyback resmi
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyback_deductions');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('suppliers');
    }
};
