<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\BudgetPlannerService;
use App\Models\Product;

echo "=== Budget Planner Test for Uganda ===\n\n";

// Test 1: Check products in database
$totalProducts = Product::active()->where('current_stock', '>', 0)->count();
echo "Total active products in stock: {$totalProducts}\n";

// Test 2: Products within 2M budget
$productsUnder2M = Product::active()
    ->where('current_stock', '>', 0)
    ->where('unit_price', '<=', 2000000)
    ->where('unit_price', '>', 0)
    ->count();
echo "Products under UGX 2,000,000: {$productsUnder2M}\n\n";

// Test 3: Get sample products with prices
echo "Sample products:\n";
$samples = Product::active()
    ->where('current_stock', '>', 0)
    ->where('unit_price', '<=', 2000000)
    ->where('unit_price', '>', 0)
    ->with('category', 'brand')
    ->orderBy('unit_price', 'desc')
    ->limit(5)
    ->get();

foreach ($samples as $product) {
    $category = $product->category->name ?? 'N/A';
    $brand = $product->brand->name ?? 'Generic';
    echo "- {$product->name} ({$brand}) - UGX " . number_format($product->unit_price) . " [{$category}]\n";
}

echo "\n=== Testing BudgetPlannerService ===\n\n";

try {
    $service = new BudgetPlannerService();
    
    // Test with 2M budget
    echo "Testing with UGX 2,000,000 budget...\n";
    $result = $service->getRecommendations(2000000);
    
    echo "Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
    echo "Bundles created: " . count($result['bundles']) . "\n";
    echo "Total products available: " . ($result['total_products_available'] ?? 0) . "\n\n";
    
    if (!empty($result['bundles'])) {
        foreach ($result['bundles'] as $bundle) {
            echo "📦 {$bundle['name']}\n";
            echo "   {$bundle['description']}\n";
            echo "   Total: UGX " . number_format($bundle['total_price']) . "\n";
            echo "   Products:\n";
            foreach ($bundle['products'] as $item) {
                echo "   - {$item['product']->name}: UGX " . number_format($item['price']) . "\n";
            }
            if (!empty($bundle['savings_tip'])) {
                echo "   💡 Tip: {$bundle['savings_tip']}\n";
            }
            echo "\n";
        }
    }
    
    echo "✅ Budget Planner is working!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
