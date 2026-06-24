<?php

namespace App\Console\Commands;

use App\Services\AIInboxResponderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessAIInbox extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ai:process-inbox';

    /**
     * The console command description.
     */
    protected $description = 'Process incoming customer messages and auto-respond with AI';

    /**
     * Execute the console command.
     */
    public function handle(AIInboxResponderService $service): int
    {
        $this->info('🤖 Processing inbox messages with AI...');

        try {
            $results = $service->processNewMessages();
            
            $this->info("✅ Processed: {$results['processed']}");
            $this->info("   → Auto-responded: {$results['responded']}");
            $this->info("   → Escalated: {$results['escalated']}");
            
            if ($results['errors'] > 0) {
                $this->warn("   ⚠️ Errors: {$results['errors']}");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            Log::error('AI inbox processing failed', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}
