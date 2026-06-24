<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\BundleCategory;
use App\Models\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BundleController extends Controller
{
    /**
     * Display all bundles
     */
    public function index(Request $request): View
    {
        $bundles = Bundle::available()
            ->ordered()
            ->with(['products' => function($q) {
                $q->select('products.id', 'name', 'thumbnail', 'unit_price', 'discount', 'discount_type');
            }])
            ->when($request->type, function($q) use ($request) {
                return $q->ofType($request->type);
            })
            ->when($request->category, function($q) use ($request) {
                return $q->where('bundle_category_id', $request->category);
            })
            ->when($request->featured, function($q) {
                return $q->featured();
            })
            ->when($request->sort, function($q) use ($request) {
                return match($request->sort) {
                    'price_low' => $q->orderBy('bundle_price', 'asc'),
                    'price_high' => $q->orderBy('bundle_price', 'desc'),
                    'popular' => $q->orderBy('purchase_count', 'desc'),
                    'newest' => $q->orderBy('created_at', 'desc'),
                    'savings' => $q->orderByRaw('(original_price - bundle_price) DESC'),
                    default => $q->ordered()
                };
            })
            ->paginate(getWebConfig('pagination_limit') ?? 12);

        $categories = BundleCategory::active()
            ->ordered()
            ->withCount(['bundles' => function($q) {
                $q->available();
            }])
            ->having('bundles_count', '>', 0)
            ->get();

        $bundleTypes = [
            'product' => translate('Product_Bundles'),
            'category' => translate('Category_Bundles'),
            'themed' => translate('Themed_Bundles'),
        ];

        return view('theme-views.bundles.index', compact('bundles', 'categories', 'bundleTypes'));
    }

    /**
     * Display a single bundle
     */
    public function show(string $slug): View|RedirectResponse
    {
        $bundle = Bundle::where('slug', $slug)
            ->with(['products' => function($q) {
                $q->with(['brand', 'category', 'reviews' => function($r) {
                    $r->where('status', 1);
                }]);
            }, 'category', 'seo'])
            ->first();

        if (!$bundle) {
            return redirect()->route('bundles.index')
                ->with('error', translate('Bundle_not_found'));
        }

        // Check if bundle is available
        if (!$bundle->isAvailable()) {
            return redirect()->route('bundles.index')
                ->with('error', translate('This_bundle_is_no_longer_available'));
        }

        // Increment view count
        $bundle->incrementViewCount();

        // Get related bundles
        $relatedBundles = Bundle::available()
            ->where('id', '!=', $bundle->id)
            ->where(function($q) use ($bundle) {
                $q->where('bundle_type', $bundle->bundle_type)
                  ->orWhere('bundle_category_id', $bundle->bundle_category_id);
            })
            ->limit(4)
            ->get();

        // SEO data
        $seoInfo = $bundle->seo;
        $metaTitle = $seoInfo?->meta_title ?? $bundle->meta_title ?? $bundle->name . ' | ' . translate('Bundle_Deal');
        $metaDescription = $seoInfo?->meta_description ?? $bundle->meta_description ?? $bundle->short_description;

        return view('theme-views.bundles.show', compact('bundle', 'relatedBundles', 'metaTitle', 'metaDescription'));
    }

    /**
     * Add bundle to cart
     */
    public function addToCart(Request $request, int $bundleId): JsonResponse|RedirectResponse
    {
        $bundle = Bundle::with('products')->find($bundleId);

        if (!$bundle || !$bundle->isAvailable()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => translate('Bundle_not_available')
                ], 404);
            }
            return back()->with('error', translate('Bundle_not_available'));
        }

        // Check product stock
        foreach ($bundle->products as $product) {
            $quantity = $product->pivot->quantity ?? 1;
            
            if ($product->current_stock < $quantity) {
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 0,
                        'message' => translate('Insufficient_stock_for') . ' ' . $product->name
                    ], 400);
                }
                return back()->with('error', translate('Insufficient_stock_for') . ' ' . $product->name);
            }
        }

        try {
            DB::beginTransaction();

            $customerId = Auth::guard('customer')->id();
            $guestId = session('guest_id');
            
            if (!$customerId && !$guestId) {
                $guestId = Str::uuid()->toString();
                session(['guest_id' => $guestId]);
            }

            $cartGroupId = $customerId 
                ? $customerId . '-' . Str::uuid()->toString()
                : $guestId . '-' . Str::uuid()->toString();

            // Add each product in bundle to cart
            $cartItems = $bundle->getCartItems();
            
            foreach ($cartItems as $item) {
                $product = $bundle->products->find($item['product_id']);
                
                // Check if product already in cart (for this bundle)
                $existingCart = Cart::where('customer_id', $customerId)
                    ->where('product_id', $item['product_id'])
                    ->where('variant', $item['variant'])
                    ->where('color', $item['color'])
                    ->first();

                if ($existingCart) {
                    // Update quantity
                    $existingCart->quantity += $item['quantity'];
                    $existingCart->save();
                } else {
                    // Create new cart item
                    Cart::create([
                        'customer_id' => $customerId,
                        'cart_group_id' => $cartGroupId,
                        'product_id' => $item['product_id'],
                        'product_type' => $product->product_type ?? 'physical',
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'tax' => $product->tax ?? 0,
                        'discount' => $item['discount'],
                        'tax_model' => $product->tax_model ?? 'exclude',
                        'slug' => $product->slug,
                        'name' => $product->name,
                        'thumbnail' => $product->thumbnail,
                        'seller_id' => $product->user_id,
                        'seller_is' => $product->added_by,
                        'variant' => $item['variant'],
                        'color' => $item['color'],
                        'is_guest' => $customerId ? 0 : 1,
                        'shop_info' => json_encode([
                            'bundle_id' => $bundle->id,
                            'bundle_name' => $bundle->name,
                        ]),
                    ]);
                }
            }

            // Increment bundle purchase count
            $bundle->incrementPurchaseCount();

            DB::commit();

            $cartCount = Cart::where('customer_id', $customerId)->count();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => translate('Bundle_added_to_cart_successfully'),
                    'cart_count' => $cartCount,
                    'bundle_name' => $bundle->name,
                ]);
            }

            return redirect()->route('shop-cart')
                ->with('success', translate('Bundle_added_to_cart_successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => translate('Failed_to_add_bundle_to_cart')
                ], 500);
            }
            
            return back()->with('error', translate('Failed_to_add_bundle_to_cart'));
        }
    }

    /**
     * Get bundles for homepage widget
     */
    public function homepageBundles(): View
    {
        $bundles = Bundle::available()
            ->homepage()
            ->ordered()
            ->with(['products' => function($q) {
                $q->select('products.id', 'name', 'thumbnail', 'unit_price');
            }])
            ->limit(6)
            ->get();

        return view('theme-views.partials._bundle-deals', compact('bundles'));
    }

    /**
     * Get featured bundles
     */
    public function featuredBundles(): View
    {
        $bundles = Bundle::available()
            ->featured()
            ->ordered()
            ->with(['products' => function($q) {
                $q->select('products.id', 'name', 'thumbnail', 'unit_price');
            }])
            ->limit(8)
            ->get();

        return view('theme-views.partials._featured-bundles', compact('bundles'));
    }

    /**
     * Quick view bundle (for modal)
     */
    public function quickView(int $bundleId): JsonResponse
    {
        $bundle = Bundle::with(['products' => function($q) {
            $q->select('products.id', 'name', 'thumbnail', 'unit_price', 'discount', 'discount_type', 'current_stock');
        }])->find($bundleId);

        if (!$bundle || !$bundle->isAvailable()) {
            return response()->json([
                'status' => 0,
                'message' => translate('Bundle_not_available')
            ], 404);
        }

        $html = view('theme-views.partials._bundle-quick-view', compact('bundle'))->render();

        return response()->json([
            'status' => 1,
            'html' => $html,
            'bundle' => [
                'id' => $bundle->id,
                'name' => $bundle->name,
                'original_price' => $bundle->original_price,
                'bundle_price' => $bundle->bundle_price,
                'savings' => $bundle->savings_amount,
                'savings_percentage' => $bundle->savings_percentage,
            ]
        ]);
    }
}
