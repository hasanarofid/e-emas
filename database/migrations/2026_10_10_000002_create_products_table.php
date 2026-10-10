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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable();
            $table->string('material')->default('GRC (Glassfibre Reinforced Concrete)');
            $table->string('dimensions')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->json('specifications')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('price_range')->nullable(); // e.g. "Mulai Rp 180.000 / m²" or "Kustom Sesuai Volume"
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('published'); // draft, published, archived
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['is_featured', 'status']);
            $table->index('category_id');
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
};
