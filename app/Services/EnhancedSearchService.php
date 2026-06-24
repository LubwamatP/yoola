<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

/**
 * Enhanced Search Service - Amazon-style robust search
 * Features:
 * - Fuzzy matching for typos
 * - Multi-strategy search (exact, partial, fuzzy, related)
 * - Advanced filtering (price range, brand, category, rating, availability)
 * - Relevance scoring
 * - Search suggestions and "did you mean"
 * - Zero-result recovery
 */
class EnhancedSearchService
{
    protected array $config;
    protected array $commonTypos;
    protected array $synonyms;
    protected ?GeminiSearchService $geminiService = null;

    public function __construct()
    {
        $this->config = [
            'max_results' => 200,
            'cache_ttl' => 1800, // 30 minutes
            'fuzzy_threshold' => 0.7,
            'use_gemini' => false // Disabled, // AI is optional - works without it
        ];

        // Initialize Gemini if available
        if ($this->config['use_gemini']) {
            try {
                // $this->geminiService = new GeminiSearchService(); // Disabled - AI search optional
            } catch (\Exception $e) {
                Log::debug('Gemini not available', ['error' => $e->getMessage()]);
            }
        }

        $this->initializeTyposAndSynonyms();
    }

    /**
     * Main search method - orchestrates all search strategies
     */
    public function search(string $query, array $filters = [], int $perPage = 20): array
    {
        $query = trim($query);

        if (empty($query)) {
            return $this->emptyResponse($perPage);
        }

        // Try Gemini AI first if available
        if ($this->geminiService && $this->geminiService->isAvailable()) {
            try {
                $geminiResult = $this->geminiService->search($query, $filters, $perPage);
                if (!empty($geminiResult['results']) && $geminiResult['total'] > 0) {
                    return $geminiResult;
                }
            } catch (\Exception $e) {
                Log::debug('Gemini search skipped', ['error' => $e->getMessage()]);
            }
        }

        // Multi-strategy traditional search
        return $this->multiStrategySearch($query, $filters, $perPage);
    }

    /**
     * Multi-strategy search with fallback chain
     */
    protected function multiStrategySearch(string $query, array $filters, int $perPage): array
    {
        // Cache key for search results (WITHOUT page number - we paginate after cache)
        $cacheKey = 'enhanced_search_v3:' . md5($query . json_encode($filters));

        // Cache only the filtered results and metadata, NOT the paginator
        $cachedData = Cache::remember($cacheKey, $this->config['cache_ttl'], function () use ($query, $filters) {
            $results = collect();
            $searchMeta = [
                'original_query' => $query,
                'corrected_query' => null,
                'strategies_used' => [],
            ];

            // Parse and normalize query
            $parsed = $this->parseQuery($query);
            $correctedQuery = $this->correctTypos($query);

            if ($correctedQuery !== strtolower($query)) {
                $searchMeta['corrected_query'] = $correctedQuery;
            }

            // STRATEGY 1: Exact phrase match (highest priority)
            $exactResults = $this->exactPhraseSearch($query, $filters);
            if ($exactResults->isNotEmpty()) {
                $results = $results->merge($exactResults);
                $searchMeta['strategies_used'][] = 'exact_phrase';
            }

            // STRATEGY 2: All words match
            if ($results->count() < $this->config['max_results']) {
                $allWordsResults = $this->allWordsSearch($parsed['tokens'], $filters, $results->pluck('id')->toArray());
                if ($allWordsResults->isNotEmpty()) {
                    $results = $results->merge($allWordsResults);
                    $searchMeta['strategies_used'][] = 'all_words';
                }
            }

            // STRATEGY 3: Any word match with relevance scoring
            if ($results->count() < 20) {
                $anyWordResults = $this->anyWordSearch($parsed['tokens'], $filters, $results->pluck('id')->toArray());
                if ($anyWordResults->isNotEmpty()) {
                    $results = $results->merge($anyWordResults);
                    $searchMeta['strategies_used'][] = 'any_word';
                }
            }

            // STRATEGY 4: Brand + Category combined search
            if ($results->count() < 20) {
                $brandCatResults = $this->brandCategorySearch($parsed, $filters, $results->pluck('id')->toArray());
                if ($brandCatResults->isNotEmpty()) {
                    $results = $results->merge($brandCatResults);
                    $searchMeta['strategies_used'][] = 'brand_category';
                }
            }

            // STRATEGY 5: Fuzzy/Typo-tolerant search
            if ($results->count() < 10) {
                $fuzzyResults = $this->fuzzySearch($query, $correctedQuery, $filters, $results->pluck('id')->toArray());
                if ($fuzzyResults->isNotEmpty()) {
                    $results = $results->merge($fuzzyResults);
                    $searchMeta['strategies_used'][] = 'fuzzy';
                }
            }

            // STRATEGY 6: Synonym expansion
            if ($results->count() < 10) {
                $synonymResults = $this->synonymSearch($parsed['tokens'], $filters, $results->pluck('id')->toArray());
                if ($synonymResults->isNotEmpty()) {
                    $results = $results->merge($synonymResults);
                    $searchMeta['strategies_used'][] = 'synonyms';
                }
            }

            // STRATEGY 7: Related products (last resort)
            if ($results->isEmpty()) {
                $relatedResults = $this->relatedProductsSearch($parsed, $filters);
                if ($relatedResults->isNotEmpty()) {
                    $results = $relatedResults;
                    $searchMeta['strategies_used'][] = 'related';
                    $searchMeta['showing_related'] = true;
                }
            }

            // Remove duplicates and score results
            $results = $results->unique('id');
            $scoredResults = $this->scoreResults($results, $parsed, $query);
            $sortedResults = $scoredResults->sortByDesc('relevance_score')->values();

            // Apply filters
            $filteredResults = $this->applyFilters($sortedResults, $filters);

            // Generate facets for filtering UI
            $facets = $this->generateFacets($filteredResults);

            // Return raw data for caching (WITHOUT paginator)
            return [
                'filteredResults' => $filteredResults,
                'total' => $filteredResults->count(),
                'intent' => $parsed,
                'facets' => $facets,
                'suggestions' => $this->generateSuggestions($parsed, $facets, $query),
                'did_you_mean' => $searchMeta['corrected_query'],
                'meta' => $searchMeta,
            ];
        });

        // Paginate OUTSIDE cache (so page number is always current)
        $filteredResults = $cachedData['filteredResults'];
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $filteredResults->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $currentItems,
            $cachedData['total'],
            $perPage,
            $currentPage,
            ['path' => url('/search'), 'query' => request()->query()]
        );

