<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductView;
use App\Models\ProductCoView;
use App\Models\UserPreference;
use App\Events\ProductViewedEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ViewTrackingService
{
    protected const RECENT_VIEWS_KEY = 'recent_product_views';
    protected const MAX_RECENT_VIEWS = 10;
    protected const DUPLICATE_WINDOW_MINUTES = 5;

    /**
     * Record a product view
     */
    public function recordView(int $productId, array $options = []): ?ProductView
    {
        try {
            // Check for duplicate view within window
            if ($this->isDuplicateView($productId)) {
                return null;
            }

            $userId = Auth::guard('customer')->id();
            $sessionId = Session::getId();

            // Create view record
            $view = ProductView::create([
                'product_id' => $productId,
                'user_id' => $userId,
                'session_id' => $sessionId,
                'viewed_at' => now(),
                'duration_seconds' => $options['duration'] ?? null,
                'device_type' => $options['device_type'] ? ProductView::detectDeviceType() : null,
                'referrer' => $options['referrer'] ?? request()->header('referer'),
                'source' => $options['source'] ? ProductView::detectSource() : null,
                'added_to_cart' => false,
                'purchased' => false,
            ]);

            // Track for co-view calculation
            $this->recordRecentView($productId);

            // Update user preferences if logged in
            if ($userId) {
                $this->updateUserPreferences($userId, $productId);
            }

            // Dispatch event for further processing
            event(new ProductViewedEvent($view));

            return $view;

        } catch (\Exception $e) {
            Log::error('Failed to record product view', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Check if this is a duplicate view within the time window
     */
    protected function isDuplicateView(int $productId): bool
    {
        $sessionId = Session::getId();
        $cacheKey = "view_dedupe:{$sessionId}:{$productId}";

        if (Cache::has($cacheKey)) {
            return true;
        }

        Cache::put($cacheKey, true, now()->addMinutes(self::DUPLICATE_WINDOW_MINUTES));
        return false;
    }

    /**
     * Track recent views for co-view calculation
     */
    protected function recordRecentView(int $productId): void
    {
        $sessionId = Session::getId();
        $cacheKey = self::RECENT_VIEWS_KEY . ':' . $sessionId;

        $recentViews = Cache::get($cacheKey, []);

        // Record co-views with previous products in session
        foreach ($recentViews as $previousProductId) {
            if ($previousProductId !== $productId) {
                ProductCoView::recordCoView($productId, $previousProductId);
            }
        }

        // Add current product to recent views
        $recentViews = array_filter($recentViews, fn($id) => $id !== $productId);
        array_unshift($recentViews, $productId);
        $recentViews = array_slice($recentViews, 0, self::MAX_RECENT_VIEWS);

        Cache::put($cacheKey, $recentViews, now()->addHours(2));
    }

    /**
     * Update user preferences based on view
     */
    protected function updateUserPreferences(int $userId, int $productId): void
    {
        try {
            $product = Product::with(['category', 'brand'])->find($productId);

            if ($product) {
                $preferences = UserPreference::getOrCreate($userId);
                $preferences->recordProductView($product);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update user preferences', [
                'user_id' => $userId,
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update view duration (called via AJAX when user leaves page)
     */
    public function updateDuration(int $viewId, int $durationSeconds): bool
    {
        try {
            $view = ProductView::find($viewId);

            if ($view) {
                $view->duration_seconds = $durationSeconds;
                $view->save();
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to update view duration', [
                'view_id' => $viewId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Mark a view as having added to cart
     */
    public function markAddedToCart(int $productId, ?int $userId = null): void
    {
        $sessionId = Session::getId();
        $userId = $userId ? Auth::guard('customer')->id() : null;

        $query = ProductView::forProduct($productId)
            ->forSession($sessionId)
            ->where('added_to_cart', false)
            ->orderByDesc('viewed_at');

        if ($userId) {
            $query->orWhere(function ($q) use ($productId, $userId) {
                $q->forProduct($productId)->forUser($userId)->where('added_to_cart', false);
            });
        }

        $view = $query->first();

        if ($view) {
            $view->added_to_cart = true;
            $view->save();
        }
    }

    /**
     * Mark views as purchased
     */
    public function markPurchased(array $productIds, ?int $userId = null): void
    {
        $sessionId = Session::getId();
        $userId = $userId ? Auth::guard('customer')->id() : null;

        foreach ($productIds as $productId) {
            $query = ProductView::forProduct($productId)
                ->where('purchased', false)
                ->orderByDesc('viewed_at');

            if ($userId) {
                $query->where(function ($q) use ($sessionId, $userId) {
                    $q->forSession($sessionId)->orWhere('user_id', $userId);
                });
            } else {
                $query->forSession($sessionId);
            }

            $view = $query->first();

            if ($view) {
                $view->purchased = true;
                $view->save();
            }
        }
    }

    /**
     * Get recently viewed product IDs for current session/user
     */
    public function getRecentlyViewedIds(int $limit = 10): array
    {
        $userId = Auth::guard('customer')->id();
        $sessionId = Session::getId();

        $query = ProductView::select('product_id')
            ->distinct();

        if ($userId) {
            $query->where(function ($q) use ($sessionId, $userId) {
                $q->forSession($sessionId)->orWhere('user_id', $userId);
            });
        } else {
            $query->forSession($sessionId);
        }

        return $query->orderByDesc('viewed_at')
            ->limit($limit)
            ->pluck('product_id')
            ->toArray();
    }

    /**
     * Get recently viewed products for current session/user
     */
    public function getRecentlyViewed(int $limit = 10): \Illuminate\Support\Collection
    {
        $productIds = $this->getRecentlyViewedIds($limit);

        // Get products in order
        if (empty($productIds)) {
            return collect();
        }

        return Product::active()
            ->whereIn('id', $productIds)
            ->with(['brand', 'category', 'reviews'])
            ->get()
            ->sortBy(function ($product) use ($productIds) {
                return array_search($product->id, $productIds);
            })
            ->values();
    }

    /**
     * Get view statistics for a product
     */
    public function getProductStats(int $productId): array
    {
        return [
            'views_1h' => ProductView::forProduct($productId)->lastHours(1)->count(),
            'views_24h' => ProductView::forProduct($productId)->lastHours(24)->count(),
            'views_7d' => ProductView::forProduct($productId)->lastDays(7)->count(),
            'unique_24h' => ProductView::forProduct($productId)
                ->lastHours(24)
                ->distinct('session_id')
                ->count('session_id'),
            'cart_rate' => $this->calculateCartRate($productId),
            'conversion_rate' => $this->calculateConversionRate($productId),
        ];
    }

    /**
     * Calculate cart addition rate
     */
    protected function calculateCartRate(int $productId): float
    {
        $views = ProductView::forProduct($productId)->lastDays(7)->count();
        if ($views === 0) return 0;

        $cartAdds = ProductView::forProduct($productId)->lastDays(7)->addedToCart()->count();
        return round($cartAdds / $views, 4);
    }

    /**
     * Calculate purchase conversion rate
     */
    protected function calculateConversionRate(int $productId): float
    {
        $views = ProductView::forProduct($productId)->lastDays(7)->count();
        if ($views === 0) return 0;

        $purchases = ProductView::forProduct($productId)->lastDays(7)->purchased()->count();
        return round($purchases / $views, 4);
    }
}
