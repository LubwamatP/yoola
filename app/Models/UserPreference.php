<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $table = 'user_preferences';
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'category_affinity',
        'brand_affinity',
        'price_range_min',
        'price_range_max',
        'avg_viewed_price',
        'total_views',
        'total_purchases',
        'recently_viewed',
        'last_activity_at',
    ];

    protected $casts = [
        'category_affinity' => 'array',
        'brand_affinity' => 'array',
        'price_range_min' => 'float',
        'price_range_max' => 'float',
        'avg_viewed_price' => 'float',
        'total_views' => 'integer',
        'total_purchases' => 'integer',
        'recently_viewed' => 'array',
        'last_activity_at' => 'datetime',
    ];

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create preferences for a user
     */
    public static function getOrCreate(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'category_affinity' => [],
                'brand_affinity' => [],
                'recently_viewed' => [],
                'total_views' => 0,
                'total_purchases' => 0,
            ]
        );
    }

    /**
     * Add a product view to preferences
     */
    public function recordProductView(Product $product): void
    {
        // Update recently viewed (keep last 50)
        $recentlyViewed = $this->recently_viewed ?? [];
        $recentlyViewed = array_filter($recentlyViewed, fn($id) => $id !== $product->id);
        array_unshift($recentlyViewed, $product->id);
        $this->recently_viewed = array_slice($recentlyViewed, 0, 50);

        // Update category affinity
        $categoryAffinity = $this->category_affinity ?? [];
        $categoryId = (string) $product->category_id;
        $categoryAffinity[$categoryId] = ($categoryAffinity[$categoryId] ?? 0) + 1;
        $this->category_affinity = $categoryAffinity;

        // Update brand affinity
        if ($product->brand_id) {
            $brandAffinity = $this->brand_affinity ?? [];
            $brandId = (string) $product->brand_id;
            $brandAffinity[$brandId] = ($brandAffinity[$brandId] ?? 0) + 1;
            $this->brand_affinity = $brandAffinity;
        }

        // Update price tracking
        $this->total_views = ($this->total_views ?? 0) + 1;
        $this->updatePriceRange($product->unit_price);

        $this->last_activity_at = now();
        $this->save();
    }

    /**
     * Update price range preferences
     */
    protected function updatePriceRange(float $price): void
    {
        // Update min/max
        if ($this->price_range_min === null || $price < $this->price_range_min) {
            $this->price_range_min = $price;
        }
        if ($this->price_range_max === null || $price > $this->price_range_max) {
            $this->price_range_max = $price;
        }

        // Calculate running average
        $totalViews = $this->total_views ?? 1;
        $currentAvg = $this->avg_viewed_price ?? $price;
        $this->avg_viewed_price = (($currentAvg * ($totalViews - 1)) + $price) / $totalViews;
    }

    /**
     * Get top categories by affinity
     */
    public function getTopCategories(int $limit = 5): array
    {
        $affinity = $this->category_affinity ?? [];
        arsort($affinity);
        return array_slice($affinity, 0, $limit, true);
    }

    /**
     * Get top brands by affinity
     */
    public function getTopBrands(int $limit = 5): array
    {
        $affinity = $this->brand_affinity ?? [];
        arsort($affinity);
        return array_slice($affinity, 0, $limit, true);
    }

    /**
     * Get recently viewed product IDs
     */
    public function getRecentlyViewedIds(int $limit = 10): array
    {
        return array_slice($this->recently_viewed ?? [], 0, $limit);
    }

    /**
     * Check if user has viewed a product recently
     */
    public function hasViewedRecently(int $productId): bool
    {
        return in_array($productId, $this->recently_viewed ?? []);
    }

    /**
     * Get price range preference as array
     */
    public function getPriceRange(): array
    {
        return [
            'min' => $this->price_range_min,
            'max' => $this->price_range_max,
            'avg' => $this->avg_viewed_price,
        ];
    }

    /**
     * Calculate preference score for a product
     */
    public function getProductScore(Product $product): float
    {
        $score = 0;

        // Category match
        $categoryAffinity = $this->category_affinity ?? [];
        $categoryId = (string) $product->category_id;
        if (isset($categoryAffinity[$categoryId])) {
            $score += min($categoryAffinity[$categoryId] * 10, 50);
        }

        // Brand match
        if ($product->brand_id) {
            $brandAffinity = $this->brand_affinity ?? [];
            $brandId = (string) $product->brand_id;
            if (isset($brandAffinity[$brandId])) {
                $score += min($brandAffinity[$brandId] * 8, 40);
            }
        }

        // Price range match
        if ($this->avg_viewed_price) {
            $priceDiff = abs($product->unit_price - $this->avg_viewed_price);
            $priceRatio = $priceDiff / max($this->avg_viewed_price, 1);
            // Lower difference = higher score
            $score += max(0, 30 - ($priceRatio * 30));
        }

        return $score;
    }
}