        return [
            'results' => $paginator,
            'total' => $cachedData['total'],
            'ai_powered' => false,
            'intent' => $cachedData['intent'],
            'facets' => $cachedData['facets'],
            'filters_applied' => $filters,
            'suggestions' => $cachedData['suggestions'],
            'did_you_mean' => $cachedData['did_you_mean'],
            'meta' => $cachedData['meta'],
        ];
    }

    /**
     * Strategy 1: Exact phrase match
     */
    protected function exactPhraseSearch(string $query, array $filters): Collection
    {
        return $this->baseQuery($filters)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('slug', 'LIKE', "%{$query}%");
            })
            ->limit(50)
            ->get();
    }

    /**
     * Strategy 2: All words must match
     */
    protected function allWordsSearch(array $tokens, array $filters, array $excludeIds = []): Collection
    {
        if (empty($tokens)) return collect();

        $query = $this->baseQuery($filters);

        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        $query->where(function ($q) use ($tokens) {
            foreach ($tokens as $token) {
                if (strlen($token) >= 2) {
                    $q->where(function ($inner) use ($token) {
                        $inner->where('name', 'LIKE', "%{$token}%")
                              ->orWhere('details', 'LIKE', "%{$token}%");
                    });
                }
            }
        });

        return $query->limit(50)->get();
    }

    /**
     * Strategy 3: Any word match
     */
    protected function anyWordSearch(array $tokens, array $filters, array $excludeIds = []): Collection
    {
        if (empty($tokens)) return collect();

        $query = $this->baseQuery($filters);

        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        $query->where(function ($q) use ($tokens) {
            foreach ($tokens as $token) {
                if (strlen($token) >= 2) {
                    $q->orWhere('name', 'LIKE', "%{$token}%");
                }
            }
        });

        return $query->limit(30)->get();
    }

    /**
     * Strategy 4: Brand + Category combination
     */
    protected function brandCategorySearch(array $parsed, array $filters, array $excludeIds = []): Collection
    {
        $results = collect();

        // Find matching brands
        if (!empty($parsed['detected_brand'])) {
            $brandQuery = $this->baseQuery($filters);
            if (!empty($excludeIds)) {
                $brandQuery->whereNotIn('products.id', $excludeIds);
            }

            $brandQuery->whereHas('brand', function ($q) use ($parsed) {
                $q->where('name', 'LIKE', "%{$parsed['detected_brand']}%");
            });

            // If we also have a category/product type, add that
            if (!empty($parsed['detected_category'])) {
                $brandQuery->where(function ($q) use ($parsed) {
                    $q->where('name', 'LIKE', "%{$parsed['detected_category']}%")
                      ->orWhereHas('category', function ($cq) use ($parsed) {
                          $cq->where('name', 'LIKE', "%{$parsed['detected_category']}%");
                      });
                });
            }

            $results = $brandQuery->limit(30)->get();
        }

        return $results;
    }

    /**
     * Strategy 5: Fuzzy matching for typos
     */
    protected function fuzzySearch(string $originalQuery, string $correctedQuery, array $filters, array $excludeIds = []): Collection
    {
        $results = collect();

        // Search with corrected query
        if ($correctedQuery !== strtolower($originalQuery)) {
            $query = $this->baseQuery($filters);
            if (!empty($excludeIds)) {
                $query->whereNotIn('id', $excludeIds);
            }

            $query->where('name', 'LIKE', "%{$correctedQuery}%");
            $results = $query->limit(20)->get();
        }

        // Also try searching each corrected word
        $words = explode(' ', $correctedQuery);
        foreach ($words as $word) {
            if (strlen($word) >= 3 && $results->count() < 20) {
                $existingIds = array_merge($excludeIds, $results->pluck('id')->toArray());

                $query = $this->baseQuery($filters);
                $query->whereNotIn('id', $existingIds);
                $query->where('name', 'LIKE', "%{$word}%");

                $results = $results->merge($query->limit(10)->get());
            }
        }

        return $results;
    }

    /**
     * Strategy 6: Synonym expansion
     */
    protected function synonymSearch(array $tokens, array $filters, array $excludeIds = []): Collection
    {
        $expandedTerms = [];

        foreach ($tokens as $token) {
            $expandedTerms[] = $token;
            if (isset($this->synonyms[$token])) {
                $expandedTerms = array_merge($expandedTerms, $this->synonyms[$token]);
            }
        }

        $expandedTerms = array_unique($expandedTerms);

        if (empty($expandedTerms)) return collect();

        $query = $this->baseQuery($filters);
        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        $query->where(function ($q) use ($expandedTerms) {
            foreach ($expandedTerms as $term) {
                $q->orWhere('name', 'LIKE', "%{$term}%");
            }
        });

        return $query->limit(20)->get();
    }

    /**
     * Strategy 7: Related products (when nothing else works)
     */
    protected function relatedProductsSearch(array $parsed, array $filters): Collection
    {
        // Try to find products in similar categories
        $categoryTerms = ['electronics', 'appliances', 'home', 'kitchen'];

        foreach ($parsed['tokens'] as $token) {
            $categoryTerms[] = $token;
        }

        $query = $this->baseQuery($filters);
        $query->where(function ($q) use ($categoryTerms) {
            foreach ($categoryTerms as $term) {
                $q->orWhereHas('category', function ($cq) use ($term) {
                    $cq->where('name', 'LIKE', "%{$term}%");
                });
            }
        });

        return $query->inRandomOrder()->limit(20)->get();
    }

    /**
     * Base query with eager loading and status filter
     */
    protected function baseQuery(array $filters = [])
    {
        $query = Product::query()
            ->with(['brand:id,name,image', 'category:id,name', 'reviews'])
            ->where('status', 1);

        // Only in-stock if filter set
        if (!empty($filters['in_stock'])) {
            $query->where('current_stock', '>', 0);
        }

        return $query;
    }

    /**
     * Apply all filters to results
     */
    protected function applyFilters(Collection $results, array $filters): Collection
    {
        // Price range filter
        if (!empty($filters['price_min'])) {
            $results = $results->filter(fn($p) => $p->unit_price >= $filters['price_min']);
        }
        if (!empty($filters['price_max'])) {
            $results = $results->filter(fn($p) => $p->unit_price <= $filters['price_max']);
        }

        // Brand filter
        if (!empty($filters['brand'])) {
            $brandName = strtolower($filters['brand']);
            $results = $results->filter(fn($p) =>
                $p->brand && strtolower($p->brand->name) === $brandName
            );
        }

        // Category filter
        if (!empty($filters['category'])) {
            $catName = strtolower($filters['category']);
            $results = $results->filter(fn($p) =>
                $p->category && strtolower($p->category->name) === $catName
            );
        }

        // Rating filter
        if (!empty($filters['min_rating'])) {
            $results = $results->filter(function ($p) use ($filters) {
                $avgRating = $p->reviews->avg('rating') ?? 0;
                return $avgRating >= $filters['min_rating'];
            });
        }

        // In stock filter
        if (!empty($filters['in_stock'])) {
            $results = $results->filter(fn($p) => $p->current_stock > 0);
        }

        // Discount filter
        if (!empty($filters['has_discount'])) {
            $results = $results->filter(fn($p) => $p->discount > 0);
        }

        // Sort
        if (!empty($filters['sort'])) {
            $results = match($filters['sort']) {
                'price_low' => $results->sortBy('unit_price'),
                'price_high' => $results->sortByDesc('unit_price'),
                'newest' => $results->sortByDesc('created_at'),
                'rating' => $results->sortByDesc(fn($p) => $p->reviews->avg('rating') ?? 0),
                'name_asc' => $results->sortBy('name'),
                'name_desc' => $results->sortByDesc('name'),
                default => $results,
            };
        }

        return $results->values();
    }

    /**
     * Parse query into tokens and detect intent
     */
    protected function parseQuery(string $query): array
    {
        $query = strtolower(trim($query));
        $tokens = preg_split('/[\s\-\_\+\,\.]+/', $query, -1, PREG_SPLIT_NO_EMPTY);

        // Remove common stop words
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'for', 'in', 'on', 'at', 'to', 'buy', 'get', 'want', 'need', 'looking'];
        $tokens = array_filter($tokens, fn($t) => !in_array($t, $stopWords) && strlen($t) >= 2);
        $tokens = array_values($tokens);

        // Detect brand
        $detectedBrand = $this->detectBrand($tokens);

        // Detect category/product type
        $detectedCategory = $this->detectCategory($tokens);

        return [
            'original' => $query,
            'tokens' => $tokens,
            'detected_brand' => $detectedBrand,
            'detected_category' => $detectedCategory,
        ];
    }

    /**
     * Detect brand from tokens
     */
    protected function detectBrand(array $tokens): ?string
    {
        $brands = [
            'hisense' => ['hisense', 'hi-sense', 'hisence', 'hisense'],
            'samsung' => ['samsung', 'samsong', 'samsoung'],
            'lg' => ['lg', 'l.g', 'life good'],
            'sony' => ['sony', 'sonny'],
            'tcl' => ['tcl', 't.c.l'],
            'skyworth' => ['skyworth', 'sky worth'],
            'ramtons' => ['ramtons', 'ramton'],
            'hotpoint' => ['hotpoint', 'hot point'],
            'bruhm' => ['bruhm', 'brum'],
            'changhong' => ['changhong', 'chang hong'],
            'haier' => ['haier', 'haire'],
            'midea' => ['midea', 'media'],
            'vitron' => ['vitron', 'vytron'],
            'nunix' => ['nunix', 'nunux'],
            'von' => ['von', 'vonn'],
            'mika' => ['mika'],
            'armco' => ['armco'],
            'nexus' => ['nexus'],
            'whirlpool' => ['whirlpool', 'whirl pool'],
            'bosch' => ['bosch', 'bosh'],
            'philips' => ['philips', 'phillips'],
            'panasonic' => ['panasonic', 'pana sonic'],
            'sharp' => ['sharp'],
            'toshiba' => ['toshiba', 'tosiba'],
        ];

        foreach ($tokens as $token) {
            foreach ($brands as $brand => $variants) {
                foreach ($variants as $variant) {
                    if ($token === $variant || similar_text($token, $variant) / max(strlen($token), strlen($variant)) > 0.8) {
                        return $brand;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Detect product category from tokens
     */
    protected function detectCategory(array $tokens): ?string
    {
        $categories = [
            'refrigerator' => ['fridge', 'refrigerator', 'freezer', 'fridges', 'refrigerators'],
            'television' => ['tv', 'television', 'smart tv', 'led tv', 'oled'],
            'washing machine' => ['washing', 'washer', 'laundry'],
            'air conditioner' => ['ac', 'aircon', 'air conditioner', 'cooling'],
            'microwave' => ['microwave', 'micro wave'],
            'cooker' => ['cooker', 'stove', 'oven', 'range'],
            'blender' => ['blender', 'mixer', 'juicer'],
            'speaker' => ['speaker', 'soundbar', 'subwoofer', 'woofer'],
            'fan' => ['fan', 'standing fan', 'ceiling fan'],
            'iron' => ['iron', 'pressing', 'steam iron'],
            'kettle' => ['kettle', 'jug', 'water heater'],
            'vacuum' => ['vacuum', 'cleaner', 'hoover'],
        ];

        foreach ($tokens as $token) {
            foreach ($categories as $category => $keywords) {
                if (in_array($token, $keywords)) {
                    return $category;
                }
            }
        }

        return null;
    }

    /**
     * Correct common typos
     */
    protected function correctTypos(string $query): string
    {
        $query = strtolower($query);

        foreach ($this->commonTypos as $typo => $correction) {
            $query = str_replace($typo, $correction, $query);
        }

        return $query;
    }

    /**
     * Score results by relevance
     */
    protected function scoreResults(Collection $results, array $parsed, string $originalQuery): Collection
    {
        $queryLower = strtolower($originalQuery);
        $tokens = $parsed['tokens'];

        return $results->map(function ($product) use ($queryLower, $tokens, $parsed) {
            $score = 0;
            $name = strtolower($product->name ?? '');
            $brandName = strtolower($product->brand->name ?? '');
            $categoryName = strtolower($product->category->name ?? '');

            // Exact phrase match in name - highest score
            if (str_contains($name, $queryLower)) {
                $score += 1000;
            }

            // Brand match
            if ($parsed['detected_brand'] && str_contains($brandName, $parsed['detected_brand'])) {
                $score += 500;
            }
            if ($parsed['detected_brand'] && str_contains($name, $parsed['detected_brand'])) {
                $score += 300;
            }

            // Category match
            if ($parsed['detected_category'] && str_contains($name, $parsed['detected_category'])) {
                $score += 400;
            }
            if ($parsed['detected_category'] && str_contains($categoryName, $parsed['detected_category'])) {
                $score += 200;
            }

            // Token matches
            $tokensMatched = 0;
            foreach ($tokens as $token) {
                if (str_contains($name, $token)) {
                    $score += 100;
                    $tokensMatched++;
                }
                if (str_contains($brandName, $token)) {
                    $score += 50;
                    $tokensMatched++;
                }
            }

            // Bonus for multiple token matches
            if ($tokensMatched > 1) {
                $score += $tokensMatched * 50;
            }

            // Boost for in-stock items
            if ($product->current_stock > 0) {
                $score += 50;
            }

            // Boost for items with reviews
            if ($product->reviews && $product->reviews->count() > 0) {
                $avgRating = $product->reviews->avg('rating') ?? 0;
                $score += $avgRating * 10;
            }

            // Boost for discounted items
            if ($product->discount > 0) {
                $score += 25;
            }

            $product->relevance_score = $score;
            return $product;
        });
    }

    /**
     * Generate facets for filter UI
     */
    protected function generateFacets(Collection $results): array
    {
        if ($results->isEmpty()) {
            return [
                'brands' => [],
                'categories' => [],
                'price_ranges' => [],
                'ratings' => [],
            ];
        }

        // Brand facets
        $brands = $results
            ->filter(fn($p) => $p->brand)
            ->groupBy(fn($p) => $p->brand->name)
            ->map->count()
            ->sortDesc()
            ->take(15)
            ->toArray();

        // Category facets
        $categories = $results
            ->filter(fn($p) => $p->category)
            ->groupBy(fn($p) => $p->category->name)
            ->map->count()
            ->sortDesc()
            ->take(10)
            ->toArray();

        // Price ranges
        $prices = $results->pluck('unit_price')->filter();
        $minPrice = $prices->min() ?? 0;
        $maxPrice = $prices->max() ?? 0;

        $priceRanges = [];
        if ($maxPrice > 0) {
            $ranges = [
                ['min' => 0, 'max' => 100000, 'label' => 'Under 100,000 UGX'],
                ['min' => 100000, 'max' => 500000, 'label' => '100,000 - 500,000 UGX'],
                ['min' => 500000, 'max' => 1000000, 'label' => '500,000 - 1,000,000 UGX'],
                ['min' => 1000000, 'max' => 2000000, 'label' => '1,000,000 - 2,000,000 UGX'],
                ['min' => 2000000, 'max' => 5000000, 'label' => '2,000,000 - 5,000,000 UGX'],
                ['min' => 5000000, 'max' => null, 'label' => 'Above 5,000,000 UGX'],
            ];

            foreach ($ranges as $range) {
                $count = $results->filter(function ($p) use ($range) {
                    if ($range['max'] === null) {
                        return $p->unit_price >= $range['min'];
                    }
                    return $p->unit_price >= $range['min'] && $p->unit_price < $range['max'];
                })->count();

                if ($count > 0) {
                    $priceRanges[] = array_merge($range, ['count' => $count]);
                }
            }
        }

        // Rating facets
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

        return [
            'brands' => $brands,
            'categories' => $categories,
            'price_ranges' => $priceRanges,
            'ratings' => $ratings,
            'total_results' => $results->count(),
            'in_stock_count' => $results->filter(fn($p) => $p->current_stock > 0)->count(),
            'with_discount_count' => $results->filter(fn($p) => $p->discount > 0)->count(),
        ];
    }

    /**
     * Generate search suggestions
     */
    protected function generateSuggestions(array $parsed, array $facets, string $query): array
    {
        $suggestions = [];

        // Suggest by top brands found
        if (!empty($facets['brands'])) {
            $topBrands = array_slice(array_keys($facets['brands']), 0, 3);
            foreach ($topBrands as $brand) {
                $suggestions[] = "{$brand} " . ($parsed['detected_category'] ?? 'products');
            }
        }

        // Suggest related categories
        if (!empty($facets['categories'])) {
            foreach (array_slice(array_keys($facets['categories']), 0, 2) as $cat) {
                $suggestions[] = $cat;
            }
        }

        return array_slice(array_unique($suggestions), 0, 5);
    }

    /**
     * Empty response structure
     */
    protected function emptyResponse(int $perPage): array
    {
        return [
            'results' => new LengthAwarePaginator([], 0, $perPage),
            'total' => 0,
            'ai_powered' => false,
            'intent' => [],
            'facets' => [],
            'filters_applied' => [],
            'suggestions' => [],
            'did_you_mean' => null,
            'meta' => [],
        ];
    }

    /**
     * Initialize typos dictionary and synonyms
     */
    protected function initializeTyposAndSynonyms(): void
    {
        $this->commonTypos = [
            // Brand typos
            'hisence' => 'hisense',
            'hisense' => 'hisense',
            'samsong' => 'samsung',
            'samsoung' => 'samsung',
            'philips' => 'philips',
            'phillips' => 'philips',
            'panasnic' => 'panasonic',
            'tosiba' => 'toshiba',

            // Product typos
            'refridgerator' => 'refrigerator',
            'refridgrator' => 'refrigerator',
            'refrigerator' => 'refrigerator',
            'frige' => 'fridge',
            'fridhe' => 'fridge',
            'frezer' => 'freezer',
            'frezzer' => 'freezer',
            'telivision' => 'television',
            'televison' => 'television',
            'micorwave' => 'microwave',
            'micowave' => 'microwave',
            'wahsing' => 'washing',
            'washng' => 'washing',
            'machne' => 'machine',
            'speker' => 'speaker',
            'speeker' => 'speaker',
            'airconditioner' => 'air conditioner',
            'aircon' => 'air conditioner',
        ];

        $this->synonyms = [
            'fridge' => ['refrigerator', 'freezer', 'cooler'],
            'refrigerator' => ['fridge', 'freezer', 'cooler'],
            'tv' => ['television', 'smart tv', 'led tv'],
            'television' => ['tv', 'smart tv', 'led tv'],
            'ac' => ['air conditioner', 'aircon', 'cooling'],
            'washing' => ['laundry', 'washer'],
            'cooker' => ['stove', 'range', 'oven'],
            'blender' => ['mixer', 'food processor', 'juicer'],
            'speaker' => ['sound system', 'soundbar', 'audio'],
            'phone' => ['mobile', 'smartphone', 'cellphone'],
            'laptop' => ['notebook', 'computer', 'pc'],
        ];
    }
}
