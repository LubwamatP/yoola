<?php
// Tools routes - Budget Planner, TV Size Calculator

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BudgetPlannerController;

// TV Size Calculator
Route::get('/tv-size-calculator', function() {
    return view('theme-views.tv-size-calculator');
})->name('tv-size-calculator');

// Budget Planner Routes
Route::prefix('budget-planner')->name('budget-planner.')->group(function() {
    Route::get('/', [BudgetPlannerController::class, 'index'])->name('index');
    Route::get('/recommendations', [BudgetPlannerController::class, 'getRecommendations'])->name('recommendations');
    Route::post('/recommendations', [BudgetPlannerController::class, 'getRecommendations'])->name('recommendations.post');
});

// Backward compatibility
Route::get('/budget-planner', [BudgetPlannerController::class, 'index'])->name('budget-planner');

// Yoola vs Jumia comparison page (SEO)
Route::get('/yoola-vs-jumia', function() {
    return view('theme-views.pages.yoola-vs-jumia');
})->name('yoola-vs-jumia');

Route::get('/compare/yoola-jumia', function() {
    return redirect('/yoola-vs-jumia', 301);
});


// Product Bundles (SEO landing)
Route::get('/bundles', function() {
    return view('theme-views.bundles.index');
})->name('bundles.index');

Route::get('/bundles/{slug}', function($slug) {
    // TODO: Fetch bundle by slug
    return view('theme-views.bundles.show', compact('slug'));
})->name('bundles.show');


// SEO Price Pages
# Route::get('/prices/{slug}', function($slug) {
#     $page = \DB::table('price_pages')->where('slug', $slug)->where('is_active', 1)->first();
#     if (!$page) {
#         abort(404);
#     }
#     
#     // Get products based on product_type
#     $products = \App\Models\Product::where('status', 1);
#     
#     // Filter by product type keyword
#     if ($page->product_type) {
#         $keywords = [
#             'tv' => ['TV', 'Television', 'Smart TV'],
#             'fridge' => ['Fridge', 'Refrigerator', 'Freezer'],
#             'freezer' => ['Freezer', 'Deep Freezer', 'Chest Freezer'],
#             'ac' => ['Air Conditioner', 'AC', 'Aircon'],
#             'washing' => ['Washing Machine', 'Washer'],
#         ];
#         
#         $searchTerms = $keywords[$page->product_type] ?? [$page->product_type];
#         $products = $products->where(function($q) use ($searchTerms) {
#             foreach ($searchTerms as $term) {
#                 $q->orWhere('name', 'LIKE', "%{$term}%");
#             }
#         });
#     }
#     
#     // Filter by brand if in slug
#     if (str_contains($slug, 'samsung')) {
#         $products = $products->where('name', 'LIKE', '%Samsung%');
#     } elseif (str_contains($slug, 'hisense')) {
#         $products = $products->where('name', 'LIKE', '%Hisense%');
#     }
#     
#     // Filter by size if in slug
#     if (str_contains($slug, '32-inch')) {
#         $products = $products->where('name', 'LIKE', '%32%');
#     } elseif (str_contains($slug, '43-inch')) {
#         $products = $products->where('name', 'LIKE', '%43%');
#     } elseif (str_contains($slug, '55-inch')) {
#         $products = $products->where('name', 'LIKE', '%55%');
#     }
#     
#     $products = $products->orderBy('unit_price', 'asc')->take(20)->get();
#     
#     return view('theme-views.pages.price-page', compact('page', 'products'));
# })->name('price.page');
# 

