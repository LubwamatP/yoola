<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Modules\Blog\Entities\Blog;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {--ping : Ping Google and Bing after generation}';
    protected $description = 'Generate comprehensive XML sitemap for Yoola.ug';

    protected string $baseUrl;

    public function handle()
    {
        $this->baseUrl = rtrim(config('app.url'), '/');
        $publicPath = public_path();

        $this->info('Generating Yoola.ug sitemaps...');

        // 1. Products
        $this->generateSitemap(
            $publicPath . '/sitemap-products.xml',
            $this->getProductUrls()
        );

        // 2. Categories
        $this->generateSitemap(
            $publicPath . '/sitemap-categories.xml',
            $this->getCategoryUrls()
        );

        // 3. Brands
        $this->generateSitemap(
            $publicPath . '/sitemap-brands.xml',
            $this->getBrandUrls()
        );

        // 4. Blog
        if (class_exists(Blog::class)) {
            $this->generateSitemap(
                $publicPath . '/sitemap-blog.xml',
                $this->getBlogUrls()
            );
        }

        // 5. Core pages + SEO landing pages
        $this->generateSitemap(
            $publicPath . '/sitemap-seo.xml',
            $this->getSeoUrls()
        );

        // 6. Price pages from tools.php
        $this->generateSitemap(
            $publicPath . '/sitemap-prices.xml',
            $this->getPriceUrls()
        );

        // 7. Static pages
        $this->generateSitemap(
            $publicPath . '/sitemap-pages.xml',
            $this->getStaticUrls()
        );

        // 8. Generate index
        $this->generateIndex($publicPath . '/sitemap-index.xml');

        $this->info('Done! Sitemap-index.xml generated.');
        $this->info('Submit: ' . $this->baseUrl . '/sitemap-index.xml');

        if ($this->option('ping')) {
            $this->pingGoogle();
            $this->pingBing();
        }

        return 0;
    }

    protected function generateSitemap(string $path, array $urls): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . $this->escape($url['loc']) . "</loc>\n";
            $xml .= "    <lastmod>" . ($url['lastmod'] ?? date('Y-m-d')) . "</lastmod>\n";
            $xml .= "    <changefreq>" . ($url['changefreq'] ?? 'weekly') . "</changefreq>\n";
            $xml .= "    <priority>" . number_format($url['priority'] ?? 0.5, 1) . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';
        file_put_contents($path, $xml);
        $this->info('  ' . basename($path) . ': ' . count($urls) . ' URLs');
    }

    protected function generateIndex(string $path): void
    {
        $files = glob(public_path('sitemap-*.xml'));
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($files as $file) {
            if (basename($file) === 'sitemap-index.xml') continue;
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>" . $this->escape($this->baseUrl . '/' . basename($file)) . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';
        file_put_contents($path, $xml);
    }

    protected function getProductUrls(): array
    {
        $urls = [];
        Product::where('status', 1)->chunk(200, function ($products) use (&$urls) {
            foreach ($products as $p) {
                $urls[] = [
                    'loc' => $this->baseUrl . '/product/' . $p->slug,
                    'lastmod' => $p->updated_at->format('Y-m-d'),
                    'changefreq' => 'weekly',
                    'priority' => 0.8,
                ];
            }
        });
        return $urls;
    }

    protected function getCategoryUrls(): array
    {
        $urls = [];
        Category::chunk(200, function ($cats) use (&$urls) {
            foreach ($cats as $c) {
                $urls[] = [
                    'loc' => $this->baseUrl . '/category/' . $c->slug,
                    'lastmod' => $c->updated_at->format('Y-m-d'),
                    'changefreq' => 'weekly',
                    'priority' => 0.7,
                ];
            }
        });
        return $urls;
    }

    protected function getBrandUrls(): array
    {
        $urls = [];
        Brand::where('status', 1)->chunk(200, function ($brands) use (&$urls) {
            foreach ($brands as $b) {
                $urls[] = [
                    'loc' => $this->baseUrl . '/products?brand=' . $b->slug,
                    'lastmod' => date('Y-m-d'),
                    'changefreq' => 'monthly',
                    'priority' => 0.5,
                ];
            }
        });
        return $urls;
    }

    protected function getBlogUrls(): array
    {
        $urls = [];
        Blog::where('status', 1)->chunk(200, function ($blogs) use (&$urls) {
            foreach ($blogs as $b) {
                $urls[] = [
                    'loc' => $this->baseUrl . '/blog/' . $b->slug,
                    'lastmod' => $b->updated_at->format('Y-m-d'),
                    'changefreq' => 'monthly',
                    'priority' => 0.6,
                ];
            }
        });
        return $urls;
    }

    protected function getSeoUrls(): array
    {
        $base = $this->baseUrl;
        $today = date('Y-m-d');
        $urls = [];

        // ===== TV SEO =====
        $tvPillar = ['/smart-tv-prices-uganda'];
        $tvPrices = ['tvs-under-500k-uganda', 'tvs-under-1m-uganda', 'tvs-under-2m-uganda', 'tvs-under-3m-uganda'];
        $tvSizes = [32, 43, 50, 55, 65, 75];
        $tvBrands = ['chiq', 'samsung', 'hisense', 'lg', 'tcl'];

        foreach ($tvPillar as $u) $urls[] = ['loc' => "$base$u", 'changefreq' => 'weekly', 'priority' => 0.7];
        foreach ($tvPrices as $u) $urls[] = ['loc' => "$base/$u", 'changefreq' => 'weekly', 'priority' => 0.7];
        foreach ($tvSizes as $s) $urls[] = ['loc' => "$base/$s-inch-tv-uganda", 'changefreq' => 'weekly', 'priority' => 0.7];
        foreach ($tvBrands as $b) $urls[] = ['loc' => "$base/$b-tv-prices-uganda", 'changefreq' => 'weekly', 'priority' => 0.7];

        // ===== FRIDGE SEO =====
        $fridgePriceBands = ['fridges-under-500k-uganda', 'fridges-under-1m-uganda', 'fridges-under-2m-uganda', 'fridges-under-3m-uganda'];
        $fridgeTypes = ['refrigerators-uganda', 'deep-freezers-uganda', 'mini-fridges-uganda', 'chest-freezers-uganda', 'beverage-coolers-uganda'];
        $fridgeBrands = ['samsung', 'hisense', 'chiq', 'adh', 'lg'];
        $fridgeKeywords = ['small-fridge-uganda', 'medium-fridge-uganda', 'large-fridge-uganda', 'double-door-fridge-uganda', 'side-by-side-fridge-uganda'];

        $urls[] = ['loc' => "$base/fridge-prices-uganda", 'changefreq' => 'weekly', 'priority' => 0.7];
        foreach ($fridgePriceBands as $u) $urls[] = ['loc' => "$base/$u", 'changefreq' => 'weekly', 'priority' => 0.7];
        foreach ($fridgeTypes as $u) $urls[] = ['loc' => "$base/$u", 'changefreq' => 'weekly', 'priority' => 0.7];
        foreach ($fridgeBrands as $b) $urls[] = ['loc' => "$base/$b-fridge-prices-uganda", 'changefreq' => 'weekly', 'priority' => 0.7];
        foreach ($fridgeKeywords as $u) $urls[] = ['loc' => "$base/$u", 'changefreq' => 'weekly', 'priority' => 0.7];

        // ===== ALL-SEO =====
        $allPillars = ['washing-machine-prices-uganda', 'cooker-prices-uganda', 'air-conditioner-prices-uganda', 'speaker-prices-uganda'];
        foreach ($allPillars as $u) $urls[] = ['loc' => "$base/$u", 'changefreq' => 'weekly', 'priority' => 0.7];

        $allSubs = [
            'front-loader-washing-machine-uganda', 'top-loader-washing-machine-uganda', 'clothes-dryer-prices-uganda',
            'washer-dryer-combo-uganda', 'samsung-washing-machine-uganda', 'lg-washing-machine-uganda', 'hisense-washing-machine-uganda',
            'gas-cooker-prices-uganda', 'electric-cooker-prices-uganda', 'built-in-oven-prices-uganda', 'microwave-prices-uganda',
            'hot-plate-prices-uganda', 'induction-cooker-uganda',
            'split-ac-prices-uganda', 'portable-ac-prices-uganda', 'samsung-ac-prices-uganda', 'hisense-ac-prices-uganda',
            'soundbar-prices-uganda', 'bluetooth-speaker-prices-uganda', 'party-speaker-prices-uganda', 'home-theatre-prices-uganda',
            'jbl-speaker-prices-uganda', 'samsung-soundbar-prices-uganda', 'headphones-prices-uganda', 'earbuds-prices-uganda',
            'blender-prices-uganda', 'electric-kettle-prices-uganda', 'coffee-maker-prices-uganda', 'air-fryer-prices-uganda',
            'juicer-prices-uganda', 'food-processor-prices-uganda', 'toaster-prices-uganda', 'sandwich-maker-prices-uganda',
            'rice-cooker-prices-uganda', 'pressure-cooker-prices-uganda', 'water-dispenser-prices-uganda',
            'fan-prices-uganda', 'standing-fan-prices-uganda', 'ceiling-fan-prices-uganda', 'iron-prices-uganda',
            'steam-iron-prices-uganda', 'vacuum-cleaner-prices-uganda',
            'hair-clipper-prices-uganda', 'trimmer-prices-uganda', 'hair-dryer-prices-uganda',
        ];
        foreach ($allSubs as $u) $urls[] = ['loc' => "$base/$u", 'changefreq' => 'weekly', 'priority' => 0.6];

        // ===== SEO-BOOST =====
        $locations = ['kampala', 'wakiso', 'entebbe', 'jinja', 'mbarara', 'gulu', 'mukono', 'mbale', 'lira', 'masaka', 'fort-portal', 'arua'];
        $locCats = ['electronics', 'tv', 'fridge', 'cooker', 'washing-machine', 'air-conditioner'];
        foreach ($locations as $l) {
            foreach ($locCats as $c) {
                $urls[] = ['loc' => "$base/buy-$c-$l", 'changefreq' => 'monthly', 'priority' => 0.4];
            }
        }

        $comparisons = [
            'samsung-vs-hisense-tv', 'samsung-vs-lg-tv', 'hisense-vs-chiq-tv', 'samsung-vs-hisense-fridge',
            'samsung-vs-lg-washing-machine', 'front-loader-vs-top-loader', 'split-ac-vs-portable-ac',
            'gas-cooker-vs-electric-cooker', 'soundbar-vs-home-theatre', 'chest-freezer-vs-upright-freezer',
            'tcl-vs-hisense-tv', 'tcl-vs-samsung-tv', 'tcl-vs-chiq-tv',
        ];
        foreach ($comparisons as $c) $urls[] = ['loc' => "$base/$c-uganda", 'changefreq' => 'monthly', 'priority' => 0.5];

        $models = [
            'hisense-32-inch-tv-price', 'hisense-43-inch-tv-price', 'hisense-55-inch-tv-price',
            'samsung-32-inch-tv-price', 'samsung-43-inch-tv-price', 'samsung-55-inch-tv-price', 'samsung-65-inch-tv-price',
            'chiq-32-inch-tv-price', 'chiq-43-inch-tv-price', 'chiq-55-inch-tv-price',
            'lg-43-inch-tv-price', 'lg-55-inch-tv-price', 'lg-32-inch-tv-price', 'lg-65-inch-tv-price',
            'samsung-double-door-fridge-price', 'hisense-double-door-fridge-price', 'samsung-side-by-side-fridge-price',
            'samsung-front-loader-price', 'lg-front-loader-price', 'samsung-top-loader-price',
            'samsung-split-ac-price', 'hisense-split-ac-price',
            'jbl-partybox-price', 'samsung-soundbar-price',
        ];
        foreach ($models as $m) $urls[] = ['loc' => "$base/$m-uganda", 'changefreq' => 'weekly', 'priority' => 0.6];

        // TCL models
        for ($s = 0; $s < count($tvSizes); $s++) {
            $urls[] = ['loc' => "$base/tcl-{$tvSizes[$s]}-inch-tv-uganda", 'changefreq' => 'weekly', 'priority' => 0.6];
        }
        $tclModels = ['tcl-p635-price', 'tcl-c645-price', 'tcl-c745-price'];
        foreach ($tclModels as $m) $urls[] = ['loc' => "$base/$m-uganda", 'changefreq' => 'weekly', 'priority' => 0.6];
        $tclPrices = ['tcl-tvs-under-500k-uganda', 'tcl-tvs-under-1m-uganda', 'tcl-tvs-under-2m-uganda'];
        foreach ($tclPrices as $u) $urls[] = ['loc' => "$base/$u", 'changefreq' => 'weekly', 'priority' => 0.7];
        $urls[] = ['loc' => "$base/tcl-tv-prices-uganda", 'changefreq' => 'weekly', 'priority' => 0.7];

        $deals = ['electronics-deals-uganda', 'tv-deals-kampala', 'cheap-electronics-uganda', 'electronics-sale-uganda', 'best-electronics-deals-2026'];
        foreach ($deals as $d) $urls[] = ['loc' => "$base/$d", 'changefreq' => 'weekly', 'priority' => 0.6];

        $questions = ['where-to-buy-tv-uganda', 'where-to-buy-fridge-uganda', 'best-electronics-shop-uganda', 'original-tv-uganda', 'genuine-electronics-uganda'];
        foreach ($questions as $q) $urls[] = ['loc' => "$base/$q", 'changefreq' => 'monthly', 'priority' => 0.5];

        $brandGood = ['is-hisense-tv-good', 'is-samsung-tv-good-uganda', 'is-lg-fridge-good-uganda', 'is-hisense-fridge-good'];
        foreach ($brandGood as $b) $urls[] = ['loc' => "$base/$b", 'changefreq' => 'monthly', 'priority' => 0.5];

        $bestHubs = ['best-fridge-uganda', 'best-washing-machine-uganda', 'best-air-conditioner-uganda', 'best-cooker-uganda'];
        foreach ($bestHubs as $b) $urls[] = ['loc' => "$base/$b", 'changefreq' => 'monthly', 'priority' => 0.6];

        // Neighborhood delivery
        $hoods = ['najjera', 'ntinda', 'bukoto', 'kiwatule', 'kyambogo', 'makerere', 'wandegeya', 'kabowa', 'kansanga', 'buziga', 'muyenga', 'naalya', 'kisaasi', 'bweyogerere'];
        foreach ($hoods as $h) $urls[] = ['loc' => "$base/electronics-delivery-$h", 'changefreq' => 'monthly', 'priority' => 0.4];

        // Accessories
        $acc = ['tv-wall-mount-uganda', 'tv-stand-uganda', 'voltage-stabilizer-uganda', 'tv-bracket-uganda', 'extension-cable-uganda', 'hdmi-cable-uganda'];
        foreach ($acc as $a) $urls[] = ['loc' => "$base/$a", 'changefreq' => 'monthly', 'priority' => 0.5];

        // Problem pages
        $probs = ['fridge-for-power-cuts-uganda', 'electronics-installment-uganda', 'electronics-on-credit-uganda'];
        foreach ($probs as $p) $urls[] = ['loc' => "$base/$p", 'changefreq' => 'monthly', 'priority' => 0.6];

        // FAQ pages
        $faqs = [
            'how-to-spot-fake-tv-uganda', 'tv-buying-guide-uganda', 'fridge-buying-guide-uganda',
            'mobile-money-electronics-uganda', 'electronics-delivery-uganda', 'electronics-warranty-uganda',
            'tired-of-fake-electronics-uganda', 'faqs', 'is-chiq-tv-good-brand',
        ];
        foreach ($faqs as $f) $urls[] = ['loc' => "$base/$f", 'changefreq' => 'monthly', 'priority' => 0.6];

        // Set lastmod
        foreach ($urls as &$u) {
            if (!isset($u['lastmod'])) $u['lastmod'] = $today;
        }

        return $urls;
    }

    protected function getPriceUrls(): array
    {
        $base = $this->baseUrl;
        $today = date('Y-m-d');
        $urls = [];

        $prices = [
            'prices/32-inch-tv-uganda', 'prices/43-inch-tv-uganda', 'prices/50-inch-tv-uganda',
            'prices/55-inch-tv-uganda', 'prices/65-inch-tv-uganda', 'prices/75-inch-tv-uganda',
            'prices/tcl-tv-uganda', 'prices/smart-tv-uganda', 'prices/fridge-uganda',
            'prices/washing-machine-uganda', 'prices/chest-freezer-uganda',
            'prices/hisense-tv-uganda', 'prices/chiq-tv-uganda', 'prices/samsung-tv-uganda',
        ];
        foreach ($prices as $p) {
            $urls[] = ['loc' => "$base/$p", 'lastmod' => $today, 'changefreq' => 'weekly', 'priority' => 0.7];
        }
        return $urls;
    }

    protected function getStaticUrls(): array
    {
        $base = $this->baseUrl;
        $today = date('Y-m-d');
        $urls = [];

        $pages = [
            '/' => 1.0, '/hisense' => 0.7, '/chiq' => 0.7, '/adh' => 0.7, '/samsung' => 0.7,
            '/brands' => 0.6, '/samsung-washing-machines' => 0.6, '/samsung-refrigerators' => 0.6,
            '/buy/electronics-kampala' => 0.7, '/buy/tv-uganda' => 0.7,
            '/best/smart-tv-uganda' => 0.7, '/compare/hisense-vs-tcl' => 0.6,
            '/faq' => 0.5, '/bundles' => 0.5,
            '/tv-size-calculator' => 0.5, '/recommendations' => 0.4, '/budget-planner' => 0.4,
            '/yoola-vs-jumia' => 0.6, '/compare/yoola-jumia' => 0.5,
            '/buy/electronics-kampala' => 0.7,
        ];

        foreach ($pages as $path => $priority) {
            $urls[] = [
                'loc' => $base . $path,
                'lastmod' => $today,
                'changefreq' => $path === '/' ? 'daily' : 'monthly',
                'priority' => $priority,
            ];
        }

        return $urls;
    }

    protected function getProgrammaticUrls(): array
    {
        // Kept for backward compatibility — merged into getSeoUrls()
        return [];
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    protected function pingGoogle(): void
    {
        $url = 'https://www.google.com/ping?sitemap=' . urlencode($this->baseUrl . '/sitemap-index.xml');
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 10]);
            $response = $client->get($url);
            $this->info('  Google ping: ' . $response->getStatusCode());
        } catch (\Exception $e) {
            $this->warn('  Google ping failed: ' . $e->getMessage());
        }
    }

    protected function pingBing(): void
    {
        $url = 'https://www.bing.com/ping?sitemap=' . urlencode($this->baseUrl . '/sitemap-index.xml');
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 10]);
            $response = $client->get($url);
            $this->info('  Bing ping: ' . $response->getStatusCode());
        } catch (\Exception $e) {
            $this->warn('  Bing ping failed: ' . $e->getMessage());
        }
    }
}
