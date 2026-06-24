<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ProductCoView extends Model
{
    protected $table = 'product_co_views';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'product_id',
        'co_viewed_product_id',
        'co_view_count',
        'affinity_score',
        'last_updated_at',
    ];

    protected $casts = [
        'co_view_count' => 'integer',
        'affinity_score' => 'float',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Main product relationship
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Co-viewed product relationship
     */
    public function coViewedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'co_viewed_product_id');
    }

    /**
     * Get products commonly viewed with a given product
     */
    public static function getRelatedProducts(int $productId, int $limit = 8): Collection
    {
        return self::with(['coViewedProduct' => function ($query) {
                $query->active()->with(['brand', 'category', 'reviews']);
            }])
            ->where('product_id', $productId)
            ->whereHas('coViewedProduct', function ($query) {
                $query->active()->where('current_stock', '>', 0);
            })
            ->orderByDesc('affinity_score')
            ->limit($limit)
            ->get()
            ->pluck('coViewedProduct')
            ->filter()
            ->values();
    }

    /**
     * Record a co-view between two products
     */
    public static function recordCoView(int $productId, int $coViewedProductId): void
    {
        if ($productId === $coViewedProductId) {
            return;
        }

        // Record both directions
        self::incrementCoView($productId, $coViewedProductId);
        self::incrementCoView($coViewedProductId, $productId);
    }

    /**
     * Increment co-view count and recalculate affinity
     */
    protected static function incrementCoView(int $productId, int $coViewedProductId): void
    {
        // Use DB::table for composite primary key tables
        $existing = \DB::table('product_co_views')
            ->where('product_id', $productId)
            ->where('co_viewed_product_id', $coViewedProductId)
            ->first();
        
        if ($existing) {
            $newCount = $existing->co_view_count + 1;
            $affinityScore = log10($newCount + 1) * 10;
            
            \DB::table('product_co_views')
                ->where('product_id', $productId)
                ->where('co_viewed_product_id', $coViewedProductId)
                ->update([
                    'co_view_count' => $newCount,
                    'affinity_score' => $affinityScore,
                    'last_updated_at' => now(),
                ]);
        } else {
            \DB::table('product_co_views')->insert([
                'product_id' => $productId,
                'co_viewed_product_id' => $coViewedProductId,
                'co_view_count' => 1,
                'affinity_score' => log10(2) * 10, // log10(1+1) * 10
                'last_updated_at' => now(),
            ]);
        }
    }

    /**
     * Batch update affinity scores for a product
     */
    public static function recalculateAffinityScores(int $productId): void
    {
        $coViews = self::where('product_id', $productId)->get();
        $maxCount = $coViews->max('co_view_count') ?: 1;

        foreach ($coViews as $coView) {
            // Normalize to 0-100 scale based on max
            $coView->affinity_score = ($coView->co_view_count / $maxCount) * 100;
            $coView->save();
        }
    }
}
