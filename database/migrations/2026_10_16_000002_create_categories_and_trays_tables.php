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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Cincin, Kalung, Gelang, Anting, Liontin, Logam Mulia
            $table->string('slug')->unique();
            $table->string('icon')->nullable(); // Lucide icon name, e.g. "Ring", "Gem"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('trays', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // contoh: BK-CNC-01, BK-KLG-02
            $table->string('name'); // contoh: Baki Cincin Dewasa A
            $table->string('showcase_location')->nullable(); // contoh: Etalase Utama 01
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('gold_rate_id')->nullable()->constrained('gold_rates')->nullOnDelete();
            $table->integer('capacity')->default(24); // kapasitas slot baki fisik
            $table->integer('total_items')->default(0);
            $table->decimal('total_weight', 10, 3)->default(0.000); // total berat gramasi fisik dalam baki
            $table->enum('status', ['active', 'archived', 'in_audit'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trays');
        Schema::dropIfExists('categories');
    }
};
