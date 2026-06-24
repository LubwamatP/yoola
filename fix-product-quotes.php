<?php
/**
 * Fix Product Names with Quote Issues
 * Run: php fix-product-quotes.php
 * 
 * Fixes:
 * - 50" → 50-inch
 * - 50&quot; → 50-inch
 * - Removes problematic quote characters
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "🔧 Product Name Quote Fixer\n";
echo "============================\n\n";

// Find products with problematic characters
$products = Product::where('name', 'like', '%"%')
    ->orWhere('name', 'like', '%&quot;%')
    ->orWhere('name', 'like', '%″%')
    ->orWhere('name', 'like', '%\'%')
    ->get();

echo "Found {$products->count()} products with quote issues\n\n";

if ($products->count() === 0) {
    echo "✅ No products need fixing!\n";
    exit(0);
}

$fixed = 0;
$errors = 0;

DB::beginTransaction();

try {
    foreach ($products as $product) {
        $originalName = $product->name;
        $newName = $originalName;
        
        // Fix patterns:
        // 50" → 50-inch
        // 50 " → 50-inch  
        // 50&quot; → 50-inch
        // 50″ → 50-inch
        
        // Pattern 1: Number followed by quote (50", 32", etc.)
        $newName = preg_replace('/(\d+)\s*["″&quot;]/', '$1-inch', $newName);
        
        // Pattern 2: HTML entity &quot;
        $newName = str_replace('&quot;', '-inch', $newName);
        $newName = str_replace('&amp;quot;', '-inch', $newName);
        
        // Pattern 3: Fancy quotes
        $newName = str_replace(['"', '"', '″', '‴'], '-inch', $newName);
        
        // Pattern 4: Regular double quote
        $newName = preg_replace('/(\d+)"/', '$1-inch', $newName);
        
        // Clean up double "inch inch" or "-inch-inch"
        $newName = preg_replace('/-inch\s*-inch/', '-inch', $newName);
        $newName = preg_replace('/inch\s*inch/', 'inch', $newName);
        
        // Clean up "inch inch" from patterns
        $newName = str_replace('inch-inch', 'inch', $newName);
        
        // Remove any remaining standalone quotes
        $newName = str_replace('"', '', $newName);
        
        // Clean up extra spaces
        $newName = preg_replace('/\s+/', ' ', $newName);
        $newName = trim($newName);
        
        if ($newName !== $originalName) {
            echo "📝 #{$product->id}\n";
            echo "   Before: {$originalName}\n";
            echo "   After:  {$newName}\n\n";
            
            $product->name = $newName;
            $product->save();
            $fixed++;
        }
    }
    
    DB::commit();
    
    echo "============================\n";
    echo "✅ Fixed {$fixed} products\n";
    echo "❌ Errors: {$errors}\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Done! Clear cache with: php artisan cache:clear\n";