// Samsung Hub
Route::get('/samsung', function() {
    $washingMachines = \App\Models\Product::active()
        ->where('name', 'LIKE', '%Samsung%')
        ->where(function($q) {
            $q->where('name', 'LIKE', '%washing%')
              ->orWhere('name', 'LIKE', '%washer%')
              ->orWhere('name', 'LIKE', '%twin tub%');
        })
        ->orderBy('unit_price', 'asc')
        ->take(6)
        ->get();
    
    $refrigerators = \App\Models\Product::active()
        ->where('name', 'LIKE', '%Samsung%')
        ->where(function($q) {
            $q->where('name', 'LIKE', '%fridge%')
              ->orWhere('name', 'LIKE', '%refrigerator%')
              ->orWhere('name', 'LIKE', '%freezer%');
        })
        ->orderBy('unit_price', 'asc')
        ->take(6)
        ->get();
    
    $tvs = \App\Models\Product::active()
        ->where('name', 'LIKE', '%Samsung%')
        ->where(function($q) {
            $q->where('name', 'LIKE', '%TV%')
              ->orWhere('name', 'LIKE', '%QLED%')
              ->orWhere('name', 'LIKE', '%UHD%');
        })
        ->orderBy('unit_price', 'asc')
        ->take(6)
        ->get();
    
    $acs = \App\Models\Product::active()
        ->where('name', 'LIKE', '%Samsung%')
        ->where(function($q) {
            $q->where('name', 'LIKE', '%air conditioner%')
              ->orWhere('name', 'LIKE', '%AC%')
              ->orWhere('name', 'LIKE', '%split%');
        })
        ->orderBy('unit_price', 'asc')
        ->take(6)
        ->get();
    
    return view('theme-views.pages.samsung-hub', compact('washingMachines', 'refrigerators', 'tvs', 'acs'));
})->name('samsung.hub');

// Samsung Washing Machines
Route::get('/samsung-washing-machines', function() {
    $products = \App\Models\Product::active()
        ->where('name', 'LIKE', '%Samsung%')
        ->where(function($q) {
            $q->where('name', 'LIKE', '%washing%')
              ->orWhere('name', 'LIKE', '%washer%')
              ->orWhere('name', 'LIKE', '%twin tub%');
        })
        ->orderBy('unit_price', 'asc')
        ->get();
    
    $twinTubs = $products->filter(fn($p) => str_contains(strtolower($p->name), 'twin'));
    $topLoaders = $products->filter(fn($p) => str_contains(strtolower($p->name), 'top load'));
    $frontLoaders = $products->filter(fn($p) => str_contains(strtolower($p->name), 'front'));
    
    return view('theme-views.pages.samsung-washing-machines', compact('products', 'twinTubs', 'topLoaders', 'frontLoaders'));
})->name('samsung.washing-machines');

// Samsung Refrigerators
Route::get('/samsung-refrigerators', function() {
    $products = \App\Models\Product::active()
        ->where('name', 'LIKE', '%Samsung%')
        ->where(function($q) {
            $q->where('name', 'LIKE', '%fridge%')
              ->orWhere('name', 'LIKE', '%refrigerator%')
              ->orWhere('name', 'LIKE', '%freezer%');
        })
        ->orderBy('unit_price', 'asc')
        ->get();
    
    $doubleDoors = $products->filter(fn($p) => str_contains(strtolower($p->name), 'double'));
    $sideBySides = $products->filter(fn($p) => str_contains(strtolower($p->name), 'side'));
    $frenchDoors = $products->filter(fn($p) => str_contains(strtolower($p->name), 'french'));
    $singleDoors = $products->filter(fn($p) => !str_contains(strtolower($p->name), 'double') && !str_contains(strtolower($p->name), 'side') && !str_contains(strtolower($p->name), 'french'));
    
    return view('theme-views.pages.samsung-refrigerators', compact('products', 'doubleDoors', 'sideBySides', 'frenchDoors', 'singleDoors'));
})->name('samsung.refrigerators');


// ============================================
// PROGRAMMATIC SEO - PRICE PAGES
// Target keywords: [product] price Uganda
// ============================================

// TV Price Pages
Route::get('/prices/32-inch-tv-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%32%inch%')
        ->orWhere('name', 'like', '%32 inch%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => '32 Inch TV Prices in Uganda 2026',
        'metaDescription' => 'Compare 32 inch TV prices in Uganda. Genuine Hisense, TCL, Samsung TVs. Free Kampala delivery.',
        'categoryName' => '32 Inch TVs',
    ]);
});

Route::get('/prices/43-inch-tv-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%43%inch%')
        ->orWhere('name', 'like', '%43 inch%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => '43 Inch TV Prices in Uganda 2026',
        'metaDescription' => 'Compare 43 inch Smart TV prices in Uganda. Genuine Hisense, CHiQ, Samsung 4K TVs. Free delivery Kampala.',
        'categoryName' => '43 Inch TVs',
    ]);
});

