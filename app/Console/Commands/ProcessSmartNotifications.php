<?php

namespace App\Console\Commands;

use App\Services\SmartNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessSmartNotifications extends Command
{
    /**
     * The name and signature of the console command.
     * PRD Section 5: Smart Push Notifications
     */
    protected $signature = 'notifications:process {type?}';

    /**
     * The console command description.
     */
    protected $description = 'Process smart notifications (cart abandonment, price drops, win-back, etc.)';

    /**
     * Execute the console command.
     */
    public function handle(SmartNotificationService $service): int
    {
        $type = $this->argument('type');

        $this->info('🔔 Processing smart notifications...');
        $this->newLine();

        try {
            $totalSent = 0;
            $totalSkipped = 0;
            $totalErrors = 0;

            // Cart Abandonment (30min, 4hr, 24hr delays)
            if (!$type || $type === 'cart') {
                $this->info('🛒 Processing cart abandonment...');
                $results = $service->processCartAbandonment();
                $this->line("   → {$results['sent']} sent, {$results['skipped']} skipped, {$results['errors']} errors");
                $totalSent += $results['sent'];
                $totalSkipped += $results['skipped'];
                $totalErrors += $results['errors'];
            }

            // Price Drops
            if (!$type || $type === 'price') {
                $this->info('📉 Processing price drops...');
                $results = $service->processPriceDrops();
                $this->line("   → {$results['sent']} sent, {$results['skipped']} skipped, {$results['errors']} errors");
                $totalSent += $results['sent'];
                $totalSkipped += $results['skipped'];
                $totalErrors += $results['errors'];
            }

            // Win-Back (inactive 14+ days)
            if (!$type || $type === 'winback') {
                $this->info('👋 Processing win-back for inactive users...');
                $results = $service->processWinBack();
                $this->line("   → {$results['sent']} sent, {$results['skipped']} skipped, {$results['errors']} errors");
                $totalSent += $results['sent'];
                $totalSkipped += $results['skipped'];
                $totalErrors += $results['errors'];
            }

            $this->newLine();
            $this->info("✅ Smart notifications processed!");
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Sent', $totalSent],
                    ['Skipped', $totalSkipped],
                    ['Errors', $totalErrors],
                ]
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error processing notifications: ' . $e->getMessage());
            Log::error('Smart notification processing failed', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}
