<?php
use Illuminate\Support\Facades\Route;
use App\Models\Product;

Route::get('/how-to-spot-fake-tv-uganda', function () {
    return view('theme-views.pages.seo.helpful-faq', [
        'h1' => 'How to Spot Fake TVs in Uganda (2026 Guide)',
        'pageTitle' => 'How to Spot Fake TVs Uganda | Avoid Scams',
        'metaDescription' => 'Learn to identify fake TVs in Uganda. Protect your money from scams.',
        'intro' => 'Fake TVs are everywhere in Uganda. They look real but die within months. This guide helps you avoid wasting money.',
        'solution' => ['title' => 'Buy from Yoola - 100% Genuine', 'text' => 'All TVs verified from authorized distributors with real warranty.'],
        'faqs' => [
            ['q' => 'Why are there so many fake TVs in Uganda?', 'a' => 'Counterfeit electronics flood Uganda because they are cheap to produce. They look identical to real TVs but use components that fail quickly.'],
            ['q' => 'How can I tell if a TV is fake?', 'a' => '<strong>Warning signs:</strong><ul><li>Price 50%+ below market rate</li><li>No proper warranty card</li><li>Misspelled brand names (Samsvng, Hisence)</li><li>Seller refuses receipt</li><li>Pressure to buy "today only"</li></ul>'],
            ['q' => 'What happens if I buy a fake?', 'a' => '<ul><li>Poor picture quality from day one</li><li>TV fails within 3-6 months</li><li>No warranty - seller disappears</li><li>Your money is lost</li></ul>'],
            ['q' => 'How do I verify a TV is original?', 'a' => '<ol><li>Check serial number matches box</li><li>Look for official warranty card</li><li>Buy from authorized dealers only</li><li>Always get a receipt</li></ol>'],
            ['q' => 'Where should I buy?', 'a' => 'Buy from authorized dealers:<ul><li><strong>Yoola.ug</strong> - All products verified genuine</li><li>Official brand showrooms</li></ul>Avoid roadside vendors and random Facebook sellers.'],
        ],
        'products' => Product::active()->where('name', 'LIKE', '%tv%')->orderBy('unit_price')->limit(8)->get(),
    ]);
})->name('faq.fake-tv');

Route::get('/tv-buying-guide-uganda', function () {
    return view('theme-views.pages.seo.helpful-faq', [
        'h1' => 'TV Buying Guide Uganda 2026',
        'pageTitle' => 'TV Buying Guide Uganda | What Size & Type',
        'metaDescription' => 'Complete guide to buying a TV in Uganda. Size, Smart vs Digital, best brands.',
        'intro' => 'Buying a TV in Uganda can be confusing. This guide helps you choose right.',
        'faqs' => [
            ['q' => 'What size TV for my room?', 'a' => '<strong>Room size guide:</strong><ul><li>Small room (3x3m): 32-43 inch</li><li>Medium room (4x4m): 43-55 inch</li><li>Large room (5x5m+): 55-75 inch</li></ul><p>Sit 1.5x the screen diagonal away for best viewing.</p>'],
            ['q' => 'Smart TV vs Digital TV?', 'a' => '<strong>Smart TV:</strong> Has WiFi, Netflix, YouTube. Get this if you have internet.<br><strong>Digital TV:</strong> Cheaper, local channels only. Good if no internet.'],
            ['q' => 'Which brand is best?', 'a' => '<ul><li><strong>Premium:</strong> Samsung, LG</li><li><strong>Value:</strong> Hisense</li><li><strong>Budget:</strong> CHiQ, TCL</li></ul>'],
            ['q' => 'How much should I budget?', 'a' => '<ul><li>32" Digital: UGX 400-600K</li><li>43" Smart: UGX 800K-1.2M</li><li>55" 4K Smart: UGX 1.5-2.5M</li></ul>'],
        ],
        'products' => Product::active()->where('name', 'LIKE', '%tv%')->orderBy('unit_price')->limit(8)->get(),
    ]);
})->name('faq.tv-guide');

