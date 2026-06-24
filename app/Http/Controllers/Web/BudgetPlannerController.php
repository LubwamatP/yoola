<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class BudgetPlannerController extends Controller
{
    /**
     * Show the budget planner tool
     */
    public function index()
    {
        // Get categories with product counts and price ranges
        $categories = Category::whereHas('product', function($q) {
            $q->where('status', 1)->where('current_stock', '>', 0);
        })
        ->withCount(['product' => function($q) {
            $q->where('status', 1)->where('current_stock', '>', 0);
        }])
        ->get()
        ->map(function($category) {
            $products = Product::where('category_id', $category->id)
                ->where('status', 1)
                ->where('current_stock', '>', 0)
                ->get();
            
            $prices = $products->map(function($p) {
                return $p->unit_price * (1 - ($p->discount / 100));
            });
            
            $category->min_price = $prices->min() ?? 0;
            $category->max_price = $prices->max() ?? 0;
            $category->avg_price = $prices->avg() ?? 0;
            
            return $category;
        })
        ->filter(function($cat) {
            return $cat->product_count > 0;
        })
        ->sortBy('name');

        // Popular budget ranges for Uganda
        $popularBudgets = [
            ['amount' => 500000, 'label' => '500K UGX', 'description' => 'Small appliances'],
            ['amount' => 1000000, 'label' => '1M UGX', 'description' => 'TV or Fridge'],
            ['amount' => 2000000, 'label' => '2M UGX', 'description' => 'Multiple items'],
            ['amount' => 5000000, 'label' => '5M UGX', 'description' => 'Full home setup'],
        ];

        return view(VIEW_FILE_NAMES['budget_planner'], compact('categories', 'popularBudgets'));
    }

    /**
     * Get product recommendations based on budget
     */
    public function getRecommendations(Request $request)
    {
        $budget = (float) $request->input('budget', 0);
        $categoryIds = $request->input('categories', []);
        $prioritize = $request->input('prioritize', 'value'); // value, variety, quality

        if ($budget <= 0) {
            return response()->json(['error' => 'Please enter a valid budget'], 400);
        }

        // Get products within budget
        $query = Product::where('status', 1)
            ->where('current_stock', '>', 0)
            ->with(['category', 'brand']);

        if (!empty($categoryIds)) {
            $query->whereIn('category_id', $categoryIds);
        }

        $products = $query->get()->map(function($product) {
            $finalPrice = $product->unit_price * (1 - ($product->discount / 100));
            $product->final_price = $finalPrice;
            return $product;
        })->filter(function($product) use ($budget) {
            return $product->final_price <= $budget;
        });

        // Generate smart combinations
        $combinations = $this->generateCombinations($products, $budget, $prioritize);

        // Single best items per category
        $bestPerCategory = $products->groupBy('category_id')
            ->map(function($categoryProducts) use ($prioritize) {
                if ($prioritize === 'quality') {
                    return $categoryProducts->sortByDesc('unit_price')->first();
                }
                return $categoryProducts->sortByDesc(function($p) {
                    return $p->discount > 0 ? $p->discount : 0;
                })->first();
            })
            ->filter()
            ->values();

        return response()->json([
            'budget' => $budget,
            'formatted_budget' => number_format($budget) . '/=',
            'total_products_in_budget' => $products->count(),
            'combinations' => $combinations,
            'best_per_category' => $bestPerCategory->take(6)->map(function($p) {
                return $this->formatProduct($p);
            }),
            'all_affordable' => $products->sortBy('final_price')->take(20)->map(function($p) {
                return $this->formatProduct($p);
            })->values(),
        ]);
    }

    /**
     * Generate smart product combinations within budget
     */
    private function generateCombinations($products, $budget, $prioritize)
    {
        $combinations = [];

        // Combination 1: Best Value Bundle (maximize items)
        $valueBundle = $this->greedyPack($products, $budget, 'value');
        if (!empty($valueBundle['items'])) {
            $combinations[] = [
                'name' => 'Best Value Bundle',
                'description' => 'Get the most for your money',
                'icon' => '💰',
                'items' => $valueBundle['items'],
                'total' => $valueBundle['total'],
                'remaining' => $budget - $valueBundle['total'],
                'item_count' => count($valueBundle['items']),
            ];
        }

        // Combination 2: Quality Pick (fewer, better items)
        $qualityBundle = $this->greedyPack($products, $budget, 'quality');
        if (!empty($qualityBundle['items'])) {
            $combinations[] = [
                'name' => 'Premium Selection',
                'description' => 'Fewer items, higher quality',
                'icon' => '⭐',
                'items' => $qualityBundle['items'],
                'total' => $qualityBundle['total'],
                'remaining' => $budget - $qualityBundle['total'],
                'item_count' => count($qualityBundle['items']),
            ];
        }

        // Combination 3: Variety Pack (one from each category)
        $varietyBundle = $this->varietyPack($products, $budget);
        if (!empty($varietyBundle['items'])) {
            $combinations[] = [
                'name' => 'Variety Pack',
                'description' => 'One item from different categories',
                'icon' => '🎯',
                'items' => $varietyBundle['items'],
                'total' => $varietyBundle['total'],
                'remaining' => $budget - $varietyBundle['total'],
                'item_count' => count($varietyBundle['items']),
            ];
        }

        return $combinations;
    }

    /**
     * Greedy algorithm to pack products within budget
     */
    private function greedyPack($products, $budget, $strategy = 'value')
    {
        $items = [];
        $total = 0;
        $usedCategories = [];

        // Sort based on strategy
        if ($strategy === 'value') {
            $sorted = $products->sortBy('final_price');
        } else {
            $sorted = $products->sortByDesc('final_price');
        }

        foreach ($sorted as $product) {
            // Skip if we already have something from this category (for variety)
            if (in_array($product->category_id, $usedCategories)) {
                continue;
            }

            if ($total + $product->final_price <= $budget) {
                $items[] = $this->formatProduct($product);
                $total += $product->final_price;
                $usedCategories[] = $product->category_id;

                // Limit to 5 items per bundle
                if (count($items) >= 5) break;
            }
        }

        return ['items' => $items, 'total' => $total];
    }

    /**
     * Create variety pack with one item per category
     */
    private function varietyPack($products, $budget)
    {
        $items = [];
        $total = 0;

        $byCategory = $products->groupBy('category_id');
        
        // Get best value from each category
        foreach ($byCategory as $categoryProducts) {
            $best = $categoryProducts->sortBy('final_price')->first();
            
            if ($best && $total + $best->final_price <= $budget) {
                $items[] = $this->formatProduct($best);
                $total += $best->final_price;

                if (count($items) >= 4) break;
            }
        }

        return ['items' => $items, 'total' => $total];
    }

    /**
     * Format product for JSON response
     */
    private function formatProduct($product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'image' => getValidImage(path: 'storage/app/public/product/thumbnail/' . $product->thumbnail, type: 'product'),
            'category' => $product->category?->name ?? 'Uncategorized',
            'brand' => $product->brand?->name ?? '',
            'original_price' => $product->unit_price,
            'discount' => $product->discount,
            'final_price' => $product->final_price,
            'formatted_price' => number_format($product->final_price) . '/=',
            'url' => route('product', $product->slug),
            'in_stock' => $product->current_stock > 0,
            'stock_quantity' => $product->current_stock,
        ];
    }
}
