<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyesuaikan skema tabel buyback_deductions persis dengan standar Sitokmas
     */
    public function up(): void
    {
        Schema::table('buyback_deductions', function (Blueprint $table) {
            $table->dropForeign(['gold_rate_id']);
            $table->dropUnique(['gold_rate_id']);
        });

        Schema::table('buyback_deductions', function (Blueprint $table) {
            $table->unsignedBigInteger('gold_rate_id')->nullable()->change();
            $table->foreign('gold_rate_id')->references('id')->on('gold_rates')->nullOnDelete();

            $table->string('condition_status')->default('Jual Baik')->after('id');
            $table->foreignId('category_id')->nullable()->after('condition_status')->constrained('categories')->nullOnDelete();
            $table->decimal('deduction_amount', 15, 2)->default(0)->after('gold_rate_id');
            $table->enum('deduction_type', ['nominal', 'percentage'])->default('percentage')->after('deduction_amount');
            $table->boolean('deduct_by_weight')->default(false)->after('deduction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buyback_deductions', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['condition_status', 'category_id', 'deduction_amount', 'deduction_type', 'deduct_by_weight']);
        });
    }
};
