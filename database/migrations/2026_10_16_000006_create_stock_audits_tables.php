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
        Schema::create('stock_audits', function (Blueprint $table) {
            $table->id();
            $table->string('audit_code')->unique(); // contoh: AUD-20261016-01
            $table->foreignId('tray_id')->constrained('trays')->cascadeOnDelete();
            $table->foreignId('auditor_id')->constrained('users')->restrictOnDelete();
            
            $table->enum('status', ['draft', 'completed'])->default('draft');
            $table->integer('total_expected_items')->default(0);
            $table->integer('total_scanned_items')->default(0);
            $table->integer('total_missing_items')->default(0);
            $table->integer('total_mismatched_items')->default(0); // perhiasan dari baki lain yang nyasar
            
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_audit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_audit_id')->constrained('stock_audits')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('sku');
            $table->enum('scan_status', ['matched', 'missing', 'unexpected'])->default('missing');
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_audit_items');
        Schema::dropIfExists('stock_audits');
    }
};