Route::get('/prices/50-inch-tv-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%50%inch%')
        ->orWhere('name', 'like', '%50 inch%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => '50 Inch TV Prices in Uganda 2026',
        'metaDescription' => 'Compare 50 inch 4K Smart TV prices in Uganda. Hisense, CHiQ, Samsung. Best prices + free Kampala delivery.',
        'categoryName' => '50 Inch TVs',
    ]);
});

Route::get('/prices/55-inch-tv-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%55%inch%')
        ->orWhere('name', 'like', '%55 inch%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => '55 Inch TV Prices in Uganda 2026',
        'metaDescription' => 'Compare 55 inch 4K Smart TV prices in Uganda. Premium Hisense, Samsung 55 inch TVs with free Kampala delivery.',
        'categoryName' => '55 Inch TVs',
    ]);
});

Route::get('/prices/65-inch-tv-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%65%inch%')
        ->orWhere('name', 'like', '%65 inch%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => '65 Inch TV Prices in Uganda 2026',
        'metaDescription' => 'Compare 65 inch 4K Smart TV prices in Uganda. Large screen Hisense, Samsung TVs. Best prices + free delivery.',
        'categoryName' => '65 Inch TVs',
    ]);
});

Route::get('/prices/75-inch-tv-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%75%inch%')
        ->orWhere('name', 'like', '%75 inch%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => '75 Inch TV Prices in Uganda 2026',
        'metaDescription' => 'Compare 75 inch 4K Smart TV prices in Uganda. Premium large screen TVs from Hisense, Samsung. Free Kampala delivery.',
        'categoryName' => '75 Inch TVs',
    ]);
});

Route::get('/prices/tcl-tv-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%TCL%')
        ->orWhere('name', 'like', '%tcl%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => 'TCL TV Prices in Uganda 2026',
        'metaDescription' => 'Compare TCL Smart TV prices in Uganda. Affordable 4K Android TVs from the world\'s #2 TV brand. Best prices + free delivery.',
        'categoryName' => 'TCL TVs',
    ]);
});

Route::get('/prices/smart-tv-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%Smart TV%')
        ->orWhere('name', 'like', '%smart tv%')
        ->orWhere('name', 'like', '%Smart%TV%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => 'Smart TV Prices in Uganda 2026 | All Sizes & Brands',
        'metaDescription' => 'Compare smart TV prices in Uganda. 32, 43, 50, 55, 65 & 75 inch smart TVs from Hisense, Samsung, TCL. Free Kampala delivery.',
        'categoryName' => 'Smart TVs',
    ]);
});

// Fridge Price Pages  
Route::get('/prices/fridge-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%fridge%')
        ->orWhere('name', 'like', '%refrigerator%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => 'Fridge Prices in Uganda 2026',
        'metaDescription' => 'Compare refrigerator prices in Uganda. Buy genuine Samsung, Hisense, ADH fridges. Free Kampala delivery.',
        'categoryName' => 'Refrigerators',
    ]);
});

Route::get('/prices/washing-machine-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%washing%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => 'Washing Machine Prices in Uganda 2026',
        'metaDescription' => 'Compare washing machine prices in Uganda. Buy genuine Samsung, Hisense washers. Free Kampala delivery.',
        'categoryName' => 'Washing Machines',
    ]);
});

Route::get('/prices/chest-freezer-uganda', function () {
    $products = \App\Models\Product::where('name', 'like', '%freezer%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => 'Chest Freezer Prices in Uganda 2026',
        'metaDescription' => 'Compare chest freezer and deep freezer prices in Uganda. ADH, Hisense, SPJ freezers. Free Kampala delivery.',
        'categoryName' => 'Chest Freezers',
    ]);
});

// Brand-specific pages
Route::get('/prices/hisense-tv-uganda', function () {
    $brand = \App\Models\Brand::where('name', 'like', '%hisense%')->first();
    $products = \App\Models\Product::where('brand_id', $brand->id ?? 0)
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => 'Hisense TV Prices in Uganda 2026',
        'metaDescription' => 'Compare all Hisense TV prices in Uganda. 32, 43, 50, 55, 65 inch Smart TVs. Genuine products. Free delivery.',
        'categoryName' => 'Hisense TVs',
    ]);
});

