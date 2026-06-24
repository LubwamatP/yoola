<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActiveSession;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\ProductStat;
use App\Models\SearchAnalytics;
use App\Models\PopularSearch;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AnalyticsDashboardController extends Controller
{
    /**
     * Main analytics dashboard view
     */
    public function index(): View
    {
        // Get today's stats summary
        $today = Carbon::today();
        $todayViews = ProductView::whereDate('viewed_at', $today)->count();
        $todayUniqueVisitors = ProductView::whereDate('viewed_at', $today)
            ->distinct('session_id')
            ->count('session_id');
        $todaySearches = SearchAnalytics::whereDate('created_at', $today)->count();

        // Get top viewed products today
        $topViewedToday = ProductView::select('product_id', DB::raw('COUNT(*) as views'))
            ->whereDate('viewed_at', $today)
            ->groupBy('product_id')
            ->orderByDesc('views')
            ->limit(10)
            ->with(['product:id,name,slug,thumbnail,unit_price'])
            ->get();

        // Get trending searches
        $trendingSearches = SearchAnalytics::select('query', DB::raw('COUNT(*) as count'))
            ->whereDate('created_at', '>=', $today->copy()->subDays(7))
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(20)
            ->get();

        // Get view stats by period
        $viewsThisWeek = ProductView::whereBetween('viewed_at', [
            $today->copy()->startOfWeek(),
            $today->copy()->endOfWeek()
        ])->count();

        $viewsThisMonth = ProductView::whereBetween('viewed_at', [
            $today->copy()->startOfMonth(),
            $today->copy()->endOfMonth()
        ])->count();

        $viewsThisYear = ProductView::whereBetween('viewed_at', [
            $today->copy()->startOfYear(),
            $today->copy()->endOfYear()
        ])->count();

        return view('admin-views.analytics.dashboard', compact(
            'todayViews',
            'todayUniqueVisitors',
            'todaySearches',
            'topViewedToday',
            'trendingSearches',
            'viewsThisWeek',
            'viewsThisMonth',
            'viewsThisYear'
        ));
    }

    /**
     * Get real-time active viewers (products being viewed now)
     * Uses heartbeat-based presence tracking for accurate real-time data
     */
    public function getLiveViewers(): JsonResponse
    {
        // Sessions active within last 30 seconds are considered "live"
        $timeoutSeconds = 30;

        // Get products being viewed right now with viewer counts
        $liveViews = ActiveSession::active($timeoutSeconds)
            ->whereNotNull('current_product_id')
            ->selectRaw('current_product_id, COUNT(*) as active_viewers, MAX(last_activity) as last_view')
            ->groupBy('current_product_id')
            ->orderByDesc('active_viewers')
            ->limit(20)
            ->get();

        $products = [];
        foreach ($liveViews as $view) {
            $product = Product::select('id', 'name', 'slug', 'thumbnail', 'unit_price')
                ->find($view->current_product_id);

            if ($product) {
                $products[] = [
                    'product' => $product,
                    'active_viewers' => $view->active_viewers,
                    'last_view' => Carbon::parse($view->last_view)->diffForHumans(),
                ];
            }
        }

        // Total unique active users on the site right now
        $totalActiveUsers = ActiveSession::active($timeoutSeconds)->count();

        return response()->json([
            'products' => $products,
            'total_active_users' => $totalActiveUsers,
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Get most viewed products for a specific period
     */
    public function getMostViewed(Request $request): JsonResponse
    {
        $period = $request->get('period', 'today');
        $limit = $request->get('limit', 20);

        $query = ProductView::select('product_id', DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT session_id) as unique_visitors'));

        switch ($period) {
            case 'today':
                $query->whereDate('viewed_at', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('viewed_at', Carbon::yesterday());
                break;
            case 'week':
                $query->whereBetween('viewed_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereBetween('viewed_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
                break;
            case 'year':
                $query->whereBetween('viewed_at', [
                    Carbon::now()->startOfYear(),
                    Carbon::now()->endOfYear()
                ]);
                break;
        }

        $results = $query->groupBy('product_id')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();

        $products = [];
        foreach ($results as $result) {
            $product = Product::with(['category:id,name', 'brand:id,name'])
                ->select('id', 'name', 'slug', 'thumbnail', 'unit_price', 'category_id', 'brand_id')
                ->find($result->product_id);

            if ($product) {
                $products[] = [
                    'product' => $product,
                    'views' => $result->views,
                    'unique_visitors' => $result->unique_visitors,
                ];
            }
        }

        return response()->json([
            'period' => $period,
            'products' => $products,
        ]);
    }

    /**
     * Get view statistics chart data
     */
    public function getViewsChart(Request $request): JsonResponse
    {
        $period = $request->get('period', 'week');
        $labels = [];
        $views = [];
        $uniqueVisitors = [];

        switch ($period) {
            case 'today':
                // Hourly breakdown for today
                for ($i = 0; $i < 24; $i++) {
                    $hour = Carbon::today()->addHours($i);
                    $labels[] = $hour->format('H:00');

                    $hourViews = ProductView::whereBetween('viewed_at', [
                        $hour,
                        $hour->copy()->addHour()
                    ])->count();

                    $hourUnique = ProductView::whereBetween('viewed_at', [
                        $hour,
                        $hour->copy()->addHour()
                    ])->distinct('session_id')->count('session_id');

                    $views[] = $hourViews;
                    $uniqueVisitors[] = $hourUnique;
                }
                break;

            case 'week':
                // Daily breakdown for last 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $day = Carbon::today()->subDays($i);
                    $labels[] = $day->format('D, M j');

                    $dayViews = ProductView::whereDate('viewed_at', $day)->count();
                    $dayUnique = ProductView::whereDate('viewed_at', $day)
                        ->distinct('session_id')
                        ->count('session_id');

                    $views[] = $dayViews;
                    $uniqueVisitors[] = $dayUnique;
                }
                break;

            case 'month':
                // Daily breakdown for last 30 days
                for ($i = 29; $i >= 0; $i--) {
                    $day = Carbon::today()->subDays($i);
                    $labels[] = $day->format('M j');

                    $dayViews = ProductView::whereDate('viewed_at', $day)->count();
                    $dayUnique = ProductView::whereDate('viewed_at', $day)
                        ->distinct('session_id')
                        ->count('session_id');

                    $views[] = $dayViews;
                    $uniqueVisitors[] = $dayUnique;
                }
                break;

            case 'year':
                // Monthly breakdown for last 12 months
                for ($i = 11; $i >= 0; $i--) {
                    $month = Carbon::today()->subMonths($i);
                    $labels[] = $month->format('M Y');

                    $monthViews = ProductView::whereBetween('viewed_at', [
                        $month->copy()->startOfMonth(),
                        $month->copy()->endOfMonth()
                    ])->count();

                    $monthUnique = ProductView::whereBetween('viewed_at', [
                        $month->copy()->startOfMonth(),
                        $month->copy()->endOfMonth()
                    ])->distinct('session_id')->count('session_id');

                    $views[] = $monthViews;
                    $uniqueVisitors[] = $monthUnique;
                }
                break;
        }

        return response()->json([
            'labels' => $labels,
            'views' => $views,
            'unique_visitors' => $uniqueVisitors,
        ]);
    }

    /**
     * Get search analytics data
     */
    public function getSearchAnalytics(Request $request): JsonResponse
    {
        $period = $request->get('period', 'week');
        $limit = $request->get('limit', 50);

        $startDate = match ($period) {
            'today' => Carbon::today(),
            'yesterday' => Carbon::yesterday(),
            'week' => Carbon::now()->subDays(7),
            'month' => Carbon::now()->subDays(30),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subDays(7),
        };

        // Top searches
        $topSearches = SearchAnalytics::select(
                'query',
                DB::raw('COUNT(*) as search_count'),
                DB::raw('AVG(results_count) as avg_results'),
                DB::raw('SUM(clicks) as total_clicks')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('query')
            ->orderByDesc('search_count')
            ->limit($limit)
            ->get();

        // Zero result searches (what users want but can't find)
        $zeroResultSearches = SearchAnalytics::select('query', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->where('results_count', 0)
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(20)
            ->get();

        // Search volume over time
        $searchVolume = [];
        if ($period === 'today') {
            for ($i = 0; $i < 24; $i++) {
                $hour = Carbon::today()->addHours($i);
                $count = SearchAnalytics::whereBetween('created_at', [$hour, $hour->copy()->addHour()])->count();
                $searchVolume[] = ['label' => $hour->format('H:00'), 'count' => $count];
            }
        } else {
            $days = match ($period) {
                'week' => 7,
                'month' => 30,
                'year' => 12,
                default => 7,
            };

            for ($i = $days - 1; $i >= 0; $i--) {
                if ($period === 'year') {
                    $date = Carbon::today()->subMonths($i);
                    $count = SearchAnalytics::whereBetween('created_at', [
                        $date->copy()->startOfMonth(),
                        $date->copy()->endOfMonth()
                    ])->count();
                    $searchVolume[] = ['label' => $date->format('M Y'), 'count' => $count];
                } else {
                    $date = Carbon::today()->subDays($i);
                    $count = SearchAnalytics::whereDate('created_at', $date)->count();
                    $searchVolume[] = ['label' => $date->format('M j'), 'count' => $count];
                }
            }
        }

        return response()->json([
            'top_searches' => $topSearches,
            'zero_result_searches' => $zeroResultSearches,
            'search_volume' => $searchVolume,
            'total_searches' => SearchAnalytics::where('created_at', '>=', $startDate)->count(),
        ]);
    }

    /**
     * Get product detail analytics
     */
    public function getProductAnalytics(Request $request, int $productId): JsonResponse
    {
        $period = $request->get('period', 'month');

        $startDate = match ($period) {
            'week' => Carbon::now()->subDays(7),
            'month' => Carbon::now()->subDays(30),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subDays(30),
        };

        $product = Product::with(['category:id,name', 'brand:id,name'])
            ->select('id', 'name', 'slug', 'thumbnail', 'unit_price', 'category_id', 'brand_id')
            ->findOrFail($productId);

        // Total views
        $totalViews = ProductView::where('product_id', $productId)
            ->where('viewed_at', '>=', $startDate)
            ->count();

        // Unique visitors
        $uniqueVisitors = ProductView::where('product_id', $productId)
            ->where('viewed_at', '>=', $startDate)
            ->distinct('session_id')
            ->count('session_id');

        // Views over time
        $viewsOverTime = [];
        $days = $period === 'year' ? 12 : ($period === 'month' ? 30 : 7);

        for ($i = $days - 1; $i >= 0; $i--) {
            if ($period === 'year') {
                $date = Carbon::today()->subMonths($i);
                $count = ProductView::where('product_id', $productId)
                    ->whereBetween('viewed_at', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                    ->count();
                $viewsOverTime[] = ['label' => $date->format('M Y'), 'count' => $count];
            } else {
                $date = Carbon::today()->subDays($i);
                $count = ProductView::where('product_id', $productId)
                    ->whereDate('viewed_at', $date)
                    ->count();
                $viewsOverTime[] = ['label' => $date->format('M j'), 'count' => $count];
            }
        }

        // Device breakdown
        $deviceBreakdown = ProductView::select('device_type', DB::raw('COUNT(*) as count'))
            ->where('product_id', $productId)
            ->where('viewed_at', '>=', $startDate)
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->get();

        // Traffic sources
        $trafficSources = ProductView::select('source', DB::raw('COUNT(*) as count'))
            ->where('product_id', $productId)
            ->where('viewed_at', '>=', $startDate)
            ->whereNotNull('source')
            ->groupBy('source')
            ->get();

        // Conversion stats
        $addedToCart = ProductView::where('product_id', $productId)
            ->where('viewed_at', '>=', $startDate)
            ->where('added_to_cart', true)
            ->count();

        $purchased = ProductView::where('product_id', $productId)
            ->where('viewed_at', '>=', $startDate)
            ->where('purchased', true)
            ->count();

        return response()->json([
            'product' => $product,
            'total_views' => $totalViews,
            'unique_visitors' => $uniqueVisitors,
            'views_over_time' => $viewsOverTime,
            'device_breakdown' => $deviceBreakdown,
            'traffic_sources' => $trafficSources,
            'added_to_cart' => $addedToCart,
            'purchased' => $purchased,
            'cart_rate' => $totalViews > 0 ? round(($addedToCart / $totalViews) * 100, 2) : 0,
            'conversion_rate' => $totalViews > 0 ? round(($purchased / $totalViews) * 100, 2) : 0,
        ]);
    }

    /**
     * Export analytics data
     */
    public function exportData(Request $request): JsonResponse
    {
        $type = $request->get('type', 'views');
        $period = $request->get('period', 'month');

        $startDate = match ($period) {
            'week' => Carbon::now()->subDays(7),
            'month' => Carbon::now()->subDays(30),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subDays(30),
        };

        if ($type === 'views') {
            $data = ProductView::select(
                    'product_id',
                    DB::raw('COUNT(*) as views'),
                    DB::raw('COUNT(DISTINCT session_id) as unique_visitors')
                )
                ->where('viewed_at', '>=', $startDate)
                ->groupBy('product_id')
                ->orderByDesc('views')
                ->get()
                ->map(function ($item) {
                    $product = Product::find($item->product_id);
                    return [
                        'product_name' => $product?->name ?? 'Unknown',
                        'product_id' => $item->product_id,
                        'views' => $item->views,
                        'unique_visitors' => $item->unique_visitors,
                    ];
                });
        } else {
            $data = SearchAnalytics::select(
                    'query',
                    DB::raw('COUNT(*) as search_count'),
                    DB::raw('AVG(results_count) as avg_results')
                )
                ->where('created_at', '>=', $startDate)
                ->groupBy('query')
                ->orderByDesc('search_count')
                ->get();
        }

        return response()->json(['data' => $data]);
    }
}
