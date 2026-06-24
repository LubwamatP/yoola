<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SearchAnalytics extends Model
{
    protected $table = 'search_analytics';

    protected $fillable = [
        'query',
        'results_count',
        'ai_powered',
        'intent',
        'confidence',
        'response_time_ms',
        'user_id',
        'session_id',
        'filters',
        'clicks',
        'converted',
        'clicked_product_id',
        'user_rating',
        'feedback',
    ];

    protected $casts = [
        'ai_powered' => 'boolean',
        'converted' => 'boolean',
        'intent' => 'array',
        'filters' => 'array',
        'confidence' => 'float',
        'results_count' => 'integer',
        'response_time_ms' => 'integer',
        'clicks' => 'integer',
        'user_rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * User relationship (if logged in)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Clicked product relationship
     */
    public function clickedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'clicked_product_id');
    }

    /**
     * Conversions from this search
     */
    public function conversions(): HasMany
    {
        return $this->hasMany(SearchConversion::class, 'search_analytics_id');
    }

    /**
     * Scope for AI-powered searches
     */
    public function scopeAiPowered($query)
    {
        return $query->where('ai_powered', true);
    }

    /**
     * Scope for searches with results
     */
    public function scopeWithResults($query)
    {
        return $query->where('results_count', '>', 0);
    }

    /**
     * Scope for high confidence searches
     */
    public function scopeHighConfidence($query, float $threshold = 0.7)
    {
        return $query->where('confidence', '>=', $threshold);
    }

    /**
     * Scope for converted searches
     */
    public function scopeConverted($query)
    {
        return $query->where('converted', true);
    }

    /**
     * Scope for searches from today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for searches from last N days
     */
    public function scopeLastDays($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get top searched queries
     */
    public static function getTopQueries(int $limit = 20, int $days = 30): array
    {
        return static::select('query')
            ->selectRaw('COUNT(*) as search_count')
            ->selectRaw('AVG(results_count) as avg_results')
            ->selectRaw('SUM(clicks) as total_clicks')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('query')
            ->orderByDesc('search_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get searches with zero results
     */
    public static function getZeroResultQueries(int $limit = 50, int $days = 7): array
    {
        return static::select('query')
            ->selectRaw('COUNT(*) as search_count')
            ->where('results_count', 0)
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('query')
            ->orderByDesc('search_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get AI search performance metrics
     */
    public static function getAiSearchMetrics(int $days = 30): array
    {
        $baseQuery = static::where('created_at', '>=', now()->subDays($days));

        $totalSearches = (clone $baseQuery)->count();
        $aiSearches = (clone $baseQuery)->where('ai_powered', true)->count();
        $avgResponseTime = (clone $baseQuery)->avg('response_time_ms');
        $avgConfidence = (clone $baseQuery)->where('ai_powered', true)->avg('confidence');
        $conversionRate = $totalSearches > 0
            ?? ((clone $baseQuery)->where('converted', true)->count() / $totalSearches) * 100
            : 0;
        $clickThroughRate = $totalSearches > 0
            ?? ((clone $baseQuery)->where('clicks', '>', 0)->count() / $totalSearches) * 100
            : 0;

        return [
            'total_searches' => $totalSearches,
            'ai_searches' => $aiSearches,
            'ai_search_percentage' => $totalSearches > 0 ? round(($aiSearches / $totalSearches) * 100, 2) : 0,
            'avg_response_time_ms' => round($avgResponseTime ?? 0, 2),
            'avg_confidence' => round($avgConfidence ?? 0, 3),
            'conversion_rate' => round($conversionRate, 2),
            'click_through_rate' => round($clickThroughRate, 2),
        ];
    }

    /**
     * Record a click on search result
     */
    public function recordClick(?int $productId = null): void
    {
        $this->increment('clicks');

        if ($productId) {
            $this->update(['clicked_product_id' => $productId]);
        }
    }

    /**
     * Record a conversion from this search
     */
    public function recordConversion(int $orderId, int $productId, float $amount): void
    {
        $this->update(['converted' => true]);

        SearchConversion::create([
            'search_analytics_id' => $this->id,
            'order_id' => $orderId,
            'product_id' => $productId,
            'order_amount' => $amount,
        ]);
    }
}
