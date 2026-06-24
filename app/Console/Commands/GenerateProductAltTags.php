<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class GenerateProductAltTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:generate-alt-tags 
                            {--overwrite : Overwrite existing alt tags}
                            {--dry-run : Show what would be changed without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SEO-optimized image alt tags for all products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $overwrite = $this->option('overwrite');
        $dryRun = $this->option('dry-run');

        $this->info('🏷️  Generating product image alt tags...');
        $this->newLine();

        // Get products - only those without alt tags unless overwrite is set
        $query = Product::with(['brand', 'category']);
        
        if (!$overwrite) {
            $query->where(function($q) {
                $q->whereNull('image_alt')
                  ->orWhere('image_alt', '');
            });
        }

        $products = $query->get();
        $totalProducts = $products->count();

        if ($totalProducts === 0) {
            $this->info('✅ All products already have alt tags. Use --overwrite to regenerate.');
            return Command::SUCCESS;
        }

        $this->info("Found {$totalProducts} products to update");
        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be saved');
            $this->newLine();
        }

        $bar = $this->output->createProgressBar($totalProducts);
        $bar->start();

        $updated = 0;
        $examples = [];

        foreach ($products as $product) {
            // Generate alt tag
            $altTag = $this->generateAltTag($product);

            if ($dryRun) {
                // Collect examples for dry run
                if (count($examples) < 5) {
                    $examples[] = [
                        'name' => $product->name,
                        'current' => $product->image_alt ?? '(empty)',
                        'new' => $altTag,
                    ];
                }
            } else {
                // Actually update the product
                $product->image_alt = $altTag;
                $product->save();
            }

            $updated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info('📋 Example changes:');
            $this->newLine();
            
            foreach ($examples as $example) {
                $this->line("  Product: {$example['name']}");
                $this->line("  Current: <fg=red>{$example['current']}</>");
                $this->line("  New:     <fg=green>{$example['new']}</>");
                $this->newLine();
            }

            $this->warn("Would update {$updated} products. Run without --dry-run to apply changes.");
        } else {
            $this->info("✅ Successfully updated {$updated} products with SEO alt tags!");
        }

        return Command::SUCCESS;
    }

    /**
     * Generate an SEO-optimized alt tag for a product
     */
    private function generateAltTag(Product $product): string
    {
        $parts = [];

        // Start with product name (clean it up)
        $name = $product->name;
        
        // Remove common filler words and clean up
        $name = preg_replace('/\s+/', ' ', $name); // Normalize spaces
        $parts[] = trim($name);

        // Add brand if available and not already in name
        if ($product->brand && $product->brand->name) {
            $brandName = $product->brand->name;
            if (stripos($name, $brandName) === false) {
                // Brand not in name, prepend it
                array_unshift($parts, $brandName);
            }
        }

        // Add category context if helpful
        if ($product->category && $product->category->name) {
            $categoryName = $product->category->name;
            // Only add if it adds context (not redundant)
            if (stripos($name, $categoryName) === false) {
                $parts[] = $categoryName;
            }
        }

        // Build the alt tag
        $altTag = implode(' - ', array_unique($parts));

        // Add location suffix for local SEO
        $altTag .= ' | Yoola Uganda';

        // Ensure it's not too long (keep under 125 chars for best SEO)
        if (strlen($altTag) > 125) {
            $altTag = substr($altTag, 0, 122) . '...';
        }

        return $altTag;
    }
}
