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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('type')->default('product')->after('slug'); // product, portfolio, post
            $table->text('description')->nullable()->after('type');
            $table->string('image')->nullable()->after('description');
            $table->integer('order')->default(0)->after('image');
            $table->boolean('is_active')->default(true)->after('order');
            
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['type', 'is_active']);
            $table->dropColumn(['type', 'description', 'image', 'order', 'is_active']);
        });
    }
};
