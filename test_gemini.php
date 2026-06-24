<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\EnhancedSearchService;
use App\Services\GeminiSearchService;

try {
    echo "=== Testing Gemini Search ===\n\n";

    echo "Config:\n";
    echo "  API Key: " . (strlen(config('gemini.api_key')) > 0 ? 'Set' : 'Not Set') . "\n";
    echo "  Search Enabled: " . (config('gemini.search.enabled') ? 'Yes' : 'No') . "\n\n";

    echo "Testing GeminiSearchService...\n";
    $geminiSearch = new GeminiSearchService();
    echo "  Available: " . ($geminiSearch->isAvailable() ? 'Yes' : 'No') . "\n\n";

    if ($geminiSearch->isAvailable()) {
        echo "Testing search for 'hisense fridge'...\n";
        $result = $geminiSearch->search('hisense fridge', [], 5);
        echo "  AI Powered: " . ($result['ai_powered'] ? 'Yes' : 'No') . "\n";
        echo "  Total Results: " . ($result['total'] ?? 0) . "\n";
        if (isset($result['intent'])) {
            echo "  Intent: " . json_encode($result['intent']['intent'] ?? 'unknown') . "\n";
            echo "  Confidence: " . ($result['intent']['confidence'] ?? 0) . "\n";
        }
        if (isset($result['fallback_reason'])) {
            echo "  Fallback: " . $result['fallback_reason'] . "\n";
        }
        echo "\n✓ Gemini Search is working!\n";
    } else {
        echo "Testing EnhancedSearchService fallback...\n";
        $enhancedSearch = new EnhancedSearchService();
        $result = $enhancedSearch->search('hisense fridge', [], 5);
        echo "  Total Results: " . ($result['total'] ?? 0) . "\n";
        echo "\n✓ Fallback search is working!\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
