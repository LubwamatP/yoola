<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductStat;
use App\Models\ProductView;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AggregateProductStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:aggregate
                            {--product= : Specific product ID to update}
                            {--all : Update all products with views}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate product view statistics and calculate trending/popularity scores';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting product stats aggregation...');

        try {
            if ($productId = $this->option('product')) {
                $this->updateSingleProduct((int) $productId);
            } elseif ($this->option('all')) {
                $this->updateAllProducts();
            } else {
                // Default: update products with recent views
                $this->updateRecentlyViewedProducts();
            }

            $this->info('Product stats aggregation completed successfully.');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to aggregate stats: ' . $e->getMessage());
            Log::error('Product stats aggregation failed', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }

    /**
     * Update stats for a single product
     */
    protected function updateSingleProduct(int $productId): void
    {
        $this->info("Updating stats for product ID: {$productId}");
        ProductStat::updateForProduct($productId);
        $this->info('Done.');
    }

    /**
     * Update stats for all products with views
     */
    protected function updateAllProducts(): void
    {
        $productIds = ProductView::distinct('product_id')
            ->pluck('product_id');

        $total = $productIds->count();
        $this->info("Updating stats for {$total} products...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($productIds as $productId) {
            ProductStat::updateForProduct($productId);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Update stats for products with views in the last 24 hours
     */
    protected function updateRecentlyViewedProducts(): void
    {
        $productIds = ProductView::where('viewed_at', '>=', now()->subHours(24))
            ->distinct('product_id')
            ->pluck('product_id');

        $total = $productIds->count();
        $this->info("Updating stats for {$total} recently viewed products...");

        if ($total === 0) {
            $this->info('No products with recent views found.');
            return;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($productIds as $productId) {
            ProductStat::updateForProduct($productId);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }
}
