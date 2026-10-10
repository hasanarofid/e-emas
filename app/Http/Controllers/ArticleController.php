<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    /**
     * Menampilkan daftar artikel & tips arsitektur ornamen untuk SEO.
     */
    public function index(Request $request): Response
    {
        $categorySlug = $request->query('kategori');

        $categories = Category::where('type', 'post')
            ->where('is_active', true)
            ->get();

        $query = Post::where('status', 'published')
            ->with('category')
            ->latest();

        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $articles = $query->paginate(9)->withQueryString();

        return Inertia::render('Articles/Index', [
            'articles' => $articles,
            'categories' => $categories,
            'currentCategory' => $categorySlug,
        ]);
    }

    /**
     * Menampilkan isi lengkap artikel SEO.
     */
    public function show(string $slug): Response
    {
        $article = Post::where('slug', $slug)
            ->where('status', 'published')
            ->with('category')
            ->firstOrFail();

        $relatedArticles = Post::where('id', '!=', $article->id)
            ->where('status', 'published')
            ->latest()
            ->take(3)
            ->get();

        return Inertia::render('Articles/Show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
