<?php
/**
 * Sitemap Generator & Site Analyzer for Yoola.ug
 * Run: php generate-sitemap.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\PricePage;

$baseUrl = 'https://yoola.ug';
$urls = [];
$issues = [];

echo "🔍 YOOLA.UG SITE ANALYSIS\n";
echo "========================\n\n";

// 1. Get all products
$products = Product::where('status', 1)->get();
echo "📦 Products: " . $products->count() . "\n";

$productsWithoutDescription = 0;
$productsWithoutImages = 0;
$productsWithoutCategory = 0;
$productsWithoutBrand = 0;

foreach ($products as $product) {
    $urls[] = [
        'loc' => $baseUrl . '/product/' . $product->slug,
        'lastmod' => $product->updated_at->format('Y-m-d'),
        'priority' => '0.8',
        'changefreq' => 'weekly'
    ];
    
    // Check for issues
    if (empty($product->details) || strlen(strip_tags($product->details)) < 50) {
        $productsWithoutDescription++;
    }
    if (empty($product->images) || $product->images == '[]') {
        $productsWithoutImages++;
    }
    if (empty($product->category_id)) {
        $productsWithoutCategory++;
    }
    if (empty($product->brand_id)) {
        $productsWithoutBrand++;
    }
}

// 2. Get all categories
$categories = Category::all();
echo "📁 Categories: " . $categories->count() . "\n";

foreach ($categories as $category) {
    $urls[] = [
        'loc' => $baseUrl . '/products?category=' . $category->slug,
        'lastmod' => $category->updated_at ? $category->updated_at->format('Y-m-d') : date('Y-m-d'),
        'priority' => '0.7',
        'changefreq' => 'weekly'
    ];
}

// 3. Get all brands
$brands = Brand::all();
echo "🏷️ Brands: " . $brands->count() . "\n";

foreach ($brands as $brand) {
    $urls[] = [
        'loc' => $baseUrl . '/products?brand=' . $brand->slug,
        'lastmod' => $brand->updated_at ? $brand->updated_at->format('Y-m-d') : date('Y-m-d'),
        'priority' => '0.6',
        'changefreq' => 'weekly'
    ];
}

// 4. Get price pages
$pricePages = PricePage::where('is_active', 1)->get();
echo "💰 Price Pages: " . $pricePages->count() . "\n";

foreach ($pricePages as $page) {
    $urls[] = [
        'loc' => $baseUrl . '/prices/' . $page->slug,
        'lastmod' => $page->updated_at->format('Y-m-d'),
        'priority' => '0.9',
        'changefreq' => 'monthly'
    ];
}

// 5. Static pages
$staticPages = [
    ['loc' => $baseUrl, 'priority' => '1.0', 'changefreq' => 'daily'],
    ['loc' => $baseUrl . '/prices', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['loc' => $baseUrl . '/about-us', 'priority' => '0.5', 'changefreq' => 'monthly'],
    ['loc' => $baseUrl . '/contact-us', 'priority' => '0.5', 'changefreq' => 'monthly'],
    ['loc' => $baseUrl . '/terms', 'priority' => '0.3', 'changefreq' => 'yearly'],
    ['loc' => $baseUrl . '/privacy-policy', 'priority' => '0.3', 'changefreq' => 'yearly'],
];

foreach ($staticPages as $page) {
    $page['lastmod'] = date('Y-m-d');
    $urls[] = $page;
}

echo "\n📊 TOTAL URLs: " . count($urls) . "\n\n";

// Generate XML Sitemap
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($urls as $url) {
    $xml .= "  <url>\n";
    $xml .= "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
    $xml .= "    <lastmod>" . $url['lastmod'] . "</lastmod>\n";
    $xml .= "    <changefreq>" . $url['changefreq'] . "</changefreq>\n";
    $xml .= "    <priority>" . $url['priority'] . "</priority>\n";
    $xml .= "  </url>\n";
}

$xml .= '</urlset>';

$filename = 'sitemap-' . date('Y-m-d-His') . '.xml';
$filepath = __DIR__ . '/storage/app/public/sitemap/' . $filename;
file_put_contents($filepath, $xml);

echo "✅ Sitemap generated: storage/app/public/sitemap/$filename\n";
echo "   Size: " . round(strlen($xml) / 1024, 1) . " KB\n\n";

// Analysis Report
echo "🔍 ISSUES FOUND\n";
echo "===============\n\n";

echo "⚠️ Products without proper description: $productsWithoutDescription\n";
echo "⚠️ Products without images: $productsWithoutImages\n";
echo "⚠️ Products without category: $productsWithoutCategory\n";
echo "⚠️ Products without brand: $productsWithoutBrand\n\n";

// Check for SEO issues
echo "📋 SEO CHECKLIST\n";
echo "================\n\n";

$checks = [
    ['Product Schema', file_exists(__DIR__ . '/resources/themes/theme_aster/theme-views/partials/_product-schema.blade.php')],
    ['Organization Schema', file_exists(__DIR__ . '/resources/themes/theme_aster/theme-views/partials/_organization-schema.blade.php')],
    ['Exit Popup', file_exists(__DIR__ . '/resources/themes/theme_aster/theme-views/partials/_exit-popup.blade.php')],
    ['Trust Strip', file_exists(__DIR__ . '/resources/themes/theme_aster/theme-views/partials/_trust-strip.blade.php')],
    ['Price Pages Controller', file_exists(__DIR__ . '/app/Http/Controllers/Web/PricePageController.php')],
    ['Price Pages Model', file_exists(__DIR__ . '/app/Models/PricePage.php')],
];

foreach ($checks as $check) {
    $status = $check[1] ? '✅' : '❌';
    echo "$status {$check[0]}\n";
}

echo "\n";

// What's missing
echo "❌ WHAT'S MISSING FOR SEO\n";
echo "=========================\n\n";

$missing = [];

// Check robots.txt
if (!file_exists(__DIR__ . '/public/robots.txt')) {
    $missing[] = "robots.txt in public folder";
}

// Check for review schema (would need to check blade files)
$missing[] = "Review schema on product pages (shows ⭐ in Google)";
$missing[] = "FAQ schema on product pages";
$missing[] = "Blog integration with main site";
$missing[] = "Internal linking between price pages and products";
$missing[] = "Image alt tags optimization";
$missing[] = "Page speed optimization (lazy loading, minification)";

foreach ($missing as $item) {
    echo "• $item\n";
}

echo "\n✨ RECOMMENDATIONS\n";
echo "==================\n\n";
echo "1. Deploy the new schema markup to production\n";
echo "2. Submit sitemap to Google Search Console\n";
echo "3. Add FAQ schema to high-value product pages\n";
echo "4. Create internal links from price pages to products\n";
echo "5. Optimize images (WebP format, lazy loading)\n";
echo "6. Add review prompts to increase review count\n";

echo "\n🎯 Done!\n";
