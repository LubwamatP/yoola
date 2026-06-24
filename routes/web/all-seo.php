<?php
use Illuminate\Support\Facades\Route;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| ALL CATEGORY SEO PAGES
|--------------------------------------------------------------------------
*/

// ============================================
// WASHING MACHINES & LAUNDRY
// ============================================
Route::get('/washing-machine-prices-uganda', function () {
    $products = Product::active()->where('name', 'LIKE', '%washing%')->orWhere('name', 'LIKE', '%washer%')
        ->orderBy('unit_price', 'asc')->limit(40)->get();
    return view('theme-views.pages.seo.tv-listing', [
        'products' => $products, 'h1' => 'Washing Machine Prices in Uganda',
        'pageTitle' => 'Washing Machine Prices Uganda 2026 | Buy Washers Online',
        'metaDescription' => 'Compare washing machine prices in Uganda. Samsung, LG, Hisense washers. Free delivery Kampala.',
    ]);
})->name('seo.washing-machines');

$laundryPages = [
    'front-loader-washing-machine-uganda' => ['search' => '%front load%', 'name' => 'Front Loader Washing Machines'],
    'top-loader-washing-machine-uganda' => ['search' => '%top load%', 'name' => 'Top Loader Washing Machines'],
    'clothes-dryer-prices-uganda' => ['search' => '%dryer%', 'name' => 'Clothes Dryers'],
    'washer-dryer-combo-uganda' => ['search' => '%washer%dryer%', 'name' => 'Washer Dryer Combos'],
    'samsung-washing-machine-uganda' => ['search' => '%samsung%wash%', 'name' => 'Samsung Washing Machines'],
    'lg-washing-machine-uganda' => ['search' => '%lg%wash%', 'name' => 'LG Washing Machines'],
    'hisense-washing-machine-uganda' => ['search' => '%hisense%wash%', 'name' => 'Hisense Washing Machines'],
];
foreach ($laundryPages as $slug => $c) {
    Route::get("/{$slug}", function () use ($c) {
        $products = Product::active()->where('name', 'LIKE', $c['search'])->orderBy('unit_price', 'asc')->limit(30)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => $c['name'] . ' Prices Uganda',
            'pageTitle' => $c['name'] . ' Prices Uganda 2026', 'metaDescription' => 'Buy ' . $c['name'] . ' in Uganda. Best prices, free delivery.',
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// COOKERS & OVENS
// ============================================
Route::get('/cooker-prices-uganda', function () {
    $products = Product::active()->where(function($q) {
        $q->where('name', 'LIKE', '%cooker%')->orWhere('name', 'LIKE', '%oven%')->orWhere('name', 'LIKE', '%stove%');
    })->orderBy('unit_price', 'asc')->limit(40)->get();
    return view('theme-views.pages.seo.tv-listing', [
        'products' => $products, 'h1' => 'Cooker & Oven Prices in Uganda',
        'pageTitle' => 'Cooker Prices Uganda 2026 | Gas & Electric Cookers',
        'metaDescription' => 'Buy cookers in Uganda. Gas cookers, electric ovens, built-in hobs. Best prices at Yoola.ug',
    ]);
})->name('seo.cookers');

$cookerPages = [
    'gas-cooker-prices-uganda' => ['search' => '%gas cooker%', 'name' => 'Gas Cookers'],
    'electric-cooker-prices-uganda' => ['search' => '%electric cooker%', 'name' => 'Electric Cookers'],
    'built-in-oven-prices-uganda' => ['search' => '%built%oven%', 'name' => 'Built-in Ovens'],
    'microwave-prices-uganda' => ['search' => '%microwave%', 'name' => 'Microwaves'],
    'hot-plate-prices-uganda' => ['search' => '%hot plate%', 'name' => 'Hot Plates'],
    'induction-cooker-uganda' => ['search' => '%induction%', 'name' => 'Induction Cookers'],
];
foreach ($cookerPages as $slug => $c) {
    Route::get("/{$slug}", function () use ($c) {
        $products = Product::active()->where('name', 'LIKE', $c['search'])->orderBy('unit_price', 'asc')->limit(30)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => $c['name'] . ' Prices Uganda',
            'pageTitle' => $c['name'] . ' Prices Uganda 2026', 'metaDescription' => 'Buy ' . $c['name'] . ' in Uganda. Free delivery Kampala.',
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// AIR CONDITIONERS
// ============================================
Route::get('/air-conditioner-prices-uganda', function () {
    $products = Product::active()->where('name', 'LIKE', '%air con%')->orWhere('name', 'LIKE', '% ac %')->orWhere('name', 'LIKE', '%split%')
        ->orderBy('unit_price', 'asc')->limit(40)->get();
    return view('theme-views.pages.seo.tv-listing', [
        'products' => $products, 'h1' => 'Air Conditioner Prices in Uganda',
        'pageTitle' => 'Air Conditioner Prices Uganda 2026 | Split & Portable AC',
        'metaDescription' => 'Buy air conditioners in Uganda. Split AC, portable AC. Samsung, Hisense, CHiQ. Free installation.',
    ]);
})->name('seo.air-conditioners');

$acPages = [
    'split-ac-prices-uganda' => ['search' => '%split%', 'name' => 'Split Air Conditioners'],
    'portable-ac-prices-uganda' => ['search' => '%portable%ac%', 'name' => 'Portable Air Conditioners'],
    'samsung-ac-prices-uganda' => ['search' => '%samsung%', 'name' => 'Samsung Air Conditioners', 'extra' => '%ac%'],
    'hisense-ac-prices-uganda' => ['search' => '%hisense%', 'name' => 'Hisense Air Conditioners', 'extra' => '%ac%'],
];
foreach ($acPages as $slug => $c) {
    Route::get("/{$slug}", function () use ($c) {
        $q = Product::active()->where('name', 'LIKE', $c['search']);
        if (isset($c['extra'])) $q->where('name', 'LIKE', '%air con%');
        $products = $q->orderBy('unit_price', 'asc')->limit(30)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => $c['name'] . ' Prices Uganda',
            'pageTitle' => $c['name'] . ' Prices Uganda 2026', 'metaDescription' => 'Buy ' . $c['name'] . ' in Uganda.',
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// KITCHEN APPLIANCES
// ============================================
$kitchenPages = [
    'blender-prices-uganda' => ['search' => '%blender%', 'name' => 'Blenders'],
    'electric-kettle-prices-uganda' => ['search' => '%kettle%', 'name' => 'Electric Kettles'],
    'coffee-maker-prices-uganda' => ['search' => '%coffee%maker%', 'name' => 'Coffee Makers'],
    'air-fryer-prices-uganda' => ['search' => '%air fryer%', 'name' => 'Air Fryers'],
    'juicer-prices-uganda' => ['search' => '%juicer%', 'name' => 'Juicers'],
    'food-processor-prices-uganda' => ['search' => '%food processor%', 'name' => 'Food Processors'],
    'toaster-prices-uganda' => ['search' => '%toaster%', 'name' => 'Toasters'],
    'sandwich-maker-prices-uganda' => ['search' => '%sandwich%maker%', 'name' => 'Sandwich Makers'],
    'rice-cooker-prices-uganda' => ['search' => '%rice cooker%', 'name' => 'Rice Cookers'],
    'pressure-cooker-prices-uganda' => ['search' => '%pressure cooker%', 'name' => 'Pressure Cookers'],
    'water-dispenser-prices-uganda' => ['search' => '%water dispenser%', 'name' => 'Water Dispensers'],
];
foreach ($kitchenPages as $slug => $c) {
    Route::get("/{$slug}", function () use ($c) {
        $products = Product::active()->where('name', 'LIKE', $c['search'])->orderBy('unit_price', 'asc')->limit(30)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => $c['name'] . ' Prices Uganda',
            'pageTitle' => $c['name'] . ' Prices Uganda 2026 | Buy Online',
            'metaDescription' => 'Buy ' . $c['name'] . ' in Uganda. Best prices, genuine products. Free delivery Kampala.',
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// AUDIO & SPEAKERS
// ============================================
Route::get('/speaker-prices-uganda', function () {
    $products = Product::active()->where('name', 'LIKE', '%speaker%')->orWhere('name', 'LIKE', '%soundbar%')
        ->orderBy('unit_price', 'asc')->limit(40)->get();
    return view('theme-views.pages.seo.tv-listing', [
        'products' => $products, 'h1' => 'Speaker Prices in Uganda',
        'pageTitle' => 'Speaker Prices Uganda 2026 | Soundbars, Bluetooth Speakers',
        'metaDescription' => 'Buy speakers in Uganda. Soundbars, party speakers, Bluetooth speakers. JBL, Samsung, Sony.',
    ]);
})->name('seo.speakers');

$audioPages = [
    'soundbar-prices-uganda' => ['search' => '%soundbar%', 'name' => 'Soundbars'],
    'bluetooth-speaker-prices-uganda' => ['search' => '%bluetooth%speaker%', 'name' => 'Bluetooth Speakers'],
    'party-speaker-prices-uganda' => ['search' => '%party%speaker%', 'name' => 'Party Speakers'],
    'home-theatre-prices-uganda' => ['search' => '%home theatre%', 'name' => 'Home Theatre Systems'],
    'jbl-speaker-prices-uganda' => ['search' => '%jbl%', 'name' => 'JBL Speakers'],
    'samsung-soundbar-prices-uganda' => ['search' => '%samsung%soundbar%', 'name' => 'Samsung Soundbars'],
    'headphones-prices-uganda' => ['search' => '%headphone%', 'name' => 'Headphones'],
    'earbuds-prices-uganda' => ['search' => '%earbud%', 'name' => 'Earbuds'],
];
foreach ($audioPages as $slug => $c) {
    Route::get("/{$slug}", function () use ($c) {
        $products = Product::active()->where('name', 'LIKE', $c['search'])->orderBy('unit_price', 'asc')->limit(30)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => $c['name'] . ' Prices Uganda',
            'pageTitle' => $c['name'] . ' Prices Uganda 2026', 'metaDescription' => 'Buy ' . $c['name'] . ' in Uganda. Best deals.',
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// HOME COMFORT
// ============================================
$comfortPages = [
    'fan-prices-uganda' => ['search' => '%fan%', 'name' => 'Fans'],
    'standing-fan-prices-uganda' => ['search' => '%standing fan%', 'name' => 'Standing Fans'],
    'ceiling-fan-prices-uganda' => ['search' => '%ceiling fan%', 'name' => 'Ceiling Fans'],
    'iron-prices-uganda' => ['search' => '%iron%', 'name' => 'Irons'],
    'steam-iron-prices-uganda' => ['search' => '%steam iron%', 'name' => 'Steam Irons'],
    'vacuum-cleaner-prices-uganda' => ['search' => '%vacuum%', 'name' => 'Vacuum Cleaners'],
];
foreach ($comfortPages as $slug => $c) {
    Route::get("/{$slug}", function () use ($c) {
        $products = Product::active()->where('name', 'LIKE', $c['search'])->orderBy('unit_price', 'asc')->limit(30)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => $c['name'] . ' Prices Uganda',
            'pageTitle' => $c['name'] . ' Prices Uganda 2026', 'metaDescription' => 'Buy ' . $c['name'] . ' in Uganda.',
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// PERSONAL CARE
// ============================================
$carePages = [
    'hair-clipper-prices-uganda' => ['search' => '%clipper%', 'name' => 'Hair Clippers'],
    'trimmer-prices-uganda' => ['search' => '%trimmer%', 'name' => 'Trimmers'],
    'hair-dryer-prices-uganda' => ['search' => '%hair dryer%', 'name' => 'Hair Dryers'],
];
foreach ($carePages as $slug => $c) {
    Route::get("/{$slug}", function () use ($c) {
        $products = Product::active()->where('name', 'LIKE', $c['search'])->orderBy('unit_price', 'asc')->limit(30)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => $c['name'] . ' Prices Uganda',
            'pageTitle' => $c['name'] . ' Prices Uganda 2026', 'metaDescription' => 'Buy ' . $c['name'] . ' in Uganda.',
        ]);
    })->name("seo.{$slug}");
}
