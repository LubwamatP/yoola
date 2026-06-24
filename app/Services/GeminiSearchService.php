<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\AI\AIProviders\GeminiProvider;

class GeminiSearchService
{
    protected GeminiProvider $gemini;
    protected array $config;
    protected ?array $conversationContext = null;

    public function __construct()
    {
        $this->gemini = new GeminiProvider();
        $this->config = [
            'enabled' => config('gemini.search.enabled', true),
            'min_confidence' => config('gemini.search.min_confidence', 0.7),
            'cache_ttl' => config('gemini.search.cache_ttl', 3600),
            'max_products' => config('gemini.search.max_products', 100),
            'conversational' => config('gemini.search.conversational', true),
        ];
    }

    /**
     * Main search method - orchestrates the AI-powered search
     */
    public function search(string $query, array $filters = [], int $perPage = 20): array
    {
        // Check if Gemini is enabled and configured
        if (!$this->config['enabled'] || !$this->gemini->isConfigured()) {
            return $this->fallbackSearch($query, $filters, $perPage);
        }

        $cacheKey = $this->getCacheKey($query, $filters);

        return Cache::remember($cacheKey, $this->config['cache_ttl'], function () use ($query, $filters, $perPage) {
            try {
                // 1. Extract intent using Gemini
                $intent = $this->extractIntent($query);

                if (!$intent || ($intent['confidence'] ?? 0) < $this->config['min_confidence']) {
                    Log::info('Gemini search: Low confidence, falling back', [
                        'query' => $query,
                        'confidence' => $intent['confidence'] ?? 0
                    ]);
                    return $this->fallbackSearch($query, $filters, $perPage);
                }

                // 2. Build database query from intent
                $products = $this->executeIntentQuery($intent, $filters);

                // 3. Generate AI explanations for top results
                $rankedProducts = $this->rankAndExplain($products, $query, $intent);

                // 4. Generate facets
                $facets = $this->generateFacets($rankedProducts);

                // 5. Paginate results
                $paginated = $this->paginateResults($rankedProducts, $perPage);

                // 6. Update conversation context
                if ($this->config['conversational']) {
                    $this->updateConversationContext($query, $intent);
                }

                return [
                    'results' => $paginated,
                    'total' => $rankedProducts->count(),
                    'intent' => $intent,
                    'ai_powered' => true,
                    'detected_phrases' => $intent['entities'] ?? [],
                    'filters_applied' => $filters,
                    'suggestions' => $this->generateSuggestions($intent, $facets),
                    'did_you_mean' => $intent['did_you_mean'] ?? null,
                    'facets' => $facets,
                    'top_result' => $rankedProducts->first(),
                    'why_matches' => $this->extractWhyMatches($rankedProducts),
                ];

            } catch (\Exception $e) {
                Log::error('Gemini search failed', [
                    'query' => $query,
                    'error' => $e->getMessage()
                ]);
                return $this->fallbackSearch($query, $filters, $perPage);
            }
        });
    }

    /**
     * Extract search intent using Gemini
     */
    protected function extractIntent(string $query): ?array
    {
        $contextHint = '';
        if ($this->config['conversational'] && $this->conversationContext) {
            $contextHint = "\n\nPrevious search context: " . json_encode($this->conversationContext);
        }

        $prompt = <<<PROMPT
You are a search specialist for Yoola.ug, an electronics store in Uganda.
Analyze this customer search query and extract their intent.

Search Query: "{$query}"
{$contextHint}

Extract and return ONLY valid JSON (no markdown, no explanation):
{
  "intent": "find_product" | "compare" | "budget_search" | "brand_search" | "use_case_search" | "specification_search",
  "product_types": ["smartphones", "laptops", "tvs", "refrigerators", "speakers", "powerbanks", "cookers", "washing_machines", "air_conditioners", "solar_panels", ...],
  "budget_ugx": {"min": null, "max": null},
  "budget_mentioned": false,
  "primary_features": ["feature1", "feature2"],
  "use_case": "gaming" | "photography" | "productivity" | "entertainment" | "home_use" | "office" | "outdoor" | null,
  "brands": [],
  "specifications": {"size": null, "capacity": null, "power": null, "color": null},
  "comparison_requested": false,
  "entities": ["extracted", "key", "terms"],
  "confidence": 0.85,
  "did_you_mean": null,
  "search_summary": "Brief 1-line summary of what user wants"
}

Rules:
- budget_ugx: Extract if price mentioned (e.g., "under 500k" = max: 500000, "1 million" = 1000000). "k" means thousand.
- primary_features: Infer from use case or explicit mentions
- confidence: 0.0-1.0 scale of how well you understand the query
- entities: Key searchable terms from the query
- For Ugandan market: Consider local brands, DC/solar products, price in UGX
- Common abbreviations: TV = television, AC = air conditioner, fridge = refrigerator
PROMPT;

        $result = $this->gemini->generateJson($prompt, [
            'temperature' => 0.3,
            'max_tokens' => 1024
        ]);

        if ($result) {
            // Log successful intent extraction
            Log::info('Gemini intent extracted', [
                'query' => $query,
                'intent' => $result['intent'] ?? 'unknown',
                'confidence' => $result['confidence'] ?? 0
            ]);
        }

        return $result;
    }

