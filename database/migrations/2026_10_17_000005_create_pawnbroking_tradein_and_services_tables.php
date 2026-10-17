<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Gadai, Tukar Tambah, Servis Perhiasan, dan Timbang Baki.
     */
    public function up(): void
    {
        // 1. Gadai Perhiasan Emas
        Schema::create('pawn_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('pawn_number')->unique(); // e.g. GD-202610-0001
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            
            // Detail barang jaminan emas
            $table->string('item_name'); // e.g. Kalung Rantai Italy
            $table->foreignId('gold_rate_id')->nullable()->constrained('gold_rates')->nullOnDelete();
            $table->decimal('gross_weight', 10, 3);
            $table->decimal('estimated_value', 15, 2); // Nilai taksiran pasar
            $table->decimal('loan_amount', 15, 2); // Jumlah pinjaman dicairkan
            $table->decimal('monthly_interest_rate', 5, 2)->default(2.00); // Bunga/jasa titip % per bulan
            $table->decimal('monthly_fee', 15, 2)->default(0); // Nilai nominal jasa titip per bulan
            
            // Tenor & Status
            $table->date('start_date');
            $table->date('due_date');
            $table->enum('status', ['active', 'redeemed', 'extended', 'auctioned'])->default('active');
            // active = masih berjalan, redeemed = ditebus lunas, extended = diperpanjang, auctioned = dilelang karena jatuh tempo
            
            $table->date('settled_date')->nullable();
            $table->decimal('settled_amount', 15, 2)->nullable();
            
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. Servis & Cuci Perhiasan
        Schema::create('jewelry_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_number')->unique(); // e.g. SRV-202610-0001
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            
            $table->string('item_name'); // e.g. Cincin Berlian Patah
            $table->decimal('weight_before', 10, 3)->default(0.000);
            $table->decimal('weight_after', 10, 3)->nullable();
            $table->string('service_type'); // Cuci Emas, Patri Sambung, Pasang Permata, Sepuh Chrome, Resize
            $table->text('damage_description')->nullable();
            
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->decimal('down_payment', 15, 2)->default(0);
            $table->decimal('remaining_cost', 15, 2)->default(0);
            
            $table->date('received_date');
            $table->date('completion_target_date')->nullable();
            $table->enum('status', ['received', 'in_progress', 'ready', 'picked_up', 'cancelled'])->default('received');
            
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 3. Log Timbang Baki Harian Kasir (Pagi & Sore)
        Schema::create('tray_scale_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tray_id')->constrained('trays')->cascadeOnDelete();
            $table->date('scale_date');
            $table->enum('session', ['morning', 'evening'])->default('morning'); // Timbang Pagi / Timbang Tutup Sore
            $table->integer('item_count_system');
            $table->decimal('weight_system', 10, 3);
            $table->integer('item_count_real');
            $table->decimal('weight_real', 10, 3);
            $table->decimal('difference_weight', 10, 3); // Selisih timbangan (minus = susut/hilang)
            $table->enum('status', ['matched', 'discrepancy'])->default('matched');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('tray_scale_logs');
        Schema::dropIfExists('jewelry_services');
        Schema::dropIfExists('pawn_transactions');
    }
};
