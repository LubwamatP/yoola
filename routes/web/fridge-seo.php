<?php
use Illuminate\Support\Facades\Route;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Fridge & Freezer SEO Landing Pages
|--------------------------------------------------------------------------
*/

// PILLAR: Fridge Prices Uganda
Route::get('/fridge-prices-uganda', function () {
    $products = Product::active()
        ->where(function($q) {
            $q->where('name', 'LIKE', '%fridge%')
              ->orWhere('name', 'LIKE', '%refrigerator%')
              ->orWhere('name', 'LIKE', '%freezer%');
        })
        ->orderBy('unit_price', 'asc')->limit(40)->get();
    return view('theme-views.pages.seo.fridge-listing', [
        'products' => $products,
        'pageTitle' => 'Fridge Prices in Uganda 2026 | Refrigerators & Freezers',
        'metaDescription' => 'Compare fridge prices in Uganda. Samsung, Hisense, CHiQ refrigerators from UGX 450,000. Free delivery Kampala.',
        'h1' => 'Fridge & Freezer Prices in Uganda',
    ]);
})->name('seo.fridge-prices');

// BY PRICE
$fridgePrices = [
    'fridges-under-500k-uganda' => ['max' => 500000, 'title' => 'Fridges Under 500K Uganda'],
    'fridges-under-1m-uganda' => ['max' => 1000000, 'title' => 'Fridges Under 1 Million Uganda'],
    'fridges-under-2m-uganda' => ['max' => 2000000, 'title' => 'Fridges Under 2 Million Uganda'],
    'fridges-under-3m-uganda' => ['max' => 3000000, 'title' => 'Fridges Under 3 Million Uganda'],
];
foreach ($fridgePrices as $slug => $config) {
    Route::get("/{$slug}", function () use ($config) {
        $products = Product::active()
            ->where(function($q) {
                $q->where('name', 'LIKE', '%fridge%')
                  ->orWhere('name', 'LIKE', '%refrigerator%');
            })
            ->where('unit_price', '<=', $config['max'])
            ->orderBy('unit_price', 'asc')->limit(30)->get();
        return view('theme-views.pages.seo.fridge-listing', [
            'products' => $products,
            'pageTitle' => $config['title'] . ' | Best Deals 2026',
            'metaDescription' => $config['title'] . '. Free delivery Kampala. Genuine fridges with warranty.',
            'h1' => $config['title'],
            'maxPrice' => $config['max'],
        ]);
    })->name("seo.{$slug}");
}

// BY TYPE
$fridgeTypes = [
    'refrigerators-uganda' => ['search' => '%refrigerator%', 'name' => 'Refrigerators', 'desc' => 'Double door refrigerators'],
    'deep-freezers-uganda' => ['search' => '%deep freezer%', 'name' => 'Deep Freezers', 'desc' => 'Chest & upright freezers'],
    'mini-fridges-uganda' => ['search' => '%mini%fridge%', 'name' => 'Mini Fridges', 'desc' => 'Compact refrigerators'],
    'chest-freezers-uganda' => ['search' => '%chest freezer%', 'name' => 'Chest Freezers', 'desc' => 'Large capacity freezers'],
    'beverage-coolers-uganda' => ['search' => '%beverage%cooler%', 'name' => 'Beverage Coolers', 'desc' => 'Display fridges'],
];
foreach ($fridgeTypes as $slug => $config) {
    Route::get("/{$slug}", function () use ($config) {
        $products = Product::active()
            ->where('name', 'LIKE', $config['search'])
            ->orderBy('unit_price', 'asc')->limit(30)->get();
        return view('theme-views.pages.seo.fridge-listing', [
            'products' => $products,
            'pageTitle' => $config['name'] . ' Prices in Uganda 2026',
            'metaDescription' => 'Buy ' . $config['name'] . ' in Uganda. ' . $config['desc'] . '. Best prices at Yoola.ug',
            'h1' => $config['name'] . ' Prices in Uganda',
            'fridgeType' => $config['name'],
        ]);
    })->name("seo.{$slug}");
}

// BY BRAND
$fridgeBrands = [
    'samsung-fridge-prices-uganda' => ['name' => 'Samsung', 'tagline' => 'Premium refrigerators'],
    'hisense-fridge-prices-uganda' => ['name' => 'Hisense', 'tagline' => 'Value for money'],
    'chiq-fridge-prices-uganda' => ['name' => 'CHiQ', 'tagline' => 'Affordable quality'],
    'adh-fridge-prices-uganda' => ['name' => 'ADH', 'tagline' => 'Budget fridges'],
    'lg-fridge-prices-uganda' => ['name' => 'LG', 'tagline' => 'Smart refrigerators'],
];
foreach ($fridgeBrands as $slug => $brand) {
    Route::get("/{$slug}", function () use ($brand) {
        $products = Product::active()
            ->where('name', 'LIKE', "%{$brand['name']}%")
            ->where(function($q) {
                $q->where('name', 'LIKE', '%fridge%')
                  ->orWhere('name', 'LIKE', '%refrigerator%')
                  ->orWhere('name', 'LIKE', '%freezer%');
            })
            ->orderBy('unit_price', 'asc')->limit(30)->get();
        return view('theme-views.pages.seo.fridge-listing', [
            'products' => $products,
            'pageTitle' => $brand['name'] . ' Fridge Prices Uganda 2026',
            'metaDescription' => 'Buy ' . $brand['name'] . ' fridges in Uganda. ' . $brand['tagline'] . '. Free delivery Kampala.',
            'h1' => $brand['name'] . ' Fridge Prices in Uganda',
            'brand' => $brand,
        ]);
    })->name("seo.{$slug}");
}

// BY SIZE (Litres)
$fridgeSizes = [
    'small-fridge-uganda' => ['min' => 0, 'max' => 150, 'name' => 'Small Fridges (Under 150L)'],
    'medium-fridge-uganda' => ['min' => 150, 'max' => 300, 'name' => 'Medium Fridges (150-300L)'],
    'large-fridge-uganda' => ['min' => 300, 'max' => 500, 'name' => 'Large Fridges (300-500L)'],
    'double-door-fridge-uganda' => ['search' => '%double door%', 'name' => 'Double Door Fridges'],
    'side-by-side-fridge-uganda' => ['search' => '%side by side%', 'name' => 'Side-by-Side Fridges'],
];
foreach ($fridgeSizes as $slug => $config) {
    Route::get("/{$slug}", function () use ($config) {
        $query = Product::active()->where(function($q) {
            $q->where('name', 'LIKE', '%fridge%')->orWhere('name', 'LIKE', '%refrigerator%');
        });
        if (isset($config['search'])) {
            $query->where('name', 'LIKE', $config['search']);
        }
        $products = $query->orderBy('unit_price', 'asc')->limit(30)->get();
        return view('theme-views.pages.seo.fridge-listing', [
            'products' => $products,
            'pageTitle' => $config['name'] . ' Prices Uganda 2026',
            'metaDescription' => 'Buy ' . $config['name'] . ' in Uganda. Best prices, free delivery.',
            'h1' => $config['name'] . ' Prices in Uganda',
        ]);
    })->name("seo.{$slug}");
}
