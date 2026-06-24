<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class BudgetPlannerService
{
    private string $apiKey;
    private string $endpoint;
    private int $timeout;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        $this->endpoint = env('GEMINI_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent');
        $this->timeout = (int) env('GEMINI_REQUEST_TIMEOUT', 15);
    }

    /**
     * Get product recommendations for a given budget
     */
    public function getRecommendations(float $budget, ?string $roomType = null, ?array $categoryIds = null): array
    {
        // Get available products within budget
        $products = $this->getProductsWithinBudget($budget, $categoryIds);
        
        if ($products->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No products found within your budget.',
                'bundles' => [],
            ];
        }

        // Generate AI-powered bundles
        $bundles = $this->generateSmartBundles($products, $budget, $roomType);
        
        return [
            'success' => true,
            'budget' => $budget,
            'bundles' => $bundles,
            'total_products_available' => $products->count(),
        ];
    }

    /**
     * Get products within budget
     */
    private function getProductsWithinBudget(float $budget, ?array $categoryIds = null): Collection
    {
        $query = Product::active()
            ->where('current_stock', '>', 0)
            ->where('unit_price', '<=', $budget)
            ->where('unit_price', '>', 0)
            ->with(['brand', 'category'])
            ->select(['id', 'name', 'slug', 'unit_price', 'discount', 'discount_type', 'current_stock', 'category_id', 'brand_id', 'thumbnail', 'thumbnail_storage_type']);

        if ($categoryIds && count($categoryIds) > 0) {
            $query->whereIn('category_id', $categoryIds);
        }

        return $query->orderBy('unit_price', 'desc')->limit(100)->get();
    }

    /**
     * Generate smart bundles using Gemini AI
     */
    private function generateSmartBundles(Collection $products, float $budget, ?string $roomType): array
    {
        // Create product summary for AI
        $productSummary = $products->map(function ($product) {
            $price = $this->getDiscountedPrice($product);
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $price,
                'category' => $product->category->name ?? 'Other',
                'brand' => $product->brand->name ?? 'Generic',
            ];
        })->toArray();

        // Try AI-powered bundle generation
        $aiBundles = $this->getAIBundles($productSummary, $budget, $roomType);
        
        if (!empty($aiBundles)) {
            return $this->hydrateAIBundles($aiBundles, $products);
        }

        // Fallback to algorithm-based bundles
        return $this->generateAlgorithmicBundles($products, $budget);
    }

    /**
     * Get AI-generated bundle suggestions from Gemini
     */
    private function getAIBundles(array $productSummary, float $budget, ?string $roomType): array
    {
        if (empty($this->apiKey)) {
            return [];
        }

        $cacheKey = 'budget_planner_' . md5(json_encode([$budget, $roomType, count($productSummary)]));
        
        // Check cache first
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $roomContext = $roomType ? "The customer is furnishing their {$roomType}." : "The customer wants general home appliances.";
        
        $prompt = "You are a helpful shopping assistant for Yoola Uganda, an electronics store.

Budget: UGX " . number_format($budget) . "
{$roomContext}

Available products (showing up to 50):
" . json_encode(array_slice($productSummary, 0, 50), JSON_PRETTY_PRINT) . "

Create 3 different bundle suggestions that:
1. Stay UNDER the budget (leave some buffer)
2. Complement each other (e.g., TV + soundbar, not 2 TVs)
3. Provide good value

Return ONLY valid JSON array with this structure:
[
  {
    \"name\": \"Bundle Name\",
    \"description\": \"Why this bundle is great\",
    \"product_ids\": [1, 2, 3],
    \"total_price\": 1500000,
    \"savings_tip\": \"Optional tip\"
  }
]

Return ONLY the JSON array, no other text.";

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->endpoint . '?key=' . $this->apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 1000,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Extract JSON from response
                $text = trim($text);
                if (preg_match('/\[.*\]/s', $text, $matches)) {
                    $bundles = json_decode($matches[0], true);
                    if (is_array($bundles)) {
                        Cache::put($cacheKey, $bundles, now()->addHours(6));
                        return $bundles;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Budget Planner AI failed', ['error' => $e->getMessage()]);
        }

        return [];
    }

    /**
     * Hydrate AI bundles with full product data
     */
    private function hydrateAIBundles(array $aiBundles, Collection $products): array
    {
        $hydrated = [];
        
        foreach ($aiBundles as $bundle) {
            $bundleProducts = [];
            $totalPrice = 0;
            
            foreach ($bundle['product_ids'] ?? [] as $productId) {
                $product = $products->firstWhere('id', $productId);
                if ($product) {
                    $price = $this->getDiscountedPrice($product);
                    $bundleProducts[] = [
                        'product' => $product,
                        'price' => $price,
                    ];
                    $totalPrice += $price;
                }
            }
            
            if (count($bundleProducts) > 0) {
                $hydrated[] = [
                    'name' => $bundle['name'] ?? 'Recommended Bundle',
                    'description' => $bundle['description'] ?? '',
                    'products' => $bundleProducts,
                    'total_price' => $totalPrice,
                    'savings_tip' => $bundle['savings_tip'] ?? null,
                ];
            }
        }
        
        return $hydrated;
    }

    /**
     * Generate algorithmic bundles (fallback when AI unavailable)
     */
    private function generateAlgorithmicBundles(Collection $products, float $budget): array
    {
        $bundles = [];
        
        // Bundle 1: Best single high-value item
        $premium = $products->filter(fn($p) => $this->getDiscountedPrice($p) >= $budget * 0.7)
                           ->sortByDesc(fn($p) => $this->getDiscountedPrice($p))
                           ->first();
        
        if ($premium) {
            $remaining = $budget - $this->getDiscountedPrice($premium);
            $addons = $products->filter(fn($p) => $p->id !== $premium->id && $this->getDiscountedPrice($p) <= $remaining)
                              ->take(2);
            
            $bundleProducts = collect([$premium])->concat($addons);
            $total = $bundleProducts->sum(fn($p) => $this->getDiscountedPrice($p));
            
            $bundles[] = [
                'name' => '🌟 Premium Pick',
                'description' => 'Get the best item with complementary accessories',
                'products' => $bundleProducts->map(fn($p) => [
                    'product' => $p,
                    'price' => $this->getDiscountedPrice($p),
                ])->toArray(),
                'total_price' => $total,
                'savings_tip' => 'Best value for a single major purchase',
            ];
        }

        // Bundle 2: Value bundle (multiple mid-range items)
        $midRange = $products->filter(fn($p) => $this->getDiscountedPrice($p) <= $budget * 0.5)
                            ->sortByDesc(fn($p) => $this->getDiscountedPrice($p));
        
        $valueBundle = collect();
        $valueTotal = 0;
        
        foreach ($midRange as $product) {
            $price = $this->getDiscountedPrice($product);
            if ($valueTotal + $price <= $budget && $valueBundle->count() < 4) {
                $valueBundle->push($product);
                $valueTotal += $price;
            }
        }
        
        if ($valueBundle->count() >= 2) {
            $bundles[] = [
                'name' => '💰 Value Bundle',
                'description' => 'Get more items for your money',
                'products' => $valueBundle->map(fn($p) => [
                    'product' => $p,
                    'price' => $this->getDiscountedPrice($p),
                ])->toArray(),
                'total_price' => $valueTotal,
                'savings_tip' => 'Perfect for furnishing multiple rooms',
            ];
        }

        // Bundle 3: Essentials bundle (spread across categories)
        $categories = $products->groupBy('category_id');
        $essentials = collect();
        $essentialsTotal = 0;
        
        foreach ($categories as $categoryProducts) {
            $cheapest = $categoryProducts->sortBy(fn($p) => $this->getDiscountedPrice($p))->first();
            if ($cheapest) {
                $price = $this->getDiscountedPrice($cheapest);
                if ($essentialsTotal + $price <= $budget && $essentials->count() < 5) {
                    $essentials->push($cheapest);
                    $essentialsTotal += $price;
                }
            }
        }
        
        if ($essentials->count() >= 2) {
            $bundles[] = [
                'name' => '🏠 Home Essentials',
                'description' => 'One item from each category to cover all your needs',
                'products' => $essentials->map(fn($p) => [
                    'product' => $p,
                    'price' => $this->getDiscountedPrice($p),
                ])->toArray(),
                'total_price' => $essentialsTotal,
                'savings_tip' => 'Cover all your basics in one purchase',
            ];
        }

        return $bundles;
    }

    /**
     * Get discounted price for a product
     */
    private function getDiscountedPrice(Product $product): float
    {
        $price = $product->unit_price;
        
        if ($product->discount > 0) {
            if ($product->discount_type === 'percent') {
                $price = $price - ($price * $product->discount / 100);
            } else {
                $price = $price - $product->discount;
            }
        }
        
        return max(0, $price);
    }

    /**
     * Get room type suggestions
     */
    public function getRoomTypes(): array
    {
        return [
            'living_room' => 'Living Room',
            'bedroom' => 'Bedroom',
            'kitchen' => 'Kitchen',
            'office' => 'Home Office',
            'outdoor' => 'Outdoor/Patio',
        ];
    }

    /**
     * Get available categories with products
     */
    public function getAvailableCategories(): Collection
    {
        return Category::whereHas('products', function ($query) {
                $query->active()->where('current_stock', '>', 0);
            })
            ->where('parent_id', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }
}
