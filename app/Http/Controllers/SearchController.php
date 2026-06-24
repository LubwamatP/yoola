<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SearchAnalytics;
use App\Services\EnhancedSearchService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    protected EnhancedSearchService $searchService;

    public function __construct(EnhancedSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Main search endpoint - Amazon-style with advanced filters
     */
    public function search(Request $request)
    {
        $query = trim($request->input('q', $request->input('query', '')));
        $startTime = microtime(true);

        // Validate query
        if (empty($query)) {
            return redirect()->route('home');
        }

        try {
            // Collect all filters
            $filters = [
                'brand' => $request->input('brand'),
                'category' => $request->input('category'),
                'price_min' => $request->input('price_min') ? (float) $request->input('price_min') : null,
                'price_max' => $request->input('price_max') ? (float) $request->input('price_max') : null,
                'min_rating' => $request->input('rating') ? (int) $request->input('rating') : null,
                'in_stock' => $request->boolean('in_stock'),
                'has_discount' => $request->boolean('discount'),
                'sort' => $request->input('sort', 'relevance'),
            ];

            // Remove null filters
            $filters = array_filter($filters, fn($v) => $v !== null && $v !== '');

            $perPage = min((int) $request->input('limit', 20), 48);

            // Execute search
            $searchResponse = $this->searchService->search($query, $filters, $perPage);

            // Calculate response time
            $responseTime = round((microtime(true) - $startTime) * 1000);

            // Log search analytics
            $this->logSearchAnalytics($query, $searchResponse, $responseTime, $filters);

            // Prepare view data
            $viewData = [
                'products' => $searchResponse['results'],
                'total' => $searchResponse['total'],
                'query' => $query,
                'meta' => $searchResponse,
                'facets' => $searchResponse['facets'] ?? [],
                'ai_powered' => $searchResponse['ai_powered'] ?? false,
                'intent' => $searchResponse['intent'] ?? null,
                'suggestions' => $searchResponse['suggestions'] ?? [],
                'did_you_mean' => $searchResponse['did_you_mean'] ?? null,
                'filters' => $filters,
                'response_time' => $responseTime,
                'sort_options' => $this->getSortOptions(),
            ];

            return view('theme-views.search.index', $viewData);

        } catch (\Throwable $e) {
            Log::error("Search Failed: " . $e->getMessage(), [
                'query' => $query,
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback to simple search
            return $this->fallbackSearch($request, $query, $startTime);
        }
    }

    /**
     * Simple fallback search when enhanced search fails
     */
    protected function fallbackSearch(Request $request, string $query, float $startTime)
    {
        $products = Product::where('status', 1)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('details', 'like', "%{$query}%")
                  ->orWhere('slug', 'like', "%{$query}%");
            })
            ->with(['brand', 'category', 'reviews'])
            ->paginate(20);

        return view('theme-views.search.index', [
            'products' => $products,
            'total' => $products->total(),
            'query' => $query,
            'meta' => [],
            'facets' => [],
            'ai_powered' => false,
            'intent' => null,
            'suggestions' => [],
            'did_you_mean' => null,
            'filters' => [],
            'response_time' => round((microtime(true) - $startTime) * 1000),
            'sort_options' => $this->getSortOptions(),
        ]);
    }

    /**
     * AJAX endpoint for live search suggestions
     */
    public function suggestions(Request $request)
    {
        $query = trim($request->input('q', ''));

        if (strlen($query) < 2) {
            return response()->json(['suggestions' => [], 'categories' => [], 'brands' => []]);
        }

        try {
            $cacheKey = 'search_suggestions:' . md5($query);

            $data = Cache::remember($cacheKey, 300, function () use ($query) {
                // Product suggestions
                $products = Product::where('status', 1)
                    ->where('name', 'like', "%{$query}%")
                    ->with(['brand:id,name', 'category:id,name'])
                    ->select('id', 'name', 'slug', 'thumbnail', 'unit_price', 'brand_id', 'category_id', 'discount', 'discount_type')
                    ->limit(8)
                    ->get();

                $suggestions = $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'thumbnail' => $product->thumbnail_full_url['path'] ?? null,
                        'price' => number_format($product->unit_price) . ' UGX',
                        'brand' => $product->brand->name ?? null,
                        'has_discount' => $product->discount > 0,
                    ];
                });

                // Category suggestions
                $categories = Category::where('name', 'like', "%{$query}%")
                    ->where('position', 0)
                    ->select('id', 'name', 'slug', 'icon')
                    ->limit(4)
                    ->get()
                    ->map(fn($c) => ['name' => $c->name, 'slug' => $c->slug]);

                // Brand suggestions
                $brands = Brand::where('name', 'like', "%{$query}%")
                    ->select('id', 'name', 'image')
                    ->limit(4)
                    ->get()
                    ->map(fn($b) => ['name' => $b->name, 'image' => $b->image_full_url['path'] ?? null]);

                // Popular searches matching query
                $popular = [];
                if (class_exists(\App\Models\PopularSearch::class)) {
                    $popular = \App\Models\PopularSearch::where('query', 'like', "%{$query}%")
                        ->orderByDesc('search_count')
                        ->limit(4)
                        ->pluck('query')
                        ->toArray();
                }

                return [
                    'suggestions' => $suggestions,
                    'categories' => $categories,
                    'brands' => $brands,
                    'popular' => $popular,
                ];
            });

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error('Search suggestions failed', ['error' => $e->getMessage()]);
            return response()->json(['suggestions' => [], 'categories' => [], 'brands' => []]);
        }
    }

    /**
     * API endpoint for search (for mobile apps)
     */
    public function apiSearch(Request $request)
    {
        $query = trim($request->input('q', ''));
        $startTime = microtime(true);

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required',
            ], 400);
        }

        try {
            $filters = $request->only(['category', 'brand', 'price_min', 'price_max', 'rating', 'in_stock', 'sort']);
            $perPage = min((int) $request->input('limit', 20), 48);

            $searchResponse = $this->searchService->search($query, $filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $searchResponse['results']->items(),
                    'total' => $searchResponse['total'],
                    'current_page' => $searchResponse['results']->currentPage(),
                    'last_page' => $searchResponse['results']->lastPage(),
                    'per_page' => $perPage,
                ],
                'meta' => [
                    'ai_powered' => $searchResponse['ai_powered'] ?? false,
                    'intent' => $searchResponse['intent'] ?? null,
                    'suggestions' => $searchResponse['suggestions'] ?? [],
                    'did_you_mean' => $searchResponse['did_you_mean'] ?? null,
                    'facets' => $searchResponse['facets'] ?? [],
                    'response_time_ms' => round((microtime(true) - $startTime) * 1000),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('API Search failed', ['query' => $query, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Get filter options for dropdowns
     */
    public function getFilterOptions(Request $request)
    {
        try {
            $data = Cache::remember('search_filter_options', 3600, function () {
                return [
                    'brands' => Brand::orderBy('name')
                        ->select('id', 'name')
                        ->get()
                        ->map(fn($b) => ['id' => $b->id, 'name' => $b->name]),

                    'categories' => Category::where('position', 0)
                        ->orderBy('name')
                        ->select('id', 'name')
                        ->get()
                        ->map(fn($c) => ['id' => $c->id, 'name' => $c->name]),

                    'price_ranges' => [
                        ['min' => 0, 'max' => 100000, 'label' => 'Under 100K UGX'],
                        ['min' => 100000, 'max' => 500000, 'label' => '100K - 500K UGX'],
                        ['min' => 500000, 'max' => 1000000, 'label' => '500K - 1M UGX'],
                        ['min' => 1000000, 'max' => 2000000, 'label' => '1M - 2M UGX'],
                        ['min' => 2000000, 'max' => 5000000, 'label' => '2M - 5M UGX'],
                        ['min' => 5000000, 'max' => null, 'label' => 'Above 5M UGX'],
                    ],
                ];
            });

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Sort options for search results
     */
    protected function getSortOptions(): array
    {
        return [
            'relevance' => 'Most Relevant',
            'price_low' => 'Price: Low to High',
            'price_high' => 'Price: High to Low',
            'newest' => 'Newest Arrivals',
            'rating' => 'Customer Rating',
            'name_asc' => 'Name: A to Z',
            'name_desc' => 'Name: Z to A',
        ];
    }

    /**
     * Log search analytics for monitoring and improvement
     */
    protected function logSearchAnalytics(string $query, array $response, int $responseTime, array $filters = []): void
    {
        try {
            if (!class_exists(SearchAnalytics::class)) {
                return;
            }

            SearchAnalytics::create([
                'query' => substr($query, 0, 500),
                'results_count' => $response['total'] ?? 0,
                'ai_powered' => $response['ai_powered'] ?? false,
                'intent' => json_encode($response['intent'] ?? null),
                'confidence' => $response['intent']['confidence'] ?? null,
                'response_time_ms' => $responseTime,
                'user_id' => auth('customer')->id(),
                'session_id' => session()->getId(),
                'filters' => json_encode($filters),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::debug('Failed to log search analytics', ['error' => $e->getMessage()]);
        }
    }
}
