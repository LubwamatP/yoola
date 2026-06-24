<?php
use Illuminate\Support\Facades\Route;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| SEO BOOST: Location, Comparison, Model, Deal Pages
|--------------------------------------------------------------------------
*/

// ============================================
// LOCATION PAGES (Local SEO)
// ============================================
$locations = [
    'kampala' => ['name' => 'Kampala', 'desc' => 'Free same-day delivery'],
    'wakiso' => ['name' => 'Wakiso', 'desc' => 'Next-day delivery'],
    'entebbe' => ['name' => 'Entebbe', 'desc' => 'Fast delivery'],
    'jinja' => ['name' => 'Jinja', 'desc' => 'Delivery available'],
    'mbarara' => ['name' => 'Mbarara', 'desc' => 'Delivery available'],
    'gulu' => ['name' => 'Gulu', 'desc' => 'Delivery available'],
    'mukono' => ['name' => 'Mukono', 'desc' => 'Next-day delivery'],
];
$locationProducts = ['tv', 'fridge', 'electronics', 'washing-machine', 'air-conditioner'];

foreach ($locations as $locSlug => $loc) {
    // General electronics page per location
    Route::get("/electronics-shop-{$locSlug}", function () use ($loc) {
        $products = Product::active()->inRandomOrder()->limit(40)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products,
            'h1' => "Electronics Shop in {$loc['name']}",
            'pageTitle' => "Electronics Shop {$loc['name']} | Buy Online | Yoola.ug",
            'metaDescription' => "Buy electronics in {$loc['name']}. TVs, fridges, cookers, ACs. {$loc['desc']}. Best prices at Yoola.ug",
        ]);
    })->name("seo.electronics-{$locSlug}");

    // Product + location combos
    Route::get("/buy-tv-{$locSlug}", function () use ($loc) {
        $products = Product::active()->where('name', 'LIKE', '%tv%')->orderBy('unit_price')->limit(30)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => "Buy TV in {$loc['name']}",
            'pageTitle' => "Buy TV in {$loc['name']} | Smart TVs | Free Delivery",
            'metaDescription' => "Buy Smart TVs in {$loc['name']}. Samsung, Hisense, CHiQ. {$loc['desc']}. Yoola.ug",
        ]);
    })->name("seo.tv-{$locSlug}");

    Route::get("/buy-fridge-{$locSlug}", function () use ($loc) {
        $products = Product::active()->where('name', 'LIKE', '%fridge%')->orWhere('name', 'LIKE', '%refrigerator%')->orderBy('unit_price')->limit(30)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => "Buy Fridge in {$loc['name']}",
            'pageTitle' => "Buy Fridge in {$loc['name']} | Refrigerators | Delivery",
            'metaDescription' => "Buy fridges in {$loc['name']}. Samsung, Hisense refrigerators. {$loc['desc']}. Yoola.ug",
        ]);
    })->name("seo.fridge-{$locSlug}");
}

// ============================================
// COMPARISON PAGES (vs pages)
// ============================================
$comparisons = [
    'samsung-vs-hisense-tv' => ['a' => 'Samsung', 'b' => 'Hisense', 'cat' => 'tv', 'name' => 'Samsung vs Hisense TV'],
    'samsung-vs-lg-tv' => ['a' => 'Samsung', 'b' => 'LG', 'cat' => 'tv', 'name' => 'Samsung vs LG TV'],
    'hisense-vs-chiq-tv' => ['a' => 'Hisense', 'b' => 'CHiQ', 'cat' => 'tv', 'name' => 'Hisense vs CHiQ TV'],
    'samsung-vs-hisense-fridge' => ['a' => 'Samsung', 'b' => 'Hisense', 'cat' => 'fridge', 'name' => 'Samsung vs Hisense Fridge'],
    'samsung-vs-lg-washing-machine' => ['a' => 'Samsung', 'b' => 'LG', 'cat' => 'wash', 'name' => 'Samsung vs LG Washing Machine'],
    'front-loader-vs-top-loader' => ['a' => 'front load', 'b' => 'top load', 'cat' => 'wash', 'name' => 'Front Loader vs Top Loader'],
    'split-ac-vs-portable-ac' => ['a' => 'split', 'b' => 'portable', 'cat' => 'air con', 'name' => 'Split AC vs Portable AC'],
    'gas-cooker-vs-electric-cooker' => ['a' => 'gas cooker', 'b' => 'electric cooker', 'cat' => 'cooker', 'name' => 'Gas vs Electric Cooker'],
    'soundbar-vs-home-theatre' => ['a' => 'soundbar', 'b' => 'home theatre', 'cat' => 'speaker', 'name' => 'Soundbar vs Home Theatre'],
    'chest-freezer-vs-upright-freezer' => ['a' => 'chest', 'b' => 'upright', 'cat' => 'freezer', 'name' => 'Chest vs Upright Freezer'],
];

