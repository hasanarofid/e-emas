<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'material',
        'dimensions',
        'short_description',
        'description',
        'specifications',
        'thumbnail',
        'price_range',
        'is_featured',
        'status',
        'order',
    ];

    protected $casts = [
        'specifications' => 'array',
        'is_featured' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope query untuk produk terbit.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope query untuk featured product di homepage.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->where('status', 'published')->orderBy('order');
    }

    /**
     * Relasi ke Kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke Galeri Gambar Produk.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }
}
