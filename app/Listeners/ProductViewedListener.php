<?php

namespace App\Listeners;

use App\Events\ProductViewedEvent;
use App\Models\ProductStat;
use App\Services\RecommendationEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ProductViewedListener implements ShouldQueue
{
    /**
     * The queue connection that should be used
     */
    public string $connection = 'sync';

    /**
     * The name of the queue the job should be sent to
     */
    public string $queue = 'default';

    /**
     * Create the event listener.
     */
    public function __construct(
        protected RecommendationEngine $recommendationEngine
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(ProductViewedEvent $event): void
    {
        try {
            $productId = $event->getProductId();

            // Update product stats incrementally
            $this->updateProductStats($productId);

            // Clear user-specific recommendation cache if logged in
            if ($event->getUserId()) {
                $this->recommendationEngine->clearUserCache($event->getUserId());
            }

        } catch (\Exception $e) {
            Log::error('Failed to process product viewed event', [
                'product_id' => $event->getProductId(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update product statistics incrementally
     */
    protected function updateProductStats(int $productId): void
    {
        $stat = ProductStat::firstOrNew(['product_id' => $productId]);

        // Increment view counts
        $stat->views_1h = ($stat->views_1h ?? 0) + 1;
        $stat->views_24h = ($stat->views_24h ?? 0) + 1;
        $stat->views_7d = ($stat->views_7d ?? 0) + 1;
        $stat->views_30d = ($stat->views_30d ?? 0) + 1;

        $stat->last_viewed_at = now();

        // Recalculate scores periodically (not on every view for performance)
        $shouldRecalculate = !$stat->last_calculated_at
            || $stat->last_calculated_at->diffInMinutes(now()) >= 5;

        if ($shouldRecalculate) {
            $stat->trending_score = $stat->calculateTrendingScore();
            $stat->popularity_score = $stat->calculatePopularityScore();
            $stat->last_calculated_at = now();
        }

        $stat->save();
    }

    /**
     * Determine whether the listener should be queued.
     */
    public function shouldQueue(ProductViewedEvent $event): bool
    {
        // Always queue to avoid blocking the request
        return true;
    }
}
