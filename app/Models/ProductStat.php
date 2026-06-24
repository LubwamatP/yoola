<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductStat extends Model
{
    protected $table = 'product_stats';
    protected $primaryKey = 'product_id';
    public $incrementing = false;

    protected $fillable = [
        'product_id',
        'views_1h',
        'views_24h',
        'views_7d',
        'views_30d',
        'unique_visitors_24h',
        'unique_visitors_7d',
        'cart_adds_7d',
        'purchases_7d',
        'purchases_30d',
        'conversion_rate',
        'cart_rate',
        'trending_score',
        'popularity_score',
        'last_viewed_at',
        'last_calculated_at',
    ];

    protected $casts = [
        'views_1h' => 'integer',
        'views_24h' => 'integer',
        'views_7d' => 'integer',
        'views_30d' => 'integer',
        'unique_visitors_24h' => 'integer',
        'unique_visitors_7d' => 'integer',
        'cart_adds_7d' => 'integer',
        'purchases_7d' => 'integer',
        'purchases_30d' => 'integer',
        'conversion_rate' => 'float',
        'cart_rate' => 'float',
        'trending_score' => 'float',
        'popularity_score' => 'float',
        'last_viewed_at' => 'datetime',
        'last_calculated_at' => 'datetime',
    ];

    /**
     * Product relationship
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate trending score
     * Formula: (recent_views * conversion_rate * recency_multiplier) / time_window
     */
    public function calculateTrendingScore(): float
    {
        // View velocity (views per hour in last 24 hours)
        $viewVelocity = $this->views_24h / 24;

        // Conversion rate boost (higher conversion = more trending)
        $conversionBoost = 1 + ($this->conversion_rate * 10);

        // Recency multiplier (1h views weighted 4x, 24h views weighted 2x)
        $recencyMultiplier = ($this->views_1h * 4) + ($this->views_24h * 2) + $this->views_7d;
        $recencyMultiplier = $recencyMultiplier > 0 ? $recencyMultiplier / max($this->views_7d, 1) : 1;

        // Cart engagement boost
        $cartBoost = 1 + ($this->cart_rate * 5);

        $score = $viewVelocity * $conversionBoost * $recencyMultiplier * $cartBoost;

        return round($score, 4);
    }

    /**
     * Calculate popularity score (longer-term metric)
     */
    public function calculatePopularityScore(): float
    {
        // Base on 30-day views
        $viewScore = log10(max($this->views_30d, 1) + 1) * 10;

        // Conversion quality
        $qualityScore = $this->conversion_rate * 100;

        // Unique visitor ratio (higher = more organic interest)
        $uniqueRatio = $this->views_7d > 0
            ?? $this->unique_visitors_7d / $this->views_7d
            : 0;
        $uniqueBoost = 1 + ($uniqueRatio * 0.5);

        // Purchase momentum
        $purchaseScore = log10(max($this->purchases_30d, 1) + 1) * 20;

        $score = ($viewScore + $qualityScore + $purchaseScore) * $uniqueBoost;

        return round($score, 4);
    }

    /**
     * Get top trending products
     */
    public static function getTrending(int $limit = 12): Collection
    {
        return self::with(['product' => function ($query) {
                $query->active()->with(['brand', 'category', 'reviews']);
            }])
            ->whereHas('product', function ($query) {
                $query->active()->where('current_stock', '>', 0);
            })
            ->where('trending_score', '>', 0)
            ->orderByDesc('trending_score')
            ->limit($limit)
            ->get()
            ->pluck('product')
            ->filter()
            ->values();
    }

    /**
     * Get most popular products
     */
    public static function getPopular(int $limit = 12): Collection
    {
        return self::with(['product' => function ($query) {
                $query->active()->with(['brand', 'category', 'reviews']);
            }])
            ->whereHas('product', function ($query) {
                $query->active()->where('current_stock', '>', 0);
            })
            ->where('popularity_score', '>', 0)
            ->orderByDesc('popularity_score')
            ->limit($limit)
            ->get()
            ->pluck('product')
            ->filter()
            ->values();
    }

    /**
     * Get hot products (high velocity in last hour)
     */
    public static function getHotNow(int $limit = 8): Collection
    {
        return self::with(['product' => function ($query) {
                $query->active()->with(['brand', 'category']);
            }])
            ->whereHas('product', function ($query) {
                $query->active()->where('current_stock', '>', 0);
            })
            ->where('views_1h', '>', 0)
            ->orderByDesc('views_1h')
            ->limit($limit)
            ->get()
            ->pluck('product')
            ->filter()
            ->values();
    }

    /**
     * Update or create stats for a product
     */
    public static function updateForProduct(int $productId): self
    {
        $stats = self::firstOrNew(['product_id' => $productId]);

        // Calculate view counts
        $stats->views_1h = ProductView::forProduct($productId)->lastHours(1)->count();
        $stats->views_24h = ProductView::forProduct($productId)->lastHours(24)->count();
        $stats->views_7d = ProductView::forProduct($productId)->lastDays(7)->count();
        $stats->views_30d = ProductView::forProduct($productId)->lastDays(30)->count();

        // Calculate unique visitors
        $stats->unique_visitors_24h = ProductView::forProduct($productId)
            ->lastHours(24)
            ->distinct('session_id')
            ->count('session_id');

        $stats->unique_visitors_7d = ProductView::forProduct($productId)
            ->lastDays(7)
            ->distinct('session_id')
            ->count('session_id');

        // Calculate cart and purchase metrics
        $stats->cart_adds_7d = ProductView::forProduct($productId)
            ->lastDays(7)
            ->addedToCart()
            ->count();

        $stats->purchases_7d = ProductView::forProduct($productId)
            ->lastDays(7)
            ->purchased()
            ->count();

        $stats->purchases_30d = ProductView::forProduct($productId)
            ->lastDays(30)
            ->purchased()
            ->count();

        // Calculate rates
        $stats->conversion_rate = $stats->views_7d > 0
            ?? $stats->purchases_7d / $stats->views_7d
            : 0;

        $stats->cart_rate = $stats->views_7d > 0
            ?? $stats->cart_adds_7d / $stats->views_7d
            : 0;

        // Get last viewed timestamp
        $lastView = ProductView::forProduct($productId)
            ->orderByDesc('viewed_at')
            ->first();

        $stats->last_viewed_at = $lastView?->viewed_at;

        // Calculate scores
        $stats->trending_score = $stats->calculateTrendingScore();
        $stats->popularity_score = $stats->calculatePopularityScore();

        $stats->last_calculated_at = now();
        $stats->save();

        return $stats;
    }
}