Route::get('/prices/chiq-tv-uganda', function () {
    $brand = \App\Models\Brand::where('name', 'like', '%chiq%')->first();
    $products = \App\Models\Product::where('brand_id', $brand->id ?? 0)
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.price-page', [
        'products' => $products,
        'pageTitle' => 'CHiQ TV Prices in Uganda 2026',
        'metaDescription' => 'Compare CHiQ TV prices in Uganda. Affordable 4K Smart TVs with Android. Best prices + free Kampala delivery.',
        'categoryName' => 'CHiQ TVs',
    ]);
});


// ============================================
// BRAND LANDING PAGES
// ============================================

// Hisense Hub
Route::get('/hisense', function () {
    $brand = \App\Models\Brand::where('name', 'like', '%hisense%')->first();
    $brandId = $brand->id ?? 0;
    
    $tvs = \App\Models\Product::where('brand_id', $brandId)
        ->where('name', 'like', '%TV%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    $fridges = \App\Models\Product::where('brand_id', $brandId)
        ->where(function($q) {
            $q->where('name', 'like', '%fridge%')
              ->orWhere('name', 'like', '%refrigerator%');
        })
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    $washers = \App\Models\Product::where('brand_id', $brandId)
        ->where('name', 'like', '%wash%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.hisense-hub', compact('tvs', 'fridges', 'washers'));
})->name('hisense.hub');

// CHiQ Hub
Route::get('/chiq', function () {
    $brand = \App\Models\Brand::where('name', 'like', '%chiq%')->first();
    $brandId = $brand->id ?? 0;
    
    $tvs = \App\Models\Product::where('brand_id', $brandId)
        ->where('name', 'like', '%TV%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    $fridges = \App\Models\Product::where('brand_id', $brandId)
        ->where(function($q) {
            $q->where('name', 'like', '%fridge%')
              ->orWhere('name', 'like', '%refrigerator%');
        })
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    $freezers = \App\Models\Product::where('brand_id', $brandId)
        ->where('name', 'like', '%freezer%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.chiq-hub', compact('tvs', 'fridges', 'freezers'));
})->name('chiq.hub');

// ADH Hub
Route::get('/adh', function () {
    $brand = \App\Models\Brand::where('name', 'like', '%adh%')->first();
    $brandId = $brand->id ?? 0;
    
    $freezers = \App\Models\Product::where('brand_id', $brandId)
        ->where('name', 'like', '%freezer%')
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    $fridges = \App\Models\Product::where('brand_id', $brandId)
        ->where(function($q) {
            $q->where('name', 'like', '%fridge%')
              ->orWhere('name', 'like', '%refrigerator%');
        })
        ->where('status', 1)
        ->orderBy('unit_price', 'asc')
        ->get();
    
    return view('theme-views.pages.adh-hub', compact('freezers', 'fridges'));
})->name('adh.hub');


// Brands Index Page
Route::get('/brands', function () {
    return view('theme-views.pages.brands-index');
})->name('brands');

// ============================================
// NEW SEO KEYWORD CLUSTER PAGES - March 7, 2026
// ============================================

// "Where to Buy" Intent Pages
Route::get('/buy/electronics-kampala', function () {
    return view('theme-views.pages.buy-electronics-kampala');
})->name('buy.electronics-kampala');

Route::get('/buy/tv-uganda', function () {
    return view('theme-views.pages.buy-tv-uganda');
})->name('buy.tv-uganda');

// "Best" Intent Pages
Route::get('/best/smart-tv-uganda', function () {
    return view('theme-views.pages.best-smart-tv-uganda');
})->name('best.smart-tv-uganda');

// Comparison Pages
Route::get('/compare/hisense-vs-tcl', function () {
    return view('theme-views.pages.compare-hisense-vs-tcl');
})->name('compare.hisense-vs-tcl');

// FAQ Hub
Route::get('/faq', function () {
    return view('theme-views.pages.faq-hub');
})->name('faq');


// Tools - Linkable Assets
Route::get('/tools/tv-size-calculator', function () {
    return view('theme-views.pages.tv-size-calculator');
})->name('tools.tv-calculator');
