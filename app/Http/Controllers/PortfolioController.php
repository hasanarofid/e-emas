<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    /**
     * Menampilkan galeri proyek pemasangan ornamen arsitektur di lapangan.
     */
    public function index(Request $request): Response
    {
        $projectType = $request->query('tipe');

        $query = Portfolio::where('status', 'published')
            ->orderBy('order');

        if ($projectType) {
            $query->where('project_type', $projectType);
        }

        $portfolios = $query->paginate(9)->withQueryString();

        return Inertia::render('Portfolios/Index', [
            'portfolios' => $portfolios,
            'currentType' => $projectType,
        ]);
    }

    /**
     * Menampilkan detail proyek, dokumentasi foto, dan lingkup ornamen.
     */
    public function show(string $slug): Response
    {
        $portfolio = Portfolio::where('slug', $slug)
            ->where('status', 'published')
            ->with(['category', 'images'])
            ->firstOrFail();

        $recentPortfolios = Portfolio::where('id', '!=', $portfolio->id)
            ->where('status', 'published')
            ->take(3)
            ->get();

        return Inertia::render('Portfolios/Show', [
            'portfolio' => $portfolio,
            'recentPortfolios' => $recentPortfolios,
        ]);
    }
}