foreach ($comparisons as $slug => $c) {
    Route::get("/{$slug}-uganda", function () use ($c) {
        $productsA = Product::active()->where('name', 'LIKE', "%{$c['a']}%")->where('name', 'LIKE', "%{$c['cat']}%")->limit(15)->get();
        $productsB = Product::active()->where('name', 'LIKE', "%{$c['b']}%")->where('name', 'LIKE', "%{$c['cat']}%")->limit(15)->get();
        $products = $productsA->merge($productsB);
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => "{$c['name']} Comparison Uganda",
            'pageTitle' => "{$c['name']} Uganda 2026 | Which is Better?",
            'metaDescription' => "Compare {$c['name']} in Uganda. Prices, features, pros & cons. Find the best for you at Yoola.ug",
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// MODEL-SPECIFIC PAGES (exact match)
// ============================================
$models = [
    // TVs
    'hisense-32-inch-tv-price' => ['search' => '%hisense%32%', 'name' => 'Hisense 32 Inch TV'],
    'hisense-43-inch-tv-price' => ['search' => '%hisense%43%', 'name' => 'Hisense 43 Inch TV'],
    'hisense-55-inch-tv-price' => ['search' => '%hisense%55%', 'name' => 'Hisense 55 Inch TV'],
    'samsung-32-inch-tv-price' => ['search' => '%samsung%32%', 'name' => 'Samsung 32 Inch TV'],
    'samsung-43-inch-tv-price' => ['search' => '%samsung%43%', 'name' => 'Samsung 43 Inch TV'],
    'samsung-55-inch-tv-price' => ['search' => '%samsung%55%', 'name' => 'Samsung 55 Inch TV'],
    'samsung-65-inch-tv-price' => ['search' => '%samsung%65%', 'name' => 'Samsung 65 Inch TV'],
    'chiq-32-inch-tv-price' => ['search' => '%chiq%32%', 'name' => 'CHiQ 32 Inch TV'],
    'chiq-43-inch-tv-price' => ['search' => '%chiq%43%', 'name' => 'CHiQ 43 Inch TV'],
    'chiq-55-inch-tv-price' => ['search' => '%chiq%55%', 'name' => 'CHiQ 55 Inch TV'],
    'lg-43-inch-tv-price' => ['search' => '%lg%43%', 'name' => 'LG 43 Inch TV'],
    'lg-55-inch-tv-price' => ['search' => '%lg%55%', 'name' => 'LG 55 Inch TV'],
    // Fridges
    'samsung-double-door-fridge-price' => ['search' => '%samsung%double%door%', 'name' => 'Samsung Double Door Fridge'],
    'hisense-double-door-fridge-price' => ['search' => '%hisense%double%door%', 'name' => 'Hisense Double Door Fridge'],
    'samsung-side-by-side-fridge-price' => ['search' => '%samsung%side%side%', 'name' => 'Samsung Side by Side Fridge'],
    // Washers
    'samsung-front-loader-price' => ['search' => '%samsung%front%load%', 'name' => 'Samsung Front Loader'],
    'lg-front-loader-price' => ['search' => '%lg%front%load%', 'name' => 'LG Front Loader'],
    'samsung-top-loader-price' => ['search' => '%samsung%top%load%', 'name' => 'Samsung Top Loader'],
    // ACs
    'samsung-split-ac-price' => ['search' => '%samsung%split%', 'name' => 'Samsung Split AC'],
    'hisense-split-ac-price' => ['search' => '%hisense%split%', 'name' => 'Hisense Split AC'],
    // Speakers
    'jbl-partybox-price' => ['search' => '%jbl%partybox%', 'name' => 'JBL PartyBox'],
    'samsung-soundbar-price' => ['search' => '%samsung%soundbar%', 'name' => 'Samsung Soundbar'],
];

foreach ($models as $slug => $m) {
    Route::get("/{$slug}-uganda", function () use ($m) {
        $products = Product::active()->where('name', 'LIKE', $m['search'])->orderBy('unit_price')->limit(20)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => "{$m['name']} Price in Uganda",
            'pageTitle' => "{$m['name']} Price Uganda 2026 | Buy Online",
            'metaDescription' => "Buy {$m['name']} in Uganda. Best price, genuine product with warranty. Free delivery at Yoola.ug",
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// DEAL/PROMO PAGES
// ============================================
$deals = [
    'electronics-deals-uganda' => ['name' => 'Electronics Deals', 'desc' => 'Best deals on electronics'],
    'tv-deals-kampala' => ['name' => 'TV Deals Kampala', 'desc' => 'Discounted TVs in Kampala'],
    'cheap-electronics-uganda' => ['name' => 'Cheap Electronics', 'desc' => 'Affordable electronics'],
    'electronics-sale-uganda' => ['name' => 'Electronics Sale', 'desc' => 'Electronics on sale'],
    'best-electronics-deals-2026' => ['name' => 'Best Electronics Deals 2026', 'desc' => 'Top deals this year'],
];

foreach ($deals as $slug => $d) {
    Route::get("/{$slug}", function () use ($d) {
        $products = Product::active()->where('discount', '>', 0)->orderByDesc('discount')->limit(40)->get();
        if ($products->count() < 10) {
            $products = Product::active()->inRandomOrder()->limit(40)->get();
        }
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => $d['name'],
            'pageTitle' => "{$d['name']} | Up to 50% Off | Yoola.ug",
            'metaDescription' => "{$d['desc']}. TVs, fridges, cookers at discounted prices. Shop now at Yoola.ug",
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// QUESTION/INTENT PAGES
// ============================================
$questions = [
    'where-to-buy-tv-uganda' => ['search' => '%tv%', 'name' => 'Where to Buy TV in Uganda'],
    'where-to-buy-fridge-uganda' => ['search' => '%fridge%', 'name' => 'Where to Buy Fridge in Uganda'],
    'best-electronics-shop-uganda' => ['search' => '', 'name' => 'Best Electronics Shop in Uganda'],
    'original-tv-uganda' => ['search' => '%tv%', 'name' => 'Original TVs in Uganda'],
    'genuine-electronics-uganda' => ['search' => '', 'name' => 'Genuine Electronics in Uganda'],
];

foreach ($questions as $slug => $q) {
    Route::get("/{$slug}", function () use ($q) {
        if ($q['search']) {
            $products = Product::active()->where('name', 'LIKE', $q['search'])->orderBy('unit_price')->limit(30)->get();
        } else {
            $products = Product::active()->inRandomOrder()->limit(30)->get();
        }
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => $q['name'],
            'pageTitle' => "{$q['name']} | Trusted Store | Yoola.ug",
            'metaDescription' => "{$q['name']}. Genuine products, warranty included. Free delivery Kampala. Yoola.ug",
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// TCL COMPARISON PAGES
// ============================================
$tclComparisons = [
    'tcl-vs-hisense-tv' => ['a' => 'TCL', 'b' => 'Hisense', 'cat' => 'tv', 'name' => 'TCL vs Hisense TV'],
    'tcl-vs-samsung-tv' => ['a' => 'TCL', 'b' => 'Samsung', 'cat' => 'tv', 'name' => 'TCL vs Samsung TV'],
    'tcl-vs-chiq-tv' => ['a' => 'TCL', 'b' => 'CHiQ', 'cat' => 'tv', 'name' => 'TCL vs CHiQ TV'],
];
foreach ($tclComparisons as $slug => $c) {
    Route::get("/{$slug}-uganda", function () use ($c) {
        $productsA = Product::active()->where('name', 'LIKE', "%{$c['a']}%")->where('name', 'LIKE', "%{$c['cat']}%")->limit(15)->get();
        $productsB = Product::active()->where('name', 'LIKE', "%{$c['b']}%")->where('name', 'LIKE', "%{$c['cat']}%")->limit(15)->get();
        $products = $productsA->merge($productsB);
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products, 'h1' => "{$c['name']} Comparison Uganda",
            'pageTitle' => "{$c['name']} Uganda 2026 | Which is Better?",
            'metaDescription' => "Compare {$c['name']} in Uganda. Prices, features, pros & cons. Find the best for you at Yoola.ug",
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// CHiQ INTENT PAGES (CHiQ not sold, redirect to Hisense)
// ============================================
Route::get('/is-chiq-tv-good-brand', function () {
    $products = Product::active()->where('name', 'LIKE', '%hisense%')->where('name', 'LIKE', '%tv%')->orderBy('unit_price')->limit(12)->get();
    return view('theme-views.pages.seo.helpful-faq', [
        'h1' => 'Is CHiQ TV a Good Brand? (2026 Honest Review)',
        'pageTitle' => 'Is CHiQ TV a Good Brand? | Honest Review Uganda',
        'metaDescription' => 'Is CHiQ TV good quality? Honest review from Ugandan users. Compare with Hisense. Better alternatives at Yoola.ug.',
        'intro' => 'CHiQ is a budget Chinese TV brand popular in Uganda. But is it actually good? Here\'s the honest truth.',
        'faqs' => [
            ['q' => 'Is CHiQ a good brand?', 'a' => 'CHiQ is a <strong>decent budget option</strong>. Picture quality is acceptable for the price. However, Hisense offers better build quality, longer warranty, and better support at a similar price point.'],
            ['q' => 'How long do CHiQ TVs last?', 'a' => 'CHiQ TVs typically last 2-3 years with normal use. Hisense TVs average 5+ years with proper care. The difference is in component quality and warranty support.'],
            ['q' => 'CHiQ vs Hisense: Which is better?', 'a' => '<strong>Hisense wins in almost every category:</strong><ul><li>Better picture quality</li><li>Stronger warranty (2 years vs 1)</li><li>Better after-sales support</li><li>More reliable components</li></ul><p>For the small price difference, Hisense is the smarter choice.</p>'],
            ['q' => 'Should I buy a CHiQ TV?', 'a' => 'If budget is extremely tight, it works. But for slightly more, you get a much better experience with Hisense. We recommend Hisense as the best value TV brand in Uganda.'],
            ['q' => 'Are CHiQ TVs sold at Yoola?', 'a' => 'No. After testing, we chose to sell Hisense because of better quality, warranty, and customer satisfaction. Browse our Hisense collection below.'],
        ],
        'products' => $products,
    ]);
})->name('faq.is-chiq-good');

Route::get('/is-chiq-a-good-brand', function () {
    return redirect('/is-chiq-tv-good-brand', 301);
});

// ============================================
// "IS X BRAND GOOD" PAGES (Mid-funnel intent capture)
// ============================================
$brandGoodPages = [
    'is-hisense-tv-good' => ['brand' => 'Hisense', 'cat' => 'tv', 'name' => 'Hisense TV', 'verdict' => 'YES. Hisense is the best value TV brand in Uganda. Great picture, reliable, and excellent warranty.'],
    'is-samsung-tv-good-uganda' => ['brand' => 'Samsung', 'cat' => 'tv', 'name' => 'Samsung TV', 'verdict' => 'YES. Samsung is the premium choice. Best picture quality, most features, longest lifespan.'],
    'is-lg-fridge-good-uganda' => ['brand' => 'LG', 'cat' => 'fridge', 'name' => 'LG Fridge', 'verdict' => 'YES. LG fridges are among the most reliable. Smart inverter technology saves electricity.'],
    'is-hisense-fridge-good' => ['brand' => 'Hisense', 'cat' => 'fridge', 'name' => 'Hisense Fridge', 'verdict' => 'YES. Hisense fridges offer great value. Reliable cooling, good warranty, affordable.'],
];
foreach ($brandGoodPages as $slug => $p) {
    Route::get("/{$slug}", function () use ($p) {
        $products = Product::active()->where('name', 'LIKE', "%{$p['brand']}%")->where('name', 'LIKE', "%{$p['cat']}%")->orderBy('unit_price')->limit(12)->get();
        return view('theme-views.pages.seo.helpful-faq', [
            'h1' => "Is {$p['name']} Good? (2026 Review)",
            'pageTitle' => "Is {$p['name']} Good in Uganda? | Honest Review",
            'metaDescription' => "Is {$p['name']} worth buying? Honest review from real users. {$p['verdict']}",
            'intro' => "Considering a {$p['name']}? Here's everything you need to know before buying.",
            'faqs' => [
                ['q' => "Are {$p['name']}s good quality?", 'a' => $p['verdict']],
                ['q' => 'Where can I buy genuine?', 'a' => 'Buy from authorized dealers like <strong>Yoola.ug</strong>. We verify every product from authorized distributors with real warranty.'],
                ['q' => 'Do they come with warranty?', 'a' => 'Yes! All our products come with manufacturer warranty. We handle all warranty claims for you.'],
                ['q' => "What's the price range?", 'a' => 'Prices vary by size and model. Browse below for current prices and availability.'],
            ],
            'products' => $products,
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// "BEST X" CATEGORY HUBS (High-intent buyer queries)
// ============================================
$bestHubs = [
    'best-fridge-uganda' => ['search' => '%fridge%', 'cat' => 'fridge', 'name' => 'Best Fridge', 'desc' => 'Compare the best refrigerators in Uganda. Samsung, Hisense, LG.'],
    'best-washing-machine-uganda' => ['search' => '%wash%', 'cat' => 'washing machine', 'name' => 'Best Washing Machine', 'desc' => 'Top rated washing machines in Uganda. Front loader, top loader. Samsung, LG, Hisense.'],
    'best-air-conditioner-uganda' => ['search' => '%air con%', 'cat' => 'air conditioner', 'name' => 'Best Air Conditioner', 'desc' => 'Best AC units in Uganda. Split, portable, inverter. Samsung, Hisense.'],
    'best-cooker-uganda' => ['search' => '%cooker%', 'cat' => 'cooker', 'name' => 'Best Cooker', 'desc' => 'Top cookers and ovens in Uganda. Gas, electric, built-in.'],
];
foreach ($bestHubs as $slug => $hub) {
    Route::get("/{$slug}", function () use ($hub) {
        $products = Product::active()->where('name', 'LIKE', $hub['search'])->orderBy('unit_price')->limit(30)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products,
            'h1' => $hub['name'] . ' in Uganda 2026',
            'pageTitle' => $hub['name'] . ' Uganda 2026 | Top Rated Picks',
            'metaDescription' => $hub['desc'] . ' Best prices, free delivery Kampala. Genuine warranty at Yoola.ug',
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// MORE LOCATIONS (Mbale, Lira, Masaka, Fort Portal, Arua)
// ============================================
$moreLocations = [
    'mbale' => ['name' => 'Mbale', 'desc' => 'Delivery to Eastern Uganda'],
    'lira' => ['name' => 'Lira', 'desc' => 'Delivery to Northern Uganda'],
    'masaka' => ['name' => 'Masaka', 'desc' => 'Delivery to Central Uganda'],
    'fort-portal' => ['name' => 'Fort Portal', 'desc' => 'Delivery to Western Uganda'],
    'arua' => ['name' => 'Arua', 'desc' => 'Delivery to West Nile'],
];
$locationCats = [
    'electronics' => ['search' => '', 'name' => 'Electronics'],
    'tv' => ['search' => '%tv%', 'name' => 'TVs'],
    'fridge' => ['search' => '%fridge%', 'name' => 'Fridges'],
    'cooker' => ['search' => '%cooker%', 'name' => 'Cookers'],
    'washing-machine' => ['search' => '%wash%', 'name' => 'Washing Machines'],
    'air-conditioner' => ['search' => '%air con%', 'name' => 'Air Conditioners'],
];
foreach ($moreLocations as $locSlug => $loc) {
    foreach ($locationCats as $catSlug => $cat) {
        Route::get("/buy-{$catSlug}-{$locSlug}", function () use ($loc, $cat) {
            $query = Product::active();
            if ($cat['search']) $query->where('name', 'LIKE', $cat['search']);
            $products = $query->orderBy('unit_price')->limit(24)->get();
            return view('theme-views.pages.seo.tv-listing', [
                'products' => $products,
                'h1' => "Buy {$cat['name']} in {$loc['name']}",
                'pageTitle' => "Buy {$cat['name']} in {$loc['name']} | Best Prices | Yoola.ug",
                'metaDescription' => "Buy {$cat['name']} in {$loc['name']}. {$loc['desc']}. Best prices, genuine warranty. Yoola.ug",
            ]);
        })->name("seo.{$catSlug}-{$locSlug}");
    }
}

// ============================================
// CAMPALA NEIGHBORHOOD DELIVERY PAGES
// ============================================
$neighborhoods = [
    'najjera' => 'Najjera', 'ntinda' => 'Ntinda', 'bukoto' => 'Bukoto',
    'kiwatule' => 'Kiwatule', 'kyambogo' => 'Kyambogo', 'makerere' => 'Makerere',
    'wandegeya' => 'Wandegeya', 'kabowa' => 'Kabowa', 'kansanga' => 'Kansanga',
    'buziga' => 'Buziga', 'muyenga' => 'Muyenga', 'naalya' => 'Naalya',
    'kisaasi' => 'Kisaasi', 'bweyogerere' => 'Bweyogerere',
];
foreach ($neighborhoods as $slug => $name) {
    Route::get("/electronics-delivery-{$slug}", function () use ($name) {
        $products = Product::active()->inRandomOrder()->limit(24)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products,
            'h1' => "Electronics Delivery to {$name}",
            'pageTitle' => "Electronics Delivery {$name} | Free Same-Day | Yoola.ug",
            'metaDescription' => "Fast electronics delivery to {$name}, Kampala. TVs, fridges, cookers. Free same-day delivery. Order on WhatsApp.",
        ]);
    })->name("seo.delivery-{$slug}");
}

// ============================================
// PROBLEM / PAIN-POINT PAGES
// ============================================
$problems = [
    'fridge-for-power-cuts-uganda' => [
        'search' => '%fridge%', 'name' => 'Fridges for Power Cuts',
        'intro' => 'Power outages are a daily reality in Uganda. A regular fridge can\'t handle constant on-off cycles. Here\'s what you need.',
        'faqs' => [
            ['q' => 'Which fridge survives power cuts best?', 'a' => '<strong>Inverter fridges</strong> are the answer. They adjust power consumption and handle voltage fluctuations better. Look for Samsung Digital Inverter or LG Smart Inverter technology. These fridges stay cold longer during outages.'],
            ['q' => 'How long does a fridge stay cold without power?', 'a' => '<ul><li><strong>Inverter fridge:</strong> 6-12 hours</li><li><strong>Regular fridge:</strong> 2-4 hours</li></ul>Keep the door closed! Opening it speeds up warming.'],
            ['q' => 'Do I need a voltage stabilizer?', 'a' => '<strong>YES.</strong> Uganda\'s power fluctuates from 180V to 260V. A stabilizer (UGX 50,000-150,000) protects your fridge compressor from burning out. Way cheaper than replacing the fridge.'],
            ['q' => 'What else can I do?', 'a' => '<ul><li>Buy a fridge with thick insulation</li><li>Get a stabilizer (essential)</li><li>Consider a generator or solar setup</li><li>Keep freezer full (frozen items help maintain cold)</li></ul>'],
        ],
    ],
    'electronics-installment-uganda' => [
        'search' => '', 'name' => 'Buy Electronics on Installment',
        'intro' => 'Want a new TV or fridge but can\'t pay all at once? Here are your options in Uganda.',
        'faqs' => [
            ['q' => 'Can I buy electronics on installment in Uganda?', 'a' => '<strong>Yes!</strong> Several options:<ul><li><strong>MTN MoMo Advance:</strong> Get up to UGX 1M credit if you actively use MoMo</li><li><strong>Airtel Money Loan:</strong> Similar to MoMo, based on usage</li><li><strong>Bank loans:</strong> Stanbic, Equity Bank offer consumer electronics loans</li></ul>'],
            ['q' => 'Does Yoola offer installment plans?', 'a' => 'We don\'t offer direct installment but we make it easy:<ul><li>Pay with MoMo/Airtel Money</li><li>Use your mobile money loan service</li><li>We provide proper receipts for bank loan applications</li><li>Cash on delivery available in Kampala</li></ul>'],
            ['q' => 'How does MTN MoMo Advance work?', 'a' => '<ol><li>Dial *165# on MTN</li><li>Select "Loans and Savings"</li><li>Check your MoMo Advance limit</li><li>If eligible, borrow instantly</li><li>Pay us via MoMo with the loan</li><li>Repay MTN over 30 days</li></ol>'],
            ['q' => 'Can I pay in two installments?', 'a' => 'Yes! Pay 50% deposit and 50% on delivery. Contact us to arrange. WhatsApp: 0704 229 768.'],
        ],
    ],
    'electronics-on-credit-uganda' => [
        'search' => '', 'name' => 'Electronics on Credit',
        'intro' => 'Looking for electronics on credit in Uganda? Compare your options and find the best way to buy now, pay later.',
        'faqs' => [
            ['q' => 'Where to get electronics on credit?', 'a' => '<strong>Top options in Uganda:</strong><ul><li><strong>MTN MoMo Advance:</strong> Instant mobile loan</li><li><strong>Airtel Money:</strong> Similar service</li><li><strong>Stanbic Bank:</strong> Personal loans for electronics</li><li><strong>Equity Bank:</strong> Asset financing</li><li><strong>Some SACCOs:</strong> Lower interest, longer approval</li></ul>'],
            ['q' => 'What documents do I need?', 'a' => '<ul><li>National ID</li><li>Proof of income (payslip or bank statement)</li><li>Proforma invoice from us (we provide this)</li></ul>'],
            ['q' => 'Is it safe to buy on credit?', 'a' => 'Yes, when buying from a trusted store. At Yoola, all products are genuine with warranty. We provide proper documentation for your loan application. Avoid roadside vendors who may sell fakes on credit.'],
            ['q' => 'How to get started?', 'a' => '<ol><li>Browse our products and pick what you want</li><li>WhatsApp us: 0704 229 768</li><li>We send a proforma invoice</li><li>Take it to your bank or use mobile money loan</li><li>Pay and we deliver!</li></ol>'],
        ],
    ],
];
foreach ($problems as $slug => $p) {
    Route::get("/{$slug}", function () use ($p) {
        $query = Product::active();
        if ($p['search']) $query->where('name', 'LIKE', $p['search']);
        $products = $query->orderBy('unit_price')->limit(12)->get();
        return view('theme-views.pages.seo.helpful-faq', [
            'h1' => $p['name'] . ' (2026 Guide)',
            'pageTitle' => $p['name'] . ' | Complete Guide Uganda',
            'metaDescription' => $p['name'] . ' in Uganda. ' . \Illuminate\Support\Str::limit(strip_tags($p['intro']), 140),
            'intro' => $p['intro'],
            'faqs' => $p['faqs'],
            'products' => $products,
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// ACCESSORIES PAGES
// ============================================
$accessories = [
    'tv-wall-mount-uganda' => ['search' => '%wall mount%', 'name' => 'TV Wall Mounts'],
    'tv-stand-uganda' => ['search' => '%tv stand%', 'name' => 'TV Stands'],
    'voltage-stabilizer-uganda' => ['search' => '%stabilizer%', 'name' => 'Voltage Stabilizers'],
    'tv-bracket-uganda' => ['search' => '%bracket%', 'name' => 'TV Brackets'],
    'extension-cable-uganda' => ['search' => '%extension%', 'name' => 'Extension Cables'],
    'hdmi-cable-uganda' => ['search' => '%hdmi%', 'name' => 'HDMI Cables'],
];
foreach ($accessories as $slug => $acc) {
    Route::get("/{$slug}", function () use ($acc) {
        $products = Product::active()->where('name', 'LIKE', $acc['search'])->orderBy('unit_price')->limit(24)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products,
            'h1' => $acc['name'] . ' Prices Uganda',
            'pageTitle' => $acc['name'] . ' Prices Uganda 2026 | Buy Online',
            'metaDescription' => 'Buy ' . $acc['name'] . ' in Uganda. Best prices, genuine products. Free delivery Kampala at Yoola.ug',
        ]);
    })->name("seo.{$slug}");
}

// ============================================
// LG TV MODEL PAGES
// ============================================
$lgModels = [
    'lg-32-inch-tv-price' => ['search' => '%lg%32%', 'name' => 'LG 32 Inch TV'],
    'lg-43-inch-tv-price' => ['search' => '%lg%43%', 'name' => 'LG 43 Inch TV'],
    'lg-55-inch-tv-price' => ['search' => '%lg%55%', 'name' => 'LG 55 Inch TV'],
    'lg-65-inch-tv-price' => ['search' => '%lg%65%', 'name' => 'LG 65 Inch TV'],
];
foreach ($lgModels as $slug => $m) {
    Route::get("/{$slug}-uganda", function () use ($m) {
        $products = Product::active()->where('name', 'LIKE', $m['search'])->where('name', 'LIKE', '%tv%')->orderBy('unit_price')->limit(20)->get();
        return view('theme-views.pages.seo.tv-listing', [
            'products' => $products,
            'h1' => "{$m['name']} Price in Uganda",
            'pageTitle' => "{$m['name']} Price Uganda 2026 | Buy Online",
            'metaDescription' => "Buy {$m['name']} in Uganda. OLED, 4K Smart TV. Best price, genuine warranty. Free delivery at Yoola.ug",
        ]);
    })->name("seo.{$slug}");
}
