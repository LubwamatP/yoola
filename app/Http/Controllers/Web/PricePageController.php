<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PricePage;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Programmatic SEO - Price Pages Controller
 * 
 * Handles "[Product] price in Uganda" pages for organic search traffic
 */
class PricePageController extends Controller
{
    /**
     * Display the price pages hub (all categories)
     */
    public function index(): View
    {
        $pricePages = PricePage::active()
            ->orderBy('priority', 'desc')
            ->orderBy('title')
            ->get()
            ->groupBy('product_type');

        return view('theme-views.price-pages.index', [
            'pricePages' => $pricePages,
        ]);
    }

    /**
     * Display a specific price page
     */
    public function show(string $slug): View
    {
        $pricePage = PricePage::where('slug', $slug)
            ->active()
            ->firstOrFail();

        $products = $pricePage->getProducts();
        $relatedPages = $pricePage->getRelatedPages();

        // Get price range
        $priceRange = $pricePage->price_range;

        return view('theme-views.price-pages.show', [
            'pricePage' => $pricePage,
            'products' => $products,
            'relatedPages' => $relatedPages,
            'priceRange' => $priceRange,
        ]);
    }

    /**
     * Generate sitemap for price pages
     */
    public function sitemap(): \Illuminate\Http\Response
    {
        $pricePages = PricePage::indexed()->get();

        $content = view('theme-views.price-pages.sitemap', [
            'pricePages' => $pricePages,
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
