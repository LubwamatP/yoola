<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Smart TV SEO Landing Pages
|--------------------------------------------------------------------------
*/

// ============================================
// PILLAR PAGE: Smart TV Prices in Uganda
// ============================================
Route::get('/smart-tv-prices-uganda', function () {
    $products = Product::active()
        ->where('name', 'LIKE', '%tv%')
        ->whereNotIn('name', ['LIKE' => '%mount%', 'LIKE' => '%stand%', 'LIKE' => '%bracket%'])
        ->orderBy('unit_price', 'asc')
        ->limit(40)
        ->get();
    
    return view('theme-views.pages.seo.smart-tv-prices-uganda', [
        'products' => $products,
        'pageTitle' => 'Smart TV Prices in Uganda 2026 | Buy TVs Online',
        'metaDescription' => 'Compare Smart TV prices in Uganda. Samsung, Hisense, CHiQ, LG TVs from UGX 450,000. Free delivery in Kampala. Best deals at Yoola.ug',
        'h1' => 'Smart TV Prices in Uganda',
    ]);
})->name('seo.smart-tv-prices');

// ============================================
// BY PRICE
// ============================================
$priceRanges = [
    'tvs-under-500k-uganda' => ['max' => 500000, 'title' => 'TVs Under 500K in Uganda', 'desc' => 'Budget TVs under UGX 500,000'],
    'tvs-under-1m-uganda' => ['max' => 1000000, 'title' => 'TVs Under 1 Million in Uganda', 'desc' => 'Quality TVs under UGX 1,000,000'],
    'tvs-under-2m-uganda' => ['max' => 2000000, 'title' => 'TVs Under 2 Million in Uganda', 'desc' => 'Premium TVs under UGX 2,000,000'],
    'tvs-under-3m-uganda' => ['max' => 3000000, 'title' => 'TVs Under 3 Million in Uganda', 'desc' => 'Large screen TVs under UGX 3,000,000'],
];

foreach ($priceRanges as $slug => $config) {
    Route::get("/{$slug}", function () use ($config, $slug) {
        $products = Product::active()
            ->where('name', 'LIKE', '%tv%')
            ->where('name', 'NOT LIKE', '%mount%')
            ->where('name', 'NOT LIKE', '%stand%')
            ->where('unit_price', '<=', $config['max'])
            ->orderBy('unit_price', 'asc')
            ->limit(30)
            ->get();
        
        return view('theme-views.pages.seo.tv-price-range', [
            'products' => $products,
            'pageTitle' => $config['title'] . ' | Best Deals 2026',
            'metaDescription' => $config['desc'] . '. Free delivery Kampala. Genuine products with warranty at Yoola.ug',
            'h1' => $config['title'],
            'maxPrice' => $config['max'],
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// BY SIZE
// ============================================
$sizes = [32, 43, 50, 55, 65, 75];

foreach ($sizes as $size) {
    Route::get("/{$size}-inch-tv-uganda", function () use ($size) {
        $products = Product::active()
            ->where(function($q) use ($size) {
                $q->where('name', 'LIKE', "%{$size}\"%")
                  ->orWhere('name', 'LIKE', "%{$size} inch%")
                  ->orWhere('name', 'LIKE', "%{$size}inch%")
                  ->orWhere('name', 'LIKE', "%{$size}\"");
            })
            ->where('name', 'LIKE', '%tv%')
            ->orderBy('unit_price', 'asc')
            ->limit(30)
            ->get();
        
        return view('theme-views.pages.seo.tv-size', [
            'products' => $products,
            'pageTitle' => "{$size} Inch TV Price in Uganda | Buy {$size}\" TVs Online",
            'metaDescription' => "Best {$size} inch TV prices in Uganda. Samsung, Hisense, CHiQ {$size}\" Smart TVs. Free delivery Kampala. Shop at Yoola.ug",
            'h1' => "{$size} Inch TV Prices in Uganda",
            'size' => $size,
        ]);
    })->name("seo.{$size}-inch-tv");
}

// ============================================
// BY BRAND
// ============================================
$brands = [
    'chiq' => ['name' => 'CHiQ', 'tagline' => 'Affordable Quality'],
    'samsung' => ['name' => 'Samsung', 'tagline' => 'Premium Smart TVs'],
    'hisense' => ['name' => 'Hisense', 'tagline' => 'Value for Money'],
    'lg' => ['name' => 'LG', 'tagline' => 'OLED & Smart TVs'],
    'tcl' => ['name' => 'TCL', 'tagline' => 'Budget Smart TVs'],
];

foreach ($brands as $slug => $brand) {
    Route::get("/{$slug}-tv-prices-uganda", function () use ($slug, $brand) {
        $products = Product::active()
            ->where('name', 'LIKE', "%{$brand['name']}%")
            ->where('name', 'LIKE', '%tv%')
            ->orderBy('unit_price', 'asc')
            ->limit(30)
            ->get();
        
        return view('theme-views.pages.seo.tv-brand', [
            'products' => $products,
            'pageTitle' => "{$brand['name']} TV Prices in Uganda 2026 | Buy {$brand['name']} Smart TVs",
            'metaDescription' => "Buy {$brand['name']} TVs in Uganda. {$brand['tagline']}. Best prices, free delivery Kampala. All sizes available at Yoola.ug",
            'h1' => "{$brand['name']} TV Prices in Uganda",
            'brand' => $brand,
        ]);
    })->name("seo.{$slug}-tv");
}
