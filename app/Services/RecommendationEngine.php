<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStat;
use App\Models\ProductCoView;
use App\Models\UserPreference;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RecommendationEngine
{
    protected const CACHE_TTL_MINUTES = 30;
    protected const CACHE_PREFIX = 'recommendations:';

    /**
     * Price tolerance multipliers for recommendations
     * A customer viewing a 400K TV shouldn't see an 8M TV
     */
    protected const PRICE_TOLERANCE_LOWER = 0.3;  // Show products down to 30% of viewed price
    protected const PRICE_TOLERANCE_UPPER = 2.5;  // Show products up to 250% of viewed price

    /**
     * Cross-sell category mappings (complementary products)
     * Format: 'category_slug' => ['complementary_category_slugs']
     */
    protected array $crossSellMappings = [
        // Electronics
        'televisions' => ['sound-bars', 'woofers', 'speakers', 'tv-stands', 'hdmi-cables', 'streaming-devices'],
        'tv' => ['sound-bars', 'woofers', 'speakers', 'tv-stands', 'hdmi-cables', 'streaming-devices'],
        'laptops' => ['laptop-bags', 'mouse', 'keyboards', 'laptop-stands', 'usb-hubs', 'headphones'],
        'phones' => ['phone-cases', 'screen-protectors', 'chargers', 'earphones', 'power-banks'],
        'smartphones' => ['phone-cases', 'screen-protectors', 'chargers', 'earphones', 'power-banks'],
        'cameras' => ['memory-cards', 'camera-bags', 'tripods', 'lenses', 'camera-straps'],
        'gaming-consoles' => ['controllers', 'gaming-headsets', 'games', 'charging-docks'],

        // Home & Kitchen
        'refrigerators' => ['water-filters', 'fridge-organizers', 'ice-makers'],
        'washing-machines' => ['detergents', 'fabric-softeners', 'laundry-baskets'],
        'cookers' => ['cookware', 'utensils', 'gas-cylinders'],
        'microwaves' => ['microwave-covers', 'microwave-cookware'],

        // Fashion
        'shoes' => ['socks', 'shoe-care', 'insoles', 'shoe-racks'],
        'dresses' => ['jewelry', 'handbags', 'belts', 'scarves'],
        'suits' => ['ties', 'cufflinks', 'dress-shirts', 'formal-shoes'],

        // Furniture
        'beds' => ['mattresses', 'pillows', 'bedsheets', 'bed-frames'],
        'sofas' => ['cushions', 'throws', 'coffee-tables', 'rugs'],
        'desks' => ['office-chairs', 'desk-lamps', 'desk-organizers'],
    ];

    /**
     * Get trending products (high velocity, recent activity)
     */
    public function getTrending(int $limit = 12): Collection
    {
        $cacheKey = self::CACHE_PREFIX . "trending:{$limit}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($limit) {
            try {
                $trending = ProductStat::getTrending($limit);

                // Fallback to latest products if no trending data
                if ($trending->isEmpty()) {
                    return $this->getFallbackProducts($limit);
                }

                return $trending;
            } catch (\Exception $e) {
                Log::warning('Failed to get trending products', ['error' => $e->getMessage()]);
                return $this->getFallbackProducts($limit);
            }
        });
    }

    /**
     * Get popular products (long-term popularity)
     */
    public function getPopular(int $limit = 12): Collection
    {
        $cacheKey = self::CACHE_PREFIX . "popular:{$limit}";

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($limit) {
            try {
                $popular = ProductStat::getPopular($limit);

                if ($popular->isEmpty()) {
                    return $this->getBestSelling($limit);
                }

                return $popular;
            } catch (\Exception $e) {
                Log::warning('Failed to get popular products', ['error' => $e->getMessage()]);
                return $this->getBestSelling($limit);
            }
        });
    }

    /**
     * Get hot now products (high activity in last hour)
     */
    public function getHotNow(int $limit = 8): Collection
    {
        $cacheKey = self::CACHE_PREFIX . "hot_now:{$limit}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($limit) {
            try {
                return ProductStat::getHotNow($limit);
            } catch (\Exception $e) {
                Log::warning('Failed to get hot now products', ['error' => $e->getMessage()]);
                return collect();
            }
        });
    }

    /**
     * Get personalized recommendations for the current user
     */
    public function getPersonalized(int $limit = 12, ?int $userId = null): Collection
    {
        $userId = $userId ? Auth::guard('customer')->id() : null;

        if (!$userId) {
            return $this->getTrending($limit);
        }

        $cacheKey = self::CACHE_PREFIX . "personalized:{$userId}:{$limit}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($userId, $limit) {
            try {
                $preferences = UserPreference::find($userId);

                if (!$preferences) {
                    return $this->getTrending($limit);
                }

                return $this->buildPersonalizedRecommendations($preferences, $limit);
            } catch (\Exception $e) {
                Log::warning('Failed to get personalized recommendations', [
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
                return $this->getTrending($limit);
            }
        });
    }

    /**
     * Build personalized recommendations based on user preferences
     */
    protected function buildPersonalizedRecommendations(UserPreference $preferences, int $limit): Collection
    {
        $recommendations = collect();

        // Get top categories and brands
        $topCategories = array_keys($preferences->getTopCategories(3));
        $topBrands = array_keys($preferences->getTopBrands(3));
        $recentlyViewed = $preferences->getRecentlyViewedIds(10);

        // Query products matching preferences
        $query = Product::active()
            ->where('current_stock', '>', 0)
            ->with(['brand', 'category', 'reviews'])
            ->whereNotIn('id', $recentlyViewed); // Exclude recently viewed

        // Prioritize by category and brand affinity
        if (!empty($topCategories)) {
            $query->where(function ($q) use ($topCategories) {
                $q->whereIn('category_id', $topCategories);
                foreach ($topCategories as $categoryId) {
                    $q->orWhere('category_ids', 'like', "%{$categoryId}%");
                }
            });
        }

        $products = $query->limit($limit * 2)->get();

        // Score and sort products
        $scored = $products->map(function ($product) use ($preferences) {
            $product->recommendation_score = $preferences->getProductScore($product);
            return $product;
        })->sortByDesc('recommendation_score');

        $recommendations = $scored->take($limit);

        // Fill with trending if not enough
        if ($recommendations->count() < $limit) {
            $needed = $limit - $recommendations->count();
            $existingIds = $recommendations->pluck('id')->toArray();
            $trending = $this->getTrending($needed * 2)
                ->filter(fn($p) => !in_array($p->id, $existingIds))
                ->take($needed);
            $recommendations = $recommendations->concat($trending);
        }

        return $recommendations->values();
    }

    /**
     * Get "customers also viewed" recommendations for a product
     */
    public function getAlsoViewed(int $productId, int $limit = 8): Collection
    {
        $cacheKey = self::CACHE_PREFIX . "also_viewed:{$productId}:{$limit}";

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($productId, $limit) {
            try {
                $related = ProductCoView::getRelatedProducts($productId, $limit);

                if ($related->isEmpty()) {
                    return $this->getSimilarProducts($productId, $limit);
                }

                return $related;
            } catch (\Exception $e) {
                Log::warning('Failed to get also viewed products', [
                    'product_id' => $productId,
                    'error' => $e->getMessage()
                ]);
                return $this->getSimilarProducts($productId, $limit);
            }
        });
    }

    /**
     * Get similar products with PRICE-AWARE logic
     * A customer viewing a 400K TV won't see an 8M TV
     */
    public function getSimilarProducts(int $productId, int $limit = 8, ?int $userId = null): Collection
    {
        $cacheKey = self::CACHE_PREFIX . "similar_price_aware:{$productId}:{$limit}:" . ($userId ?? 'guest');

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($productId, $limit, $userId) {
            try {
                $product = Product::with('category')->find($productId);

                if (!$product) {
                    return collect();
                }

                // Calculate price range based on the viewed product
                $basePrice = $product->unit_price;
                $minPrice = $basePrice * self::PRICE_TOLERANCE_LOWER;
                $maxPrice = $basePrice * self::PRICE_TOLERANCE_UPPER;

                // If user has preferences, adjust price range based on their behavior
                if ($userId) {
                    $preferences = UserPreference::find($userId);
                    if ($preferences && $preferences->avg_viewed_price) {
                        // Blend product price with user's average viewed price
                        $userAvg = $preferences->avg_viewed_price;
                        $blendedPrice = ($basePrice * 0.7) + ($userAvg * 0.3);
                        $minPrice = $blendedPrice * self::PRICE_TOLERANCE_LOWER;
                        $maxPrice = $blendedPrice * self::PRICE_TOLERANCE_UPPER;
                    }
                }

                // Query similar products within price range
                $query = Product::active()
                    ->where('id', '!=', $productId)
                    ->where('current_stock', '>', 0)
                    ->whereBetween('unit_price', [$minPrice, $maxPrice])
                    ->with(['brand', 'category', 'reviews']);

                // Prioritize same category, then same brand
                $products = $query
                    ->where(function ($q) use ($product) {
                        $q->where('category_id', $product->category_id)
                            ->orWhere('sub_category_id', $product->category_id)
                            ->orWhere('brand_id', $product->brand_id);
                    })
                    ->limit($limit * 2)
                    ->get();

                // Score products by relevance
                $scored = $products->map(function ($p) use ($product, $basePrice) {
                    $score = 0;

                    // Category match (highest priority)
                    if ($p->category_id === $product->category_id) {
                        $score += 50;
                    } elseif ($p->sub_category_id === $product->category_id) {
                        $score += 30;
                    }

                    // Brand match
                    if ($product->brand_id && $p->brand_id === $product->brand_id) {
                        $score += 25;
                    }

                    // Price proximity (closer = better)
                    $priceDiff = abs($p->unit_price - $basePrice);
                    $priceProximity = 1 - ($priceDiff / max($basePrice, 1));
                    $score += $priceProximity * 25;

                    // Add some randomness for variety (0-10 points)
                    $score += rand(0, 10);

                    $p->similarity_score = $score;
                    return $p;
                });

                // Sort by score and take limit
                $result = $scored->sortByDesc('similarity_score')->take($limit);

                // If not enough products, fill with random from same category (ignoring price)
                if ($result->count() < $limit) {
                    $existingIds = $result->pluck('id')->toArray();
                    $existingIds[] = $productId;

                    $fallback = Product::active()
                        ->where('current_stock', '>', 0)
                        ->where('category_id', $product->category_id)
                        ->whereNotIn('id', $existingIds)
                        ->with(['brand', 'category', 'reviews'])
                        ->inRandomOrder()
                        ->limit($limit - $result->count())
                        ->get();

                    $result = $result->concat($fallback);
                }

                return $result->values();
            } catch (\Exception $e) {
                Log::warning('Failed to get similar products', [
                    'product_id' => $productId,
                    'error' => $e->getMessage()
                ]);
                return $this->getSimilarProductsFallback($productId, $limit);
            }
        });
    }

    /**
     * Fallback for similar products (no price filtering)
     */
    protected function getSimilarProductsFallback(int $productId, int $limit): Collection
    {
        try {
            $product = Product::find($productId);

            if (!$product) {
                return collect();
            }

            return Product::active()
                ->where('id', '!=', $productId)
                ->where('current_stock', '>', 0)
                ->where('category_id', $product->category_id)
                ->with(['brand', 'category', 'reviews'])
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get cross-sell products (complementary items)
     * E.g., viewing a TV?? Show woofers, sound bars at similar price points
     */
    public function getCrossSellProducts(int $productId, int $limit = 6, ?int $userId = null): Collection
    {
        $cacheKey = self::CACHE_PREFIX . "cross_sell:{$productId}:{$limit}";

        return Cache::remember($cacheKey, now()->addMinutes(20), function () use ($productId, $limit, $userId) {
            try {
                $product = Product::find($productId);

                if (!$product) {
                    return collect();
                }

                // Load category separately to avoid collection issues
                $category = Category::find($product->category_id);
                if (!$category) {
                    return collect();
                }

                // Get complementary category slugs
                $categorySlug = $category->slug ?? '';
                $complementarySlugs = $this->getComplementaryCategorySlugs($categorySlug);

                if (empty($complementarySlugs)) {
                    // No defined cross-sell mappings, try to find accessories in same parent category
                    return $this->getAccessoriesInSameParent($product, $limit, $userId);
                }

                // Get complementary category IDs
                $complementaryCategoryIds = Category::whereIn('slug', $complementarySlugs)
                    ->pluck('id')
                    ->toArray();

                if (empty($complementaryCategoryIds)) {
                    return $this->getAccessoriesInSameParent($product, $limit, $userId);
                }

                // Calculate price range for cross-sell (typically lower-priced accessories)
                $basePrice = $product->unit_price;
                // Cross-sell items are usually 5% to 75% of main product price
                $minPrice = $basePrice * 0.05;
                $maxPrice = $basePrice * 0.75;

                // Adjust based on user preferences if available
                if ($userId) {
                    $preferences = UserPreference::find($userId);
                    if ($preferences && $preferences->avg_viewed_price) {
                        // Allow slightly higher cross-sell for users who view expensive items
                        $maxPrice = max($maxPrice, $preferences->avg_viewed_price * 0.5);
                    }
                }

                $crossSellProducts = Product::active()
                    ->where('current_stock', '>', 0)
                    ->whereIn('category_id', $complementaryCategoryIds)
                    ->whereBetween('unit_price', [$minPrice, $maxPrice])
                    ->with(['brand', 'category', 'reviews'])
                    ->withCount('orderDetails')
                    ->orderByDesc('order_details_count') // Popular cross-sell items first
                    ->limit($limit)
                    ->get();

                // If not enough, expand price range
                if ($crossSellProducts->count() < $limit / 2) {
                    $crossSellProducts = Product::active()
                        ->where('current_stock', '>', 0)
                        ->whereIn('category_id', $complementaryCategoryIds)
                        ->where('unit_price', '<=', $basePrice) // Just don't exceed main product price
                        ->with(['brand', 'category', 'reviews'])
                        ->inRandomOrder()
                        ->limit($limit)
                        ->get();
                }

                return $crossSellProducts;
            } catch (\Exception $e) {
                Log::warning('Failed to get cross-sell products', [
                    'product_id' => $productId,
                    'error' => $e->getMessage()
                ]);
                return collect();
            }
        });
    }

    /**
     * Get complementary category slugs for cross-selling
     */
    protected function getComplementaryCategorySlugs(string $categorySlug): array
    {
        // Normalize slug
        $normalizedSlug = strtolower(trim($categorySlug));

        // Direct match
        if (isset($this->crossSellMappings[$normalizedSlug])) {
            return $this->crossSellMappings[$normalizedSlug];
        }

        // Partial match (e.g., 'smart-tv' matches 'tv')
        foreach ($this->crossSellMappings as $key => $values) {
            if (str_contains($normalizedSlug, $key) || str_contains($key, $normalizedSlug)) {
                return $values;
            }
        }

        return [];
    }

    /**
     * Get accessories/related items in same parent category
     * Fallback when no explicit cross-sell mapping exists
     */
    protected function getAccessoriesInSameParent(Product $product, int $limit, ?int $userId = null): Collection
    {
        try {
            // Load category separately to avoid collection issues
            $category = Category::find($product->category_id);
            if (!$category) {
                return collect();
            }

            // Get parent category
            $parentId = $category->parent_id;
            if (!$parentId) {
                $parentId = $category->id; // Use current category as parent
            }

            // Find sibling categories (same parent, different from current)
            $siblingCategoryIds = Category::where('parent_id', $parentId)
                ->where('id', '!=', $product->category_id)
                ->pluck('id')
                ->toArray();

            if (empty($siblingCategoryIds)) {
                return collect();
            }

            // Get products from sibling categories at lower price point
            $maxPrice = $product->unit_price * 0.6;

            return Product::active()
                ->where('current_stock', '>', 0)
                ->whereIn('category_id', $siblingCategoryIds)
                ->where('unit_price', '<=', $maxPrice)
                ->with(['brand', 'category', 'reviews'])
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get "Complete Your Purchase" recommendations
     * Combines similar products + cross-sell for product page
     */
    public function getProductPageRecommendations(int $productId, ?int $userId = null): array
    {
        $userId = $userId ? Auth::guard('customer')->id() : null;

        try {
            $product = Product::find($productId);
            $hasUserBehavior = false;

            // Check if user has behavior data
            if ($userId) {
                $preferences = UserPreference::find($userId);
                $hasUserBehavior = $preferences && $preferences->total_views > 5;
            }

            return [
                // Similar products (price-aware, behavior-informed)
                'similar_products' => $this->getSimilarProducts($productId, 8, $userId),

                // Cross-sell (complementary items)
                'frequently_bought_together' => $this->getCrossSellProducts($productId, 4, $userId),

                // Customers also viewed (co-view data)
                'customers_also_viewed' => $this->getAlsoViewed($productId, 6),

                // For cold-start users: randomized quality products from other stores
                'from_other_stores' => $hasUserBehavior
                    ? $this->getPersonalizedFromOtherStores($productId, $userId, 6)
                    : $this->getRandomFromOtherStores($productId, 6),
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get product page recommendations', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return [
                'similar_products' => collect(),
                'frequently_bought_together' => collect(),
                'customers_also_viewed' => collect(),
                'from_other_stores' => collect(),
            ];
        }
    }

    /**
     * Get random products from other stores (for cold-start/new users)
     * Products are randomized but still quality-filtered
     */
    public function getRandomFromOtherStores(int $productId, int $limit = 8): Collection
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                return collect();
            }

            // Get random quality products from different sellers in same category
            return Product::active()
                ->where('id', '!=', $productId)
                ->where('current_stock', '>', 0)
                ->where('category_id', $product->category_id)
                ->where('user_id', '!=', $product->user_id) // Different seller
                ->with(['brand', 'category', 'reviews', 'seller.shop'])
                ->withCount(['orderDetails', 'reviews'])
                // Quality filter: has some sales or reviews
                ->where(function ($q) {
                    $q->whereHas('orderDetails')
                        ->orWhereHas('reviews');
                })
                ->inRandomOrder() // RANDOMIZED for cold-start
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get personalized products from other stores (for users with behavior)
     */
    public function getPersonalizedFromOtherStores(int $productId, int $userId, int $limit = 8): Collection
    {
        try {
            $product = Product::find($productId);
            $preferences = UserPreference::find($userId);

            if (!$product || !$preferences) {
                return $this->getRandomFromOtherStores($productId, $limit);
            }

            // Price range based on user's behavior
            $avgPrice = $preferences->avg_viewed_price ?? $product->unit_price;
            $minPrice = $avgPrice * self::PRICE_TOLERANCE_LOWER;
            $maxPrice = $avgPrice * self::PRICE_TOLERANCE_UPPER;

            $products = Product::active()
                ->where('id', '!=', $productId)
                ->where('current_stock', '>', 0)
                ->where('category_id', $product->category_id)
                ->where('user_id', '!=', $product->user_id)
                ->whereBetween('unit_price', [$minPrice, $maxPrice])
                ->with(['brand', 'category', 'reviews', 'seller.shop'])
                ->limit($limit * 2)
                ->get();

            // Score by user preferences
            $scored = $products->map(function ($p) use ($preferences) {
                $p->personalization_score = $preferences->getProductScore($p);
                return $p;
            });

            return $scored->sortByDesc('personalization_score')->take($limit)->values();
        } catch (\Exception $e) {
            return $this->getRandomFromOtherStores($productId, $limit);
        }
    }

    /**
     * Get new arrival products
     */
    public function getNewArrivals(int $limit = 12, int $daysBack = 30): Collection
    {
        $cacheKey = self::CACHE_PREFIX . "new_arrivals:{$limit}:{$daysBack}";

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($limit, $daysBack) {
            try {
                return Product::active()
                    ->where('current_stock', '>', 0)
                    ->where('created_at', '>=', now()->subDays($daysBack))
                    ->with(['brand', 'category', 'reviews'])
                    ->orderByDesc('created_at')
                    ->limit($limit)
                    ->get();
            } catch (\Exception $e) {
                Log::warning('Failed to get new arrivals', ['error' => $e->getMessage()]);
                return collect();
            }
        });
    }

    /**
     * Get "just for you" products (combines personalization with trending)
     */
    public function getJustForYou(int $limit = 12): Collection
    {
        $userId = Auth::guard('customer')->id();

        if (!$userId) {
            // For guests, mix trending and new arrivals
            $trending = $this->getTrending((int) ceil($limit / 2));
            $newArrivals = $this->getNewArrivals((int) floor($limit / 2));

            return $trending->concat($newArrivals)
                ->unique('id')
                ->take($limit)
                ->shuffle();
        }

        // For logged-in users, prioritize personalized recommendations
        $personalized = $this->getPersonalized((int) ceil($limit * 0.6), $userId);
        $trending = $this->getTrending((int) ceil($limit * 0.4));

        $existingIds = $personalized->pluck('id')->toArray();
        $filteredTrending = $trending->filter(fn($p) => !in_array($p->id, $existingIds));

        return $personalized->concat($filteredTrending)
            ->take($limit)
            ->shuffle();
    }

    /**
     * Get "You May Like" products - personalized based on behavior or random for new users
     */
    public function getYouMayLike(int $limit = 12): Collection
    {
        $userId = Auth::guard('customer')->id();
        $cacheKey = self::CACHE_PREFIX . "you_may_like:" . ($userId ?? 'guest') . ":{$limit}";

        return Cache::remember($cacheKey, now()->addMinutes(20), function () use ($userId, $limit) {
            try {
                if ($userId) {
                    // For logged-in users: use their viewing/purchase history
                    $preferences = UserPreference::find($userId);

                    if ($preferences) {
                        $topCategories = array_keys($preferences->getTopCategories(5));
                        $topBrands = array_keys($preferences->getTopBrands(5));
                        $recentlyViewed = $preferences->getRecentlyViewedIds(20);

                        $products = Product::active()
                            ->where('current_stock', '>', 0)
                            ->whereNotIn('id', $recentlyViewed)
                            ->with(['brand', 'category', 'reviews'])
                            ->where(function ($q) use ($topCategories, $topBrands) {
                                if (!empty($topCategories)) {
                                    $q->whereIn('category_id', $topCategories);
                                    foreach ($topCategories as $categoryId) {
                                        $q->orWhere('category_ids', 'like', "%{$categoryId}%");
                                    }
                                }
                                if (!empty($topBrands)) {
                                    $q->orWhereIn('brand_id', $topBrands);
                                }
                            })
                            ->inRandomOrder()
                            ->limit($limit)
                            ->get();

                        if ($products->count() >= $limit / 2) {
                            return $products;
                        }
                    }
                }

                // For guests or users without enough data: random quality products
                return $this->getRandomQualityProducts($limit);
            } catch (\Exception $e) {
                Log::warning('Failed to get you may like products', ['error' => $e->getMessage()]);
                return $this->getRandomQualityProducts($limit);
            }
        });
    }

    /**
     * Get random quality products (with good ratings or sales)
     */
    protected function getRandomQualityProducts(int $limit): Collection
    {
        return Product::active()
            ->where('current_stock', '>', 0)
            ->with(['brand', 'category', 'reviews'])
            ->withCount(['orderDetails', 'reviews'])
            ->where(function ($query) {
                // Either has sales or reviews (indicates quality)
                $query->whereHas('orderDetails')
                    ->orWhereHas('reviews');
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Get all homepage recommendation sections
     */
    public function getHomepageRecommendations(): array
    {
        $userId = Auth::guard('customer')->id();
        $cacheKey = self::CACHE_PREFIX . "homepage:" . ($userId ?? 'guest');

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($userId) {
            return [
                'trending' => $this->getTrending(12),
                'hot_now' => $this->getHotNow(8),
                'just_for_you' => $this->getJustForYou(12),
                'new_arrivals' => $this->getNewArrivals(8),
                'popular' => $this->getPopular(12),
            ];
        });
    }

    /**
     * Get fallback products when recommendation data is unavailable
     */
    protected function getFallbackProducts(int $limit): Collection
    {
        return Product::active()
            ->where('current_stock', '>', 0)
            ->with(['brand', 'category', 'reviews'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get best selling products as fallback
     */
    protected function getBestSelling(int $limit): Collection
    {
        return Product::active()
            ->where('current_stock', '>', 0)
            ->with(['brand', 'category', 'reviews'])
            ->withCount('orderDetails')
            ->orderByDesc('order_details_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Clear recommendation caches for a user
     */
    public function clearUserCache(?int $userId = null): void
    {
        $userId = $userId ? Auth::guard('customer')->id() : null;

        if ($userId) {
            Cache::forget(self::CACHE_PREFIX . "personalized:{$userId}:12");
            Cache::forget(self::CACHE_PREFIX . "homepage:{$userId}");
        }
    }

    /**
     * Clear all recommendation caches
     */
    public function clearAllCaches(): void
    {
        // Clear common caches
        foreach ([8, 10, 12, 15, 20] as $limit) {
            Cache::forget(self::CACHE_PREFIX . "trending:{$limit}");
            Cache::forget(self::CACHE_PREFIX . "popular:{$limit}");
            Cache::forget(self::CACHE_PREFIX . "hot_now:{$limit}");
            Cache::forget(self::CACHE_PREFIX . "new_arrivals:{$limit}:30");
        }
        Cache::forget(self::CACHE_PREFIX . "homepage:guest");
    }
}
