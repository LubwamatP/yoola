<?php
use Illuminate\Support\Facades\Route;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| TCL TV SEO Landing Pages
|--------------------------------------------------------------------------
*/

// PILLAR: TCL TV Prices Uganda
Route::get('/tcl-tv-prices-uganda', function () {
    $products = Product::active()
        ->where('name', 'LIKE', '%tcl%')
        ->where('name', 'LIKE', '%tv%')
        ->orderBy('unit_price', 'asc')
        ->limit(30)
        ->get();

    return view('theme-views.pages.seo.tv-listing', [
        'products' => $products,
        'pageTitle' => 'TCL TV Prices Uganda 2026 | Smart Android TVs',
        'metaDescription' => 'Buy TCL Smart TVs in Uganda. Android TV, 4K UHD, Google TV. 32, 43, 55 inch. Best prices with warranty. Free delivery Kampala. Yoola.ug',
        'h1' => 'TCL TV Prices in Uganda',
    ]);
})->name('seo.tcl-tv-prices');

// BY PRICE
$tclPrices = [
    'tcl-tvs-under-500k-uganda' => ['max' => 500000, 'title' => 'TCL TVs Under 500K Uganda', 'desc' => 'Budget TCL Smart TVs under UGX 500,000'],
    'tcl-tvs-under-1m-uganda' => ['max' => 1000000, 'title' => 'TCL TVs Under 1 Million Uganda', 'desc' => 'Mid-range TCL Android TVs under UGX 1,000,000'],
    'tcl-tvs-under-2m-uganda' => ['max' => 2000000, 'title' => 'TCL TVs Under 2 Million Uganda', 'desc' => 'Large screen TCL 4K TVs under UGX 2,000,000'],
];
foreach ($tclPrices as $slug => $config) {
    Route::get("/{$slug}", function () use ($config) {
        $products = Product::active()
            ->where('name', 'LIKE', '%tcl%')
            ->where('name', 'LIKE', '%tv%')
            ->where('unit_price', '<=', $config['max'])
            ->orderBy('unit_price', 'asc')
            ->limit(30)
            ->get();

        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products,
            'pageTitle' => $config['title'] . ' | Best Deals 2026',
            'metaDescription' => $config['desc'] . '. Free delivery Kampala. Genuine TCL products with warranty at Yoola.ug',
            'h1' => $config['title'],
            'maxPrice' => $config['max'],
        ]);
    })->name("seo.{$slug}");
}

// BY SIZE
$tclSizes = [32, 43, 55];
foreach ($tclSizes as $size) {
    Route::get("/tcl-{$size}-inch-tv-uganda", function () use ($size) {
        $products = Product::active()
            ->where('name', 'LIKE', '%tcl%')
            ->where(function($q) use ($size) {
                $q->where('name', 'LIKE', "%{$size}\"%")
                  ->orWhere('name', 'LIKE', "%{$size} inch%")
                  ->orWhere('name', 'LIKE', "%{$size}inch%");
            })
            ->where('name', 'LIKE', '%tv%')
            ->orderBy('unit_price', 'asc')
            ->limit(24)
            ->get();

        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products,
            'pageTitle' => "TCL {$size} Inch TV Price Uganda 2026 | Buy Online",
            'metaDescription' => "Buy TCL {$size} inch Smart Android TV in Uganda. 4K UHD, Google TV. Best price, genuine warranty. Free delivery Kampala.",
            'h1' => "TCL {$size} Inch TV Price in Uganda",
        ]);
    })->name("seo.tcl-{$size}-inch");
}

// MODEL-SPECIFIC
$tclModels = [
    'tcl-p635-price-uganda' => ['search' => '%tcl%p635%', 'name' => 'TCL P635'],
    'tcl-c645-price-uganda' => ['search' => '%tcl%c645%', 'name' => 'TCL C645 QLED'],
    'tcl-c745-price-uganda' => ['search' => '%tcl%c745%', 'name' => 'TCL C745'],
];
foreach ($tclModels as $slug => $m) {
    Route::get("/{$slug}", function () use ($m) {
        $products = Product::active()
            ->where('name', 'LIKE', $m['search'])
            ->orderBy('unit_price')
            ->limit(16)
            ->get();

        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products,
            'h1' => "{$m['name']} Price in Uganda",
            'pageTitle' => "{$m['name']} Price Uganda 2026 | Buy Online",
            'metaDescription' => "Buy {$m['name']} in Uganda. Best price, genuine TCL product with warranty. Free delivery at Yoola.ug",
        ]);
    })->name("seo.{$slug}");
}
