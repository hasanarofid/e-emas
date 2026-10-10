<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Menampilkan katalog produk ornamen lengkap dengan filter kategori & pencarian.
     */
    public function index(Request $request): Response
    {
        $categorySlug = $request->query('kategori');
        $search = $request->query('q');

        $categories = Category::where('type', 'product')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $query = Product::where('status', 'published')
            ->with('category')
            ->orderBy('order');

        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('material', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(12)->withQueryString();

        return Inertia::render('Products/Index', [
            'categories' => $categories,
            'products' => $products,
            'currentCategory' => $categorySlug,
            'searchQuery' => $search,
        ]);
    }

    /**
     * Menampilkan detail spesifikasi teknis dan galeri produk.
     */
    public function show(string $slug): Response
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'published')
            ->with(['category', 'images'])
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'published')
            ->take(3)
            ->get();

        return Inertia::render('Products/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
