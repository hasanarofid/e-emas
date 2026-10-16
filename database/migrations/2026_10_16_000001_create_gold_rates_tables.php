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
        Schema::create('gold_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // contoh: Emas 24K (99.9%), Emas 17K (700)
            $table->string('code')->unique(); // contoh: 24K, 22K, 17K, 16K, 375
            $table->decimal('carat', 5, 2)->default(24.00); // 24.00, 17.00
            $table->decimal('percentage', 5, 2)->default(99.90); // 99.90%, 70.00%
            $table->decimal('buy_price_per_gram', 15, 2)->default(0); // Harga beli kembali / buyback dari customer
            $table->decimal('sell_price_per_gram', 15, 2)->default(0); // Harga jual dasar perhiasan per gram
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('gold_rate_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gold_rate_id')->constrained('gold_rates')->cascadeOnDelete();
            $table->decimal('buy_price_per_gram', 15, 2);
            $table->decimal('sell_price_per_gram', 15, 2);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_rate_histories');
        Schema::dropIfExists('gold_rates');
    }
};
