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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique(); // Kode barcode perhiasan unik (contoh: JWL-202610-001)
            $table->string('name'); // contoh: Cincin Solitaire Rose Gold Permata Zircon
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('gold_rate_id')->constrained('gold_rates')->restrictOnDelete();
            $table->foreignId('tray_id')->nullable()->constrained('trays')->nullOnDelete();
            
            // Presisi gramasi toko emas (3 desimal: 0.001 gram)
            $table->decimal('gross_weight', 10, 3); // berat kotor timbangan
            $table->decimal('stone_weight', 10, 3)->default(0.000); // berat permata / batu perhiasan
            $table->decimal('net_weight', 10, 3); // berat bersih emas (gross - stone)
            
            $table->decimal('crafting_fee', 15, 2)->default(0); // ongkos bikin / pembuatan
            $table->decimal('total_price', 15, 2)->default(0); // referensi harga jual saat input
            $table->string('image_url')->nullable(); // foto perhiasan untuk surat emas
            
            $table->enum('status', ['available', 'sold', 'buyback', 'repair', 'melted'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('tray_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('from_tray_id')->nullable()->constrained('trays')->nullOnDelete();
            $table->foreignId('to_tray_id')->constrained('trays')->cascadeOnDelete();
            $table->foreignId('moved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tray_movements');
        Schema::dropIfExists('items');
    }
};
