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
        // 1. Tabel Pelanggan (Customer & Member Emas)
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // contoh: P92601228379
            $table->string('name');
            $table->date('birth_date')->nullable();
            $table->string('identity_number', 50)->nullable(); // No KTP/NIK/SIM
            $table->string('phone', 50)->nullable();
            $table->string('telephone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('city')->default('Yogyakarta');
            $table->text('address')->nullable();
            $table->enum('level', ['pelanggan', 'agen', 'vip', 'marketer'])->default('pelanggan');
            $table->integer('points')->default(0); // saldo poin saat ini
            $table->integer('total_items_count')->default(0);
            $table->decimal('total_purchased_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. Hubungkan Customer ke Tabel Penjualan (Sales) dan tambahkan SoftDeletes
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('invoice_number')->constrained('customers')->nullOnDelete();
            $table->softDeletes()->after('updated_at');
        });

        // 3. Log Riwayat Mutasi Poin & Hadiah (Customer Point Logs)
        Schema::create('customer_point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->enum('type', ['earn', 'redeem', 'adjustment']); // earn = dapat poin dari belanja, redeem = tukar hadiah, adjustment = koreksi
            $table->integer('points'); // jumlah (+ atau -)
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('reward_name')->nullable(); // Nama hadiah (jika redeem)
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // kasir / staf yang memproses
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_point_logs');

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id', 'deleted_at']);
        });

        Schema::dropIfExists('customers');
    }
};
