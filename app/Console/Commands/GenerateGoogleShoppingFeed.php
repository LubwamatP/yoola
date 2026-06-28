<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class GenerateGoogleShoppingFeed extends Command
{
    protected $signature = 'shopping:feed {--limit=0 : Limit products (0=all)}';
    protected $description = 'Generate Google Shopping Feed XML for Merchant Center';

    protected string $baseUrl;
    protected array $googleCategories = [
        'tv' => '222',
        'television' => '222',
        'led tv' => '222',
        'smart tv' => '222',
        'refrigerator' => '211',
        'fridge' => '211',
        'freezer' => '211',
        'washing machine' => '229',
        'washer' => '229',
        'air conditioner' => '204',
        'ac' => '204',
        'cooker' => '214',
        'oven' => '214',
        'microwave' => '215',
        'speaker' => '233',
        'soundbar' => '233',
        'home audio' => '233',
        'blender' => '407',
        'kettle' => '403',
        'iron' => '734',
        'phone' => '267',
        'smartphone' => '267',
        'laptop' => '172',
        'computer' => '172',
        'tablet' => '171',
    ];

    public function handle()
    {
        // Define constant required by 6valley's storage helpers
        if (!defined('DOMAIN_POINTED_DIRECTORY')) {
            define('DOMAIN_POINTED_DIRECTORY', 'public');
        }

        $this->baseUrl = rtrim(config('app.url'), '/');
        $limit = (int) $this->option('limit');

        $this->info('Generating Google Shopping Feed...');

        $query = Product::where('status', 1)
            ->where('unit_price', '>', 0);

        if ($limit > 0) {
            $query->limit($limit);
        }

        $products = $query->get();

        $this->info('Found ' . $products->count() . ' active products with prices.');

        $items = $this->buildFeedItems($products);

        $xml = $this->renderFeed($items);

        $path = public_path('google-shopping-feed.xml');
        file_put_contents($path, $xml);

        $this->info('Feed written: ' . $path);
        $this->info('Items: ' . count($items));
        $this->info('Submit to Google Merchant Center: ' . $this->baseUrl . '/google-shopping-feed.xml');

        return 0;
    }

    protected function buildFeedItems($products): array
    {
        $items = [];

        foreach ($products as $p) {
            $brand = $p->brand->name ?? 'Yoola';
            $category = $p->category->name ?? 'Electronics';
            $price = $p->unit_price ?? 0;
            $finalPrice = $price;

            // Calculate discount price
            if (($p->discount ?? 0) > 0 && $price > 0) {
                if (($p->discount_type ?? '') === 'percent') {
                    $finalPrice = $price - ($price * $p->discount / 100);
                } else {
                    $finalPrice = max(0, $price - $p->discount);
                }
            }

            // Get image
            $imageUrl = $p->thumbnail_full_url['path'] ?? '';
            if (empty($imageUrl)) {
                $imageUrl = $p->thumbnail ?? '';
            }
            if (!empty($imageUrl) && !str_starts_with($imageUrl, 'http')) {
                $imageUrl = $this->baseUrl . '/storage/product/thumbnail/' . $imageUrl;
            }

            // Build description
            $desc = $p->name;
            if (!empty($p->details)) {
                $clean = strip_tags($p->details);
                $clean = str_replace('&nbsp;', ' ', $clean);
                $clean = preg_replace('/\s+/', ' ', $clean);
                $desc = $p->name . '. ' . substr($clean, 0, 300);
            }
            $desc = substr($desc, 0, 500);

            // Determine Google product category
            $googleCat = $this->guessGoogleCategory($category, $p->name);

            // Availability
            $stock = $p->current_stock ?? 0;
            $availability = $stock > 0 ? 'in stock' : 'out of stock';

            // Shipping
            $shippingPrice = '0.00 UGX'; // Free Kampala delivery

            $items[] = [
                'id' => $p->id,
                'title' => $p->name,
                'description' => $desc,
                'link' => $this->baseUrl . '/product/' . $p->slug,
                'image_link' => $imageUrl,
                'price' => number_format($finalPrice, 2, '.', '') . ' UGX',
                'sale_price' => ($finalPrice < $price) ? number_format($finalPrice, 2, '.', '') . ' UGX' : null,
                'availability' => $availability,
                'brand' => $brand,
                'condition' => 'new',
                'google_product_category' => $googleCat,
                'product_type' => $category,
                'mpn' => $p->code ?? $p->slug,
                'shipping' => $shippingPrice,
                'identifier_exists' => 'no',
            ];
        }

        return $items;
    }

    protected function guessGoogleCategory(string $category, string $name): string
    {
        $search = strtolower($category . ' ' . $name);

        foreach ($this->googleCategories as $keyword => $gcat) {
            if (str_contains($search, $keyword)) {
                return $gcat;
            }
        }

        return '222'; // Default: Electronics > TVs
    }

    protected function renderFeed(array $items): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">' . "\n";
        $xml .= "  <channel>\n";
        $xml .= '    <title>' . $this->escape(config('app.name', 'Yoola')) . " Products</title>\n";
        $xml .= '    <link>' . $this->escape($this->baseUrl) . "</link>\n";
        $xml .= '    <description>Product feed for Google Merchant Center</description>' . "\n";

        foreach ($items as $item) {
            $xml .= "    <item>\n";
            $xml .= '      <g:id>' . $this->escape($item['id']) . "</g:id>\n";
            $xml .= '      <g:title>' . $this->escape($item['title']) . "</g:title>\n";
            $xml .= '      <g:description>' . $this->escape($item['description']) . "</g:description>\n";
            $xml .= '      <g:link>' . $this->escape($item['link']) . "</g:link>\n";
            $xml .= '      <g:image_link>' . $this->escape($item['image_link']) . "</g:image_link>\n";
            $xml .= '      <g:price>' . $this->escape($item['price']) . "</g:price>\n";

            if (!empty($item['sale_price'])) {
                $xml .= '      <g:sale_price>' . $this->escape($item['sale_price']) . "</g:sale_price>\n";
            }

            $xml .= '      <g:availability>' . $this->escape($item['availability']) . "</g:availability>\n";
            $xml .= '      <g:brand>' . $this->escape($item['brand']) . "</g:brand>\n";
            $xml .= '      <g:condition>' . $this->escape($item['condition']) . "</g:condition>\n";
            $xml .= '      <g:google_product_category>' . $this->escape($item['google_product_category']) . "</g:google_product_category>\n";
            $xml .= '      <g:product_type>' . $this->escape($item['product_type']) . "</g:product_type>\n";
            $xml .= '      <g:mpn>' . $this->escape($item['mpn']) . "</g:mpn>\n";
            $xml .= '      <g:identifier_exists>' . $this->escape($item['identifier_exists']) . "</g:identifier_exists>\n";
            $xml .= '      <g:shipping>' . "\n";
            $xml .= '        <g:country>UG</g:country>' . "\n";
            $xml .= '        <g:service>Standard</g:service>' . "\n";
            $xml .= '        <g:price>0.00 UGX</g:price>' . "\n";
            $xml .= "      </g:shipping>\n";
            $xml .= "    </item>\n";
        }

        $xml .= "  </channel>\n";
        $xml .= '</rss>';

        return $xml;
    }

    protected function escape(string $str): string
    {
        return htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
