<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PopularSearch extends Model
{
    protected $table = 'popular_searches';

    protected $fillable = [
        'query',
        'normalized_query',
        'search_count',
        'click_count',
        'avg_results',
        'conversion_rate',
        'last_searched',
    ];

    protected $casts = [
        'search_count' => 'integer',
        'click_count' => 'integer',
        'avg_results' => 'float',
        'conversion_rate' => 'float',
        'last_searched' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Normalize a search query for comparison
     */
    public static function normalizeQuery(string $query): string
    {
        $query = mb_strtolower(trim($query));
        $query = preg_replace('/\s+/', ' ', $query);
        $query = preg_replace('/[^\w\s]/', '', $query);
        return $query;
    }

    /**
     * Record or update a search
     */
    public static function recordSearch(string $query, int $resultsCount, bool $clicked = false, bool $converted = false): void
    {
        $normalized = self::normalizeQuery($query);

        $record = self::firstOrNew(['normalized_query' => $normalized]);

        if (!$record->exists) {
            $record->query = $query;
            $record->search_count = 0;
            $record->click_count = 0;
            $record->avg_results = 0;
            $record->conversion_rate = 0;
        }

        // Update counts
        $record->search_count++;
        $record->avg_results = (($record->avg_results * ($record->search_count - 1)) + $resultsCount) / $record->search_count;
        $record->last_searched = now();

        if ($clicked) {
            $record->click_count++;
        }

        if ($converted) {
            $totalConversions = ($record->conversion_rate * ($record->search_count - 1) / 100) + 1;
            $record->conversion_rate = ($totalConversions / $record->search_count) * 100;
        }

        $record->save();
    }

    /**
     * Get trending searches (most searched in last N days)
     */
    public static function getTrending(int $limit = 10, int $days = 7)
    {
        return self::where('last_searched', '>=', now()->subDays($days))
            ->orderByDesc('search_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get high-converting searches
     */
    public static function getHighConverting(int $limit = 10, int $minSearches = 10)
    {
        return self::where('search_count', '>=', $minSearches)
            ->orderByDesc('conversion_rate')
            ->limit($limit)
            ->get();
    }

    /**
     * Get underperforming searches (low results or clicks)
     */
    public static function getUnderperforming(int $limit = 20, int $minSearches = 5)
    {
        return self::where('search_count', '>=', $minSearches)
            ->where(function ($query) {
                $query->where('avg_results', '<', 5)
                    ->orWhere('click_count', '<', 2);
            })
            ->orderByDesc('search_count')
            ->limit($limit)
            ->get();
    }
}
