<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ViewTrackingService;
use App\Services\RecommendationEngine;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductTrackingController extends Controller
{
    public function __construct(
        private readonly ViewTrackingService   $viewTrackingService,
        private readonly RecommendationEngine  $recommendationEngine,
    ) {
    }

    /**
     * Record a product view via AJAX
     */
    public function recordView(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'referrer' => 'nullable|string|max:500',
        ]);

        $view = $this->viewTrackingService->recordView(
            $validated['product_id'],
            [
                'referrer' => $validated['referrer'] ?? null,
                'user_agent' => $request->userAgent(),
            ]
        );

        return response()->json([
            'success' => true,
            'recorded' => $view !== null,
        ]);
    }

    /**
     * Mark product as added to cart
     */
    public function markAddedToCart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $this->viewTrackingService->markAddedToCart($validated['product_id']);

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Get recently viewed products
     */
    public function getRecentlyViewed(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 8), 20);

        $productIds = $this->viewTrackingService->getRecentlyViewed($limit);

        $products = Product::active()
            ->whereIn('id', $productIds)
            ->with(['brand', 'category', 'reviews'])
            ->get()
            ->sortBy(function ($product) use ($productIds) {
                return array_search($product->id, $productIds);
            })
            ->values();

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Get "customers also viewed" recommendations
     */
    public function getAlsoViewed(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        $limit = $validated['limit'] ?? 8;

        $products = $this->recommendationEngine->getAlsoViewed(
            $validated['product_id'],
            $limit
        );

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Get trending products
     */
    public function getTrending(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 12), 24);

        $products = $this->recommendationEngine->getTrending($limit);

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Get personalized recommendations for current user
     */
    public function getPersonalized(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 12), 24);

        $products = $this->recommendationEngine->getPersonalized($limit);

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }
}