    /**
     * Execute database query based on extracted intent
     */
    protected function executeIntentQuery(array $intent, array $filters): Collection
    {
        $query = Product::query()
            ->select([
                'id', 'name', 'slug', 'thumbnail', 'unit_price',
                'purchase_price', 'discount', 'discount_type',
                'tax', 'tax_type', 'brand_id', 'category_ids',
                'category_id', 'details', 'current_stock'
            ])
            ->where('status', 1)
            ->where('request_status', 1);

        // Eager load relationships
        try {
            $query->with(['brand', 'category', 'reviews']);
        } catch (\Exception $e) {
            // Silently continue if relationships fail
        }

        // Apply product type filters
        if (!empty($intent['product_types'])) {
            $query->where(function ($q) use ($intent) {
                foreach ($intent['product_types'] as $type) {
                    $q->orWhere('name', 'LIKE', "%{$type}%");
                    $q->orWhereHas('category', function ($catQ) use ($type) {
                        $catQ->where('name', 'LIKE', "%{$type}%");
                    });
                }
            });
        }

        // Apply brand filters
        if (!empty($intent['brands'])) {
            $query->where(function ($q) use ($intent) {
                foreach ($intent['brands'] as $brand) {
                    $q->orWhere('name', 'LIKE', "%{$brand}%");
                    $q->orWhereHas('brand', function ($brandQ) use ($brand) {
                        $brandQ->where('name', 'LIKE', "%{$brand}%");
                    });
                }
            });
        }

        // Apply budget filters
        if (!empty($intent['budget_ugx'])) {
            if (!empty($intent['budget_ugx']['min'])) {
                $query->where('unit_price', '>=', $intent['budget_ugx']['min']);
            }
            if (!empty($intent['budget_ugx']['max'])) {
                $query->where('unit_price', '<=', $intent['budget_ugx']['max']);
            }
        }

        // Apply entity-based search (key terms)
        if (!empty($intent['entities'])) {
            $query->where(function ($q) use ($intent) {
                foreach ($intent['entities'] as $entity) {
                    if (strlen($entity) > 2) { // Skip very short terms
                        $q->orWhere('name', 'LIKE', "%{$entity}%");
                        $q->orWhere('details', 'LIKE', "%{$entity}%");
                    }
                }
            });
        }

        // Apply specifications filters
        if (!empty($intent['specifications'])) {
            foreach ($intent['specifications'] as $spec => $value) {
                if ($value) {
                    $query->where(function ($q) use ($value) {
                        $q->orWhere('name', 'LIKE', "%{$value}%");
                        $q->orWhere('details', 'LIKE', "%{$value}%");
                    });
                }
            }
        }

        // Apply additional filters from request
        if (!empty($filters['brand'])) {
            $query->whereHas('brand', function ($q) use ($filters) {
                $q->where('name', $filters['brand']);
            });
        }

        if (!empty($filters['category'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('name', $filters['category']);
            });
        }

        if (!empty($filters['price_min'])) {
            $query->where('unit_price', '>=', $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('unit_price', '<=', $filters['price_max']);
        }

        return $query->limit($this->config['max_products'])->get();
    }

    /**
     * Rank products and generate AI explanations
     */
    protected function rankAndExplain(Collection $products, string $query, array $intent): Collection
    {
        if ($products->isEmpty()) {
            return $products;
        }

        // Score each product based on intent match
        $scored = $products->map(function ($product) use ($query, $intent) {
            $score = 0;
            $matchReasons = [];
            $nameLower = strtolower($product->name ?? '');
            $detailsLower = strtolower($product->details ?? '');
            $brandName = strtolower($product->brand->name ?? '');
            $categoryName = strtolower($product->category->name ?? '');

            // Brand match bonus
            if (!empty($intent['brands'])) {
                foreach ($intent['brands'] as $brand) {
                    $brandLower = strtolower($brand);
                    if (str_contains($brandName, $brandLower) || str_contains($nameLower, $brandLower)) {
                        $score += 500;
                        $matchReasons[] = "Brand match: {$brand}";
                    }
                }
            }

            // Product type match
            if (!empty($intent['product_types'])) {
                foreach ($intent['product_types'] as $type) {
                    if (str_contains($nameLower, strtolower($type)) || str_contains($categoryName, strtolower($type))) {
                        $score += 300;
                        $matchReasons[] = "Category match";
                    }
                }
            }

            // Budget alignment
            if (!empty($intent['budget_ugx']['max']) && $product->unit_price <= $intent['budget_ugx']['max']) {
                $score += 200;
                $matchReasons[] = "Within budget";
            }

            // Feature match
            if (!empty($intent['primary_features'])) {
                foreach ($intent['primary_features'] as $feature) {
                    if (str_contains($nameLower, strtolower($feature)) || str_contains($detailsLower, strtolower($feature))) {
                        $score += 150;
                        $matchReasons[] = ucfirst($feature);
                    }
                }
            }

            // Entity match from query
            if (!empty($intent['entities'])) {
                foreach ($intent['entities'] as $entity) {
                    if (strlen($entity) > 2 && str_contains($nameLower, strtolower($entity))) {
                        $score += 100;
                    }
                }
            }

            // Boost products with reviews
            $reviewCount = $product->reviews->count() ?? 0;
            if ($reviewCount > 0) {
                $avgRating = $product->reviews->avg('rating') ?? 0;
                $score += ($avgRating * 20) + min($reviewCount * 5, 50);
                if ($avgRating >= 4) {
                    $matchReasons[] = "Highly rated ({$avgRating}/5)";
                }
            }

            // In-stock bonus
            if (($product->current_stock ?? 0) > 0) {
                $score += 50;
                $matchReasons[] = "In stock";
            }

            $product->relevance_score = $score;
            $product->match_reasons = array_unique(array_slice($matchReasons, 0, 3));
            $product->why_match = $this->generateWhyMatch($product, $intent);

            return $product;
        });

        return $scored->sortByDesc('relevance_score')->values();
    }

    /**
     * Generate "Why this matches" explanation for a product
     */
    protected function generateWhyMatch($product, array $intent): string
    {
        $reasons = [];

        if (!empty($product->match_reasons)) {
            $reasons = array_merge($reasons, $product->match_reasons);
        }

        // Add price context
        $price = number_format($product->unit_price);
        if (!empty($intent['budget_ugx']['max'])) {
            $percentOfBudget = round(($product->unit_price / $intent['budget_ugx']['max']) * 100);
            if ($percentOfBudget <= 100) {
                $reasons[] = "{$percentOfBudget}% of budget";
            }
        }

        if (empty($reasons)) {
            $reasons[] = "Matches your search";
        }

        return implode(' • ', array_slice($reasons, 0, 3));
    }

    /**
     * Extract why_matches for top products
     */
    protected function extractWhyMatches(Collection $products): array
    {
        return $products->take(5)->mapWithKeys(function ($product) {
            return [$product->id => $product->why_match ?? 'Matches your search'];
        })->toArray();
    }

    /**
     * Generate facets from results
     */
    protected function generateFacets(Collection $results): array
    {
        $facets = [
            'brands' => [],
            'categories' => [],
            'price_ranges' => []
        ];

        if ($results->isEmpty()) {
            return $facets;
        }

        // Brand facets
        $facets['brands'] = $results
            ->whereNotNull('brand')
            ->groupBy(fn($item) => $item->brand->name ?? 'Other')
            ->map->count()
            ->sortDesc()
            ->take(10)
            ->toArray();

        // Category facets
        $facets['categories'] = $results
            ->whereNotNull('category')
            ->groupBy(fn($item) => $item->category->name ?? 'Other')
            ->map->count()
            ->sortDesc()
            ->take(10)
            ->toArray();

        // Price range facets - use flat array format expected by views
        $prices = $results->pluck('unit_price')->filter();
        if ($prices->isNotEmpty()) {
            $facets['price_ranges'] = $this->calculatePriceRanges($prices);
        }

        // Add ratings facet for consistency with EnhancedSearchService
        $ratings = [];
        for ($r = 4; $r >= 1; $r--) {
            $count = $results->filter(function ($p) use ($r) {
                $avgRating = $p->reviews ? $p->reviews->avg('rating') : 0;
                return $avgRating >= $r;
            })->count();

            if ($count > 0) {
                $ratings[] = ['rating' => $r, 'label' => "{$r}+ Stars", 'count' => $count];
            }
        }
        $facets['ratings'] = $ratings;

        return $facets;
    }

    /**
     * Calculate price range buckets
     */
    protected function calculatePriceRanges(Collection $prices): array
    {
        $ranges = [];
        $thresholds = [100000, 250000, 500000, 1000000, 2000000, 5000000];

        foreach ($thresholds as $i => $threshold) {
            $min = $i === 0 ? 0 : $thresholds[$i - 1];
            $count = $prices->filter(fn($p) => $p >= $min && $p < $threshold)->count();
            if ($count > 0) {
                $ranges[] = [
                    'min' => $min,
                    'max' => $threshold,
                    'label' => $this->formatPriceRange($min, $threshold),
                    'count' => $count
                ];
            }
        }

        // Above highest threshold
        $aboveMax = $prices->filter(fn($p) => $p >= end($thresholds))->count();
        if ($aboveMax > 0) {
            $ranges[] = [
                'min' => end($thresholds),
                'max' => null,
                'label' => 'Above ' . $this->formatPrice(end($thresholds)),
                'count' => $aboveMax
            ];
        }

        return $ranges;
    }

    /**
     * Format price for display
     */
    protected function formatPrice(int $price): string
    {
        if ($price >= 1000000) {
            return number_format($price / 1000000, 1) . 'M UGX';
        }
        if ($price >= 1000) {
            return number_format($price / 1000) . 'K UGX';
        }
        return number_format($price) . ' UGX';
    }

    /**
     * Format price range for display
     */
    protected function formatPriceRange(int $min, int $max): string
    {
        return $this->formatPrice($min) . ' - ' . $this->formatPrice($max);
    }

    /**
     * Generate search suggestions based on intent and facets
     */
    protected function generateSuggestions(array $intent, array $facets): array
    {
        $suggestions = [];

        // Suggest top brands
        if (!empty($facets['brands']) && !empty($intent['product_types'])) {
            $topBrands = array_slice(array_keys($facets['brands']), 0, 3);
            foreach ($topBrands as $brand) {
                $productType = $intent['product_types'][0] ?? '';
                $suggestions[] = "{$brand} {$productType}";
            }
        }

        // Suggest related categories
        if (!empty($facets['categories'])) {
            foreach (array_slice(array_keys($facets['categories']), 0, 2) as $category) {
                $suggestions[] = $category;
            }
        }

        // Add use-case based suggestions
        if (!empty($intent['use_case'])) {
            $suggestions[] = "Best for {$intent['use_case']}";
        }

        return array_unique(array_slice($suggestions, 0, 5));
    }

    /**
     * Paginate results
     */
    protected function paginateResults(Collection $results, int $perPage): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $results->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $results->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    /**
     * Update conversation context for multi-turn search
     */
    protected function updateConversationContext(string $query, array $intent): void
    {
        $context = [
            'query' => $query,
            'intent' => $intent['intent'] ?? null,
            'product_types' => $intent['product_types'] ?? [],
            'brands' => $intent['brands'] ?? [],
            'budget' => $intent['budget_ugx'] ?? null,
            'timestamp' => now()->timestamp
        ];

        Session::put('gemini_search_context', $context);
    }

    /**
     * Load conversation context from session
     */
    protected function loadConversationContext(): void
    {
        if (!$this->config['conversational']) {
            return;
        }

        $context = Session::get('gemini_search_context');

        if ($context) {
            $contextWindow = config('gemini.search.context_window', 30) * 60; // Convert to seconds
            if ((now()->timestamp - ($context['timestamp'] ?? 0)) < $contextWindow) {
                $this->conversationContext = $context;
            } else {
                Session::forget('gemini_search_context');
            }
        }
    }

    /**
     * Fallback to traditional search when AI fails
     */
    protected function fallbackSearch(string $query, array $filters, int $perPage): array
    {
        try {
            $advancedService = new AdvancedSearchService();
            $result = $advancedService->search($query, $filters, $perPage);
            $result['ai_powered'] = false;
            $result['fallback_reason'] = 'AI search unavailable';
            return $result;
        } catch (\Exception $e) {
            // Ultimate fallback - simple LIKE search
            $products = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('details', 'LIKE', "%{$query}%")
                ->where('status', 1)
                ->paginate($perPage);

            return [
                'results' => $products,
                'total' => $products->total(),
                'intent' => null,
                'ai_powered' => false,
                'fallback_reason' => 'Using basic search',
                'detected_phrases' => [],
                'filters_applied' => $filters,
                'suggestions' => [],
                'did_you_mean' => null,
                'facets' => [],
            ];
        }
    }

    /**
     * Generate cache key for search results
     */
    protected function getCacheKey(string $query, array $filters): string
    {
        $sessionId = Session::getId();
        return 'gemini_search:' . md5($query . json_encode($filters) . $sessionId);
    }

    /**
     * Clear search cache (useful for admin)
     */
    public function clearCache(): void
    {
        // Clear all gemini search cache
        Cache::flush(); // In production, use tagged caching
    }

    /**
     * Check if Gemini search is available
     */
    public function isAvailable(): bool
    {
        return $this->config['enabled'] && $this->gemini->isConfigured();
    }
}