Route::get('/fridge-buying-guide-uganda', function () {
    return view('theme-views.pages.seo.helpful-faq', [
        'h1' => 'Fridge Buying Guide Uganda 2026',
        'pageTitle' => 'Fridge Buying Guide Uganda | Size & Brand',
        'metaDescription' => 'How to choose the right fridge in Uganda. Size guide, brands, energy tips.',
        'intro' => 'A fridge runs 24/7 for years. Get it right the first time.',
        'faqs' => [
            ['q' => 'What size fridge do I need?', 'a' => '<ul><li>1-2 people: 100-200L</li><li>3-4 people: 200-350L</li><li>5+ people: 350-500L</li></ul>Add 50L if you shop weekly.'],
            ['q' => 'Single vs Double door?', 'a' => '<strong>Single door:</strong> Cheaper, compact. Good for small families.<br><strong>Double door:</strong> More space, better organization. Good for 3+ people.'],
            ['q' => 'How much electricity?', 'a' => '<ul><li>Small fridge: ~UGX 30-40K/month</li><li>Medium: ~UGX 40-60K/month</li><li>Large: ~UGX 60-90K/month</li></ul>Get Inverter fridges to save money!'],
            ['q' => 'Do I need a stabilizer?', 'a' => '<strong>YES!</strong> Uganda has power fluctuations. A stabilizer (UGX 50-150K) protects your fridge from burning out.'],
        ],
        'products' => Product::active()->where('name', 'LIKE', '%fridge%')->orderBy('unit_price')->limit(8)->get(),
    ]);
})->name('faq.fridge-guide');

Route::get('/mobile-money-electronics-uganda', function () {
    return view('theme-views.pages.seo.helpful-faq', [
        'h1' => 'Buy Electronics with Mobile Money',
        'pageTitle' => 'Mobile Money Electronics Uganda | MTN MoMo',
        'metaDescription' => 'Pay for electronics with MTN Mobile Money in Uganda. Safe and fast.',
        'intro' => 'No need to carry cash. Pay securely with your phone.',
        'faqs' => [
            ['q' => 'What payment do you accept?', 'a' => '<ul><li>MTN Mobile Money ✓</li><li>Airtel Money ✓</li><li>Bank Transfer ✓</li><li>Cash on Delivery ✓</li></ul>'],
            ['q' => 'How does it work?', 'a' => '<ol><li>WhatsApp us: 0704 229 768</li><li>We send payment details</li><li>Pay via MoMo/Airtel</li><li>Share screenshot</li><li>We deliver same day!</li></ol>'],
            ['q' => 'Is it safe?', 'a' => 'Very safe! You control the transaction. Digital proof on your phone.'],
            ['q' => 'Can I pay on delivery?', 'a' => 'Yes! Cash on delivery in Kampala & Wakiso (free delivery).'],
        ],
        'products' => Product::active()->inRandomOrder()->limit(8)->get(),
    ]);
})->name('faq.mobile-money');

Route::get('/electronics-delivery-uganda', function () {
    return view('theme-views.pages.seo.helpful-faq', [
        'h1' => 'Electronics Delivery Across Uganda',
        'pageTitle' => 'Electronics Delivery Uganda | Free Kampala',
        'metaDescription' => 'Fast electronics delivery. Free in Kampala. Nationwide shipping.',
        'intro' => 'We deliver safely to your doorstep.',
        'faqs' => [
            ['q' => 'Where do you deliver?', 'a' => '<ul><li>Kampala & Wakiso: FREE, same/next day</li><li>Mukono, Entebbe: UGX 10-30K</li><li>Major towns: Via bus</li><li>Everywhere in Uganda!</li></ul>'],
            ['q' => 'How long?', 'a' => '<ul><li>Kampala: Same/next day</li><li>Major towns: 2-4 days</li><li>Remote: 3-7 days</li></ul>'],
            ['q' => 'What if item arrives damaged?', 'a' => 'Take photos, WhatsApp us within 24h. We replace or refund - no arguments!'],
        ],
        'products' => Product::active()->inRandomOrder()->limit(8)->get(),
    ]);
})->name('faq.delivery');

