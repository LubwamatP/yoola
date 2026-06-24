<?php

namespace App\Console\Commands;

use App\Models\ProductCoView;
use App\Models\ProductView;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateCoViewData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coview:update
                            {--days=7 : Number of days to analyze for co-view patterns}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update product co-view data for collaborative filtering recommendations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $this->info("Analyzing co-view patterns for the last {$days} days...");

        try {
            $cutoffDate = now()->subDays($days);

            // Get sessions with multiple product views
            $sessions = ProductView::select('session_id')
                ->where('viewed_at', '>=', $cutoffDate)
                ->groupBy('session_id')
                ->havingRaw('COUNT(DISTINCT product_id) >= 2')
                ->pluck('session_id');

            $totalSessions = $sessions->count();
            $this->info("Found {$totalSessions} sessions with multiple product views.");

            if ($totalSessions === 0) {
                $this->info('No co-view data to process.');
                return Command::SUCCESS;
            }

            $bar = $this->output->createProgressBar($totalSessions);
            $bar->start();

            $processedPairs = 0;

            foreach ($sessions as $sessionId) {
                $productIds = ProductView::where('session_id', $sessionId)
                    ->where('viewed_at', '>=', $cutoffDate)
                    ->distinct('product_id')
                    ->pluck('product_id')
                    ->toArray();

                // Create pairs from all products viewed in this session
                $pairCount = count($productIds);
                for ($i = 0; $i < $pairCount; $i++) {
                    for ($j = $i + 1; $j < $pairCount; $j++) {
                        ProductCoView::recordCoView($productIds[$i], $productIds[$j]);
                        ProductCoView::recordCoView($productIds[$j], $productIds[$i]);
                        $processedPairs++;
                    }
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            $this->info("Processed {$processedPairs} co-view pairs.");
            $this->info('Co-view data update completed successfully.');

            Log::info('Co-view data update completed', [
                'sessions_processed' => $totalSessions,
                'pairs_processed' => $processedPairs,
                'days_analyzed' => $days
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to update co-view data: ' . $e->getMessage());
            Log::error('Co-view data update failed', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}
