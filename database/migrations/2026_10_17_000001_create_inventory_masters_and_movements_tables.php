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
        // 1. Brands (Pabrikan / Merk Perhiasan: UBS, Antam, Lotus, Hala Gold, King Halim, dll.)
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Units (Satuan Barang: Gram, Pcs, Butir, Set, dll.)
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // contoh: Gram
            $table->string('symbol', 20)->unique(); // contoh: gr, pcs, btr
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Warehouses (Gudang / Lokasi Penyimpanan: Gudang Utama, Brankas Pusat, Display Toko Depan)
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // contoh: W-MAIN, W-SAFE-01
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('pic_name')->nullable(); // Penanggung Jawab
            $table->string('pic_phone')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Perluas relasi di tabel items
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('gold_rate_id')->constrained('brands')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->after('brand_id')->constrained('units')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->after('tray_id')->constrained('warehouses')->nullOnDelete();
        });

        // 5. Stock Movements (Keluar Masuk Barang non-penjualan: penerimaan baru, peleburan, servis keluar, transfer)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique(); // contoh: MOV-202610-0001
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->enum('type', ['in', 'out']); // in = masuk, out = keluar
            $table->enum('reason', [
                'purchase_supplier', // Pembelian dari supplier
                'manual_in',         // Input stok awal / penyesuaian masuk
                'buyback_in',        // Masuk dari buyback kasir
                'melted',            // Keluar untuk dilebur jadi emas batangan
                'repair_out',        // Keluar untuk servis/patri/cuci ke tukang
                'repair_in',         // Masuk kembali dari tukang servis
                'damaged_loss',      // Rusak / susut fisik
                'adjustment_out',    // Penyesuaian stok opname minus
                'other'
            ]);
            $table->decimal('weight', 10, 3); // berat gramasi yang bergerak
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');

        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['brand_id', 'unit_id', 'warehouse_id']);
        });

        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('units');
        Schema::dropIfExists('brands');
    }
};