Route::get('/electronics-warranty-uganda', function () {
    return view('theme-views.pages.seo.helpful-faq', [
        'h1' => 'Electronics Warranty Guide Uganda',
        'pageTitle' => 'Warranty Guide Uganda | Yoola',
        'metaDescription' => 'Understand warranty on electronics in Uganda.',
        'intro' => 'Every Yoola product has real warranty.',
        'faqs' => [
            ['q' => 'What warranty do products have?', 'a' => '<ul><li>TVs: 1-2 years</li><li>Fridges: 1-2 years (some 10yr compressor)</li><li>Small appliances: 6mo-1yr</li></ul>'],
            ['q' => 'What is covered?', 'a' => '<strong>Covered:</strong> Manufacturing defects, component failures.<br><strong>Not covered:</strong> Physical damage, power surge damage.'],
            ['q' => 'How to claim?', 'a' => '<ol><li>WhatsApp us with receipt</li><li>Describe the problem</li><li>We troubleshoot or arrange repair</li></ol>'],
        ],
        'products' => Product::active()->inRandomOrder()->limit(8)->get(),
    ]);
})->name('faq.warranty');

Route::get('/tired-of-fake-electronics-uganda', function () {
    return view('theme-views.pages.seo.helpful-faq', [
        'h1' => 'Tired of Fake Electronics? Solution Here',
        'pageTitle' => 'Tired of Fake Electronics Uganda | Buy Genuine',
        'metaDescription' => 'Frustrated with fake electronics? Yoola sells only genuine products.',
        'intro' => 'We understand. Too many Ugandans have lost money to fakes.',
        'solution' => ['title' => 'Yoola: 100% Genuine Only', 'text' => 'Every item from authorized distributors. Real warranty. Real support.'],
        'faqs' => [
            ['q' => 'Why do fakes fail fast?', 'a' => 'Cheap components, no quality control. Built to look good, not to last.'],
            ['q' => 'How does Yoola guarantee genuine?', 'a' => '<ul><li>Direct from authorized distributors</li><li>Real warranty cards</li><li>Serial numbers verified</li></ul>'],
            ['q' => 'But genuine costs more...', 'a' => 'Fakes cost MORE long-term!<br>Fake TV 400K dies in 6mo = wasted.<br>Genuine 600K lasts 5+ years = smart!'],
        ],
        'products' => Product::active()->inRandomOrder()->limit(8)->get(),
    ]);
})->name('problem.fake');

Route::get('/faqs', function () {
    return view('theme-views.pages.seo.helpful-faq', [
        'h1' => 'Help Center - Your Questions Answered',
        'pageTitle' => 'FAQs Uganda | Yoola Help Center',
        'metaDescription' => 'Common questions about buying electronics in Uganda.',
        'intro' => 'Quick answers to your questions.',
        'faqs' => [
            ['q' => 'Are products genuine?', 'a' => 'Yes, 100%! All from authorized distributors with warranty.'],
            ['q' => 'Accept Mobile Money?', 'a' => 'Yes! MTN MoMo, Airtel Money, bank transfer, cash on delivery.'],
            ['q' => 'Deliver outside Kampala?', 'a' => 'Yes! Free in Kampala/Wakiso. Affordable rates nationwide.'],
            ['q' => 'What warranty?', 'a' => 'Manufacturer warranty 1-2 years. We handle all claims.'],
            ['q' => 'How to order?', 'a' => 'WhatsApp us: <strong>0704 229 768</strong>'],
        ],
        'products' => Product::active()->inRandomOrder()->limit(8)->get(),
    ]);
})->name('faqs');
