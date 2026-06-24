<?php

namespace App\Console\Commands;

use App\Models\ProductView;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupOldViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'views:cleanup
                            {--days=90 : Number of days to keep view records}
                            {--dry-run : Preview what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old product view records to maintain database performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("Cleaning up product views older than {$days} days...");

        try {
            $cutoffDate = now()->subDays($days);

            $query = ProductView::where('viewed_at', '<', $cutoffDate);
            $count = $query->count();

            if ($count === 0) {
                $this->info('No old view records found to clean up.');
                return Command::SUCCESS;
            }

            if ($dryRun) {
                $this->warn("DRY RUN: Would delete {$count} view records older than {$cutoffDate->toDateString()}");
                return Command::SUCCESS;
            }

            $this->info("Deleting {$count} view records...");

            // Delete in chunks to avoid memory issues
            $deleted = 0;
            $chunkSize = 1000;

            while ($deleted < $count) {
                $batchCount = ProductView::where('viewed_at', '<', $cutoffDate)
                    ->limit($chunkSize)
                    ->delete();

                if ($batchCount === 0) {
                    break;
                }

                $deleted += $batchCount;
                $this->info("Deleted {$deleted}/{$count} records...");
            }

            $this->info("Successfully cleaned up {$deleted} old view records.");

            Log::info('Product views cleanup completed', [
                'deleted_count' => $deleted,
                'cutoff_date' => $cutoffDate->toDateString()
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to cleanup views: ' . $e->getMessage());
            Log::error('Product views cleanup failed', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}
