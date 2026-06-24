<?php

namespace App\Services;

use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SmartNotificationService
{
    /**
     * Notification triggers and their configurations
     * PRD Section 5: Smart Push Notifications
     */
    private const TRIGGERS = [
        'cart_abandonment' => [
            'delays' => [30, 240, 1440], // minutes: 30min, 4hr, 24hr
            'enabled' => true,
        ],
        'price_drop' => [
            'threshold' => 5, // minimum % drop to trigger
            'enabled' => true,
        ],
        'back_in_stock' => [
            'enabled' => true,
        ],
        'new_arrival' => [
            'enabled' => true,
        ],
        'accessory_upsell' => [
            'delay' => 1440, // 24 hours after delivery
            'enabled' => true,
        ],
        'order_status' => [
            'enabled' => true,
        ],
        'win_back' => [
            'inactive_days' => 14,
            'enabled' => true,
        ],
        'seller_promotion' => [
            'enabled' => true,
        ],
    ];

    /**
     * Maximum notifications per user per day
     */
    private const DAILY_LIMIT = 2;

    /**
     * FCM Server Key (from 6Valley database settings)
     */
    private $fcmServerKey;

    public function __construct()
    {
        // Use existing 6Valley FCM settings from database (Admin → 3rd Party → Firebase)
        $this->fcmServerKey = getWebConfig('push_notification_key') ?? config('services.fcm.server_key');
    }

    /**
     * Process cart abandonment notifications
     * Called by scheduled job every 15 minutes
     */
    public function processCartAbandonment(): array
    {
        $results = ['sent' => 0, 'skipped' => 0, 'errors' => 0];

        // Get carts that haven't been checked out
        $abandonedCarts = Cart::with(['user', 'product'])
            ->whereHas('user', function ($q) {
                $q->whereNotNull('fcm_token');
            })
            ->where('created_at', '<', now()->subMinutes(30))
            ->get()
            ->groupBy('user_id');

        foreach ($abandonedCarts as $userId => $cartItems) {
            $user = $cartItems->first()->user;
            
            // Check if user already received notification recently
            if ($this->hasRecentNotification($userId, 'cart_abandonment')) {
                $results['skipped']++;
                continue;
            }

            // Check daily limit
            if ($this->exceedsDailyLimit($userId)) {
                $results['skipped']++;
                continue;
            }

            // Get the most valuable item in cart
            $topItem = $cartItems->sortByDesc(function ($item) {
                return $item->product->unit_price ?? 0;
            })->first();

            if (!$topItem || !$topItem->product) {
                continue;
            }

            $message = $this->generateCartAbandonmentMessage($user, $topItem->product, $cartItems->count());
            
            $sent = $this->sendPushNotification(
                $user->fcm_token,
                $message['title'],
                $message['body'],
                [
                    'type' => 'cart_abandonment',
                    'product_id' => $topItem->product->id,
                    'url' => '/shop-cart',
                ]
            );

            if ($sent) {
                $this->logNotification($userId, 'cart_abandonment', $topItem->product->id);
                $results['sent']++;
            } else {
                $results['errors']++;
            }
        }

        return $results;
    }

    /**
     * Process price drop notifications
     */
    public function processPriceDrops(): array
    {
        $results = ['sent' => 0, 'skipped' => 0, 'errors' => 0];

        // Get products with recent price drops
        $priceDrops = Cache::get('recent_price_drops', []);

        foreach ($priceDrops as $productId => $dropInfo) {
            // Find users who viewed or wishlisted this product
            $interestedUsers = $this->getUsersInterestedInProduct($productId);

            foreach ($interestedUsers as $user) {
                if (!$user->fcm_token || $this->exceedsDailyLimit($user->id)) {
                    $results['skipped']++;
                    continue;
                }

                $product = Product::find($productId);
                if (!$product) continue;

                $message = $this->generatePriceDropMessage($product, $dropInfo);
                
                $sent = $this->sendPushNotification(
                    $user->fcm_token,
                    $message['title'],
                    $message['body'],
                    [
                        'type' => 'price_drop',
                        'product_id' => $productId,
                        'url' => '/product/' . $product->slug,
                    ]
                );

                if ($sent) {
                    $this->logNotification($user->id, 'price_drop', $productId);
                    $results['sent']++;
                } else {
                    $results['errors']++;
                }
            }
        }

        return $results;
    }

    /**
     * Process back in stock notifications
     */
    public function processBackInStock(Product $product): array
    {
        $results = ['sent' => 0, 'skipped' => 0, 'errors' => 0];

        // Find users who viewed this when it was out of stock
        $interestedUsers = $this->getUsersInterestedInProduct($product->id);

        foreach ($interestedUsers as $user) {
            if (!$user->fcm_token || $this->exceedsDailyLimit($user->id)) {
                $results['skipped']++;
                continue;
            }

            $message = $this->generateBackInStockMessage($product);
            
            $sent = $this->sendPushNotification(
                $user->fcm_token,
                $message['title'],
                $message['body'],
                [
                    'type' => 'back_in_stock',
                    'product_id' => $product->id,
                    'url' => '/product/' . $product->slug,
                ]
            );

            if ($sent) {
                $this->logNotification($user->id, 'back_in_stock', $product->id);
                $results['sent']++;
            } else {
                $results['errors']++;
            }
        }

        return $results;
    }

    /**
     * Process accessory upsell after order delivery
     */
    public function processAccessoryUpsell(Order $order): array
    {
        $results = ['sent' => 0, 'skipped' => 0, 'errors' => 0];

        $user = $order->customer;
        if (!$user || !$user->fcm_token) {
            return $results;
        }

        // Get the main product from the order
        $mainProduct = $order->details->first()?->product;
        if (!$mainProduct) {
            return $results;
        }

        // Find related accessories
        $accessories = $this->findAccessories($mainProduct);
        if ($accessories->isEmpty()) {
            return $results;
        }

        $message = $this->generateAccessoryUpsellMessage($mainProduct, $accessories);
        
        $sent = $this->sendPushNotification(
            $user->fcm_token,
            $message['title'],
            $message['body'],
            [
                'type' => 'accessory_upsell',
                'product_id' => $mainProduct->id,
                'url' => '/products?category_id=' . $mainProduct->category_id,
            ]
        );

        if ($sent) {
            $this->logNotification($user->id, 'accessory_upsell', $mainProduct->id);
            $results['sent']++;
        } else {
            $results['errors']++;
        }

        return $results;
    }

    /**
     * Send order status notification
     */
    public function sendOrderStatusNotification(Order $order, string $status): bool
    {
        $user = $order->customer;
        if (!$user || !$user->fcm_token) {
            return false;
        }

        $messages = [
            'confirmed' => [
                'title' => '✅ Order Confirmed!',
                'body' => "Your order #{$order->id} has been confirmed. We're preparing it for shipment.",
            ],
            'processing' => [
                'title' => '📦 Order Processing',
                'body' => "Your order #{$order->id} is being prepared for shipment.",
            ],
            'out_for_delivery' => [
                'title' => '🚚 Out for Delivery!',
                'body' => "Your order #{$order->id} is on its way! Track: " . url('/track-order?order_id=' . $order->id),
            ],
            'delivered' => [
                'title' => '🎉 Order Delivered!',
                'body' => "Your order #{$order->id} has been delivered. Enjoy your purchase!",
            ],
        ];

        $message = $messages[$status] ?? null;
        if (!$message) {
            return false;
        }

        return $this->sendPushNotification(
            $user->fcm_token,
            $message['title'],
            $message['body'],
            [
                'type' => 'order_status',
                'order_id' => $order->id,
                'status' => $status,
                'url' => '/track-order?order_id=' . $order->id,
            ]
        );
    }

    /**
     * Generate cart abandonment message with AI-like personalization
     */
    private function generateCartAbandonmentMessage($user, Product $product, int $itemCount): array
    {
        $productName = \Illuminate\Support\Str::limit($product->name, 30);
        $price = number_format($product->unit_price);

        $templates = [
            [
                'title' => "🛒 Don't forget your cart!",
                'body' => "Your {$productName} is waiting! Complete your order - pay with MoMo in 30 seconds.",
            ],
            [
                'title' => "⚡ Still thinking about it?",
                'body' => "{$productName} (UGX {$price}) is in your cart. Don't miss out!",
            ],
            [
                'title' => "🎯 Your cart misses you!",
                'body' => $itemCount > 1 
                    ?? "You have {$itemCount} items waiting. Complete your order now!" 
                    : "Your {$productName} is still available. Grab it before it's gone!",
            ],
        ];

        return $templates[array_rand($templates)];
    }

    /**
     * Generate price drop message
     */
    private function generatePriceDropMessage(Product $product, array $dropInfo): array
    {
        $productName = \Illuminate\Support\Str::limit($product->name, 25);
        $newPrice = number_format($product->unit_price);
        $oldPrice = number_format($dropInfo['old_price'] ?? $product->unit_price * 1.1);
        $dropPercent = $dropInfo['percent'] ?? 10;

        return [
            'title' => "🔥 Price Drop Alert!",
            'body' => "{$productName} now UGX {$newPrice} (was {$oldPrice}). {$dropPercent}% off - Limited time!",
        ];
    }

    /**
     * Generate back in stock message
     */
    private function generateBackInStockMessage(Product $product): array
    {
        $productName = \Illuminate\Support\Str::limit($product->name, 30);
        $price = number_format($product->unit_price);

        return [
            'title' => "🎉 Back in Stock!",
            'body' => "{$productName} is available again at UGX {$price}. Get it before it sells out!",
        ];
    }

    /**
     * Generate accessory upsell message
     */
    private function generateAccessoryUpsellMessage(Product $mainProduct, $accessories): array
    {
        $mainName = \Illuminate\Support\Str::limit($mainProduct->name, 20);
        $accessoryNames = $accessories->take(2)->pluck('name')->map(function ($name) {
            return \Illuminate\Support\Str::limit($name, 15);
        })->join(' & ');
        
        $lowestPrice = number_format($accessories->min('unit_price'));

        return [
            'title' => "🛡️ Protect your {$mainName}!",
            'body' => "Get {$accessoryNames} from UGX {$lowestPrice}. Complete your setup!",
        ];
    }

    /**
     * Send push notification via FCM
     */
    public function sendPushNotification(string $token, string $title, string $body, array $data = []): bool
    {
        if (empty($this->fcmServerKey)) {
            Log::warning('FCM server key not configured');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->fcmServerKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => '/storage/company/logo.png',
                    'click_action' => url($data['url'] ?? '/'),
                ],
                'data' => $data,
            ]);

            if ($response->successful()) {
                Log::info('Push notification sent', ['title' => $title, 'token' => substr($token, 0, 20) . '...']);
                return true;
            }

            Log::error('FCM error', ['response' => $response->body()]);
            return false;

        } catch (\Exception $e) {
            Log::error('FCM exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check if user has received this type of notification recently
     */
    private function hasRecentNotification(int $userId, string $type): bool
    {
        $cacheKey = "notification_{$userId}_{$type}";
        return Cache::has($cacheKey);
    }

    /**
     * Check if user exceeded daily notification limit
     */
    private function exceedsDailyLimit(int $userId): bool
    {
        $cacheKey = "notification_count_{$userId}_" . date('Y-m-d');
        $count = Cache::get($cacheKey, 0);
        return $count >= self::DAILY_LIMIT;
    }

    /**
     * Log notification sent
     */
    private function logNotification(int $userId, string $type, ?int $productId = null): void
    {
        // Increment daily count
        $countKey = "notification_count_{$userId}_" . date('Y-m-d');
        $count = Cache::get($countKey, 0);
        Cache::put($countKey, $count + 1, now()->endOfDay());

        // Mark this type as sent (prevent duplicate)
        $typeKey = "notification_{$userId}_{$type}";
        $ttl = match ($type) {
            'cart_abandonment' => 240, // 4 hours
            'price_drop' => 1440, // 24 hours
            'back_in_stock' => 1440,
            'accessory_upsell' => 10080, // 7 days
            default => 60,
        };
        Cache::put($typeKey, true, now()->addMinutes($ttl));

        // Log to database for analytics
        \DB::table('push_notification_logs')->insert([
            'user_id' => $userId,
            'type' => $type,
            'product_id' => $productId,
            'sent_at' => now(),
        ]);
    }

    /**
     * Get users interested in a product (viewed or wishlisted)
     */
    private function getUsersInterestedInProduct(int $productId)
    {
        // Get users who wishlisted this product
        $wishlistUsers = Wishlist::where('product_id', $productId)
            ->with('customer')
            ->get()
            ->pluck('customer')
            ->filter();

        // Get users who viewed this product (from visit logs if available)
        $viewedUsers = \DB::table('product_views')
            ->where('product_id', $productId)
            ->where('created_at', '>', now()->subDays(30))
            ->join('users', 'users.id', '=', 'product_views.user_id')
            ->whereNotNull('users.fcm_token')
            ->select('users.*')
            ->get();

        return $wishlistUsers->merge($viewedUsers)->unique('id');
    }

    /**
     * Find accessories for a product
     */
    private function findAccessories(Product $product)
    {
        // Find products in the same category with lower price (likely accessories)
        return Product::active()
            ->where('category_id', $product->category_id)
            ->where('unit_price', '<', $product->unit_price * 0.3) // Accessories usually < 30% of main product price
            ->where('id', '!=', $product->id)
            ->orderBy('unit_price', 'asc')
            ->take(3)
            ->get();
    }

    /**
     * Process new arrival notifications
     * PRD: "New product in a category user has browsed"
     */
    public function processNewArrival(Product $product): array
    {
        $results = ['sent' => 0, 'skipped' => 0, 'errors' => 0];

        // Find users who viewed products in this category
        $interestedUsers = \DB::table('product_views')
            ->join('products', 'products.id', '=', 'product_views.product_id')
            ->where('products.category_id', $product->category_id)
            ->where('product_views.created_at', '>', now()->subDays(30))
            ->join('users', 'users.id', '=', 'product_views.user_id')
            ->whereNotNull('users.fcm_token')
            ->select('users.*')
            ->distinct()
            ->get();

        foreach ($interestedUsers as $user) {
            if ($this->exceedsDailyLimit($user->id)) {
                $results['skipped']++;
                continue;
            }

            $message = $this->generateNewArrivalMessage($product);
            
            $sent = $this->sendPushNotification(
                $user->fcm_token,
                $message['title'],
                $message['body'],
                [
                    'type' => 'new_arrival',
                    'product_id' => $product->id,
                    'url' => '/product/' . $product->slug,
                ]
            );

            if ($sent) {
                $this->logNotification($user->id, 'new_arrival', $product->id);
                $results['sent']++;
            } else {
                $results['errors']++;
            }
        }

        return $results;
    }

    /**
     * Generate new arrival message
     */
    private function generateNewArrivalMessage(Product $product): array
    {
        $productName = \Illuminate\Support\Str::limit($product->name, 25);
        $price = number_format($product->unit_price);
        $brandName = $product->brand?->name ?? '';
        $sellerCount = Product::where('name', 'like', '%' . $product->name . '%')
            ->where('id', '!=', $product->id)
            ->count() + 1;

        $templates = [
            [
                'title' => "🆕 Just Landed!",
                'body' => "{$brandName} {$productName} now at UGX {$price}. Be the first to grab it!",
            ],
            [
                'title' => "✨ New Arrival Alert!",
                'body' => "{$productName} just dropped! Compare prices from {$sellerCount} seller(s) on Yoola.",
            ],
            [
                'title' => "🎯 Fresh Drop!",
                'body' => "The {$productName} is here! UGX {$price} - Shop now before it sells out.",
            ],
        ];

        return $templates[array_rand($templates)];
    }

    /**
     * Process seller promotion notifications
     * PRD: "Seller creates a flash sale or discount campaign"
     */
    public function processSellerPromotion(int $sellerId, string $promoTitle, int $discountPercent, array $productIds = []): array
    {
        $results = ['sent' => 0, 'skipped' => 0, 'errors' => 0];

        $seller = \App\Models\Seller::with('shop')->find($sellerId);
        if (!$seller || !$seller->shop) {
            return $results;
        }

        // Get users who have viewed/purchased from this seller
        $interestedUsers = \DB::table('orders')
            ->where('seller_id', $sellerId)
            ->where('created_at', '>', now()->subDays(90))
            ->join('users', 'users.id', '=', 'orders.customer_id')
            ->whereNotNull('users.fcm_token')
            ->select('users.*')
            ->distinct()
            ->get();

        // Also get users who viewed seller's products
        $viewedUsers = \DB::table('product_views')
            ->join('products', 'products.id', '=', 'product_views.product_id')
            ->where('products.user_id', $sellerId)
            ->where('product_views.created_at', '>', now()->subDays(30))
            ->join('users', 'users.id', '=', 'product_views.user_id')
            ->whereNotNull('users.fcm_token')
            ->select('users.*')
            ->distinct()
            ->get();

        $allUsers = collect($interestedUsers)->merge($viewedUsers)->unique('id');

        foreach ($allUsers as $user) {
            if ($this->exceedsDailyLimit($user->id)) {
                $results['skipped']++;
                continue;
            }

            $message = $this->generateSellerPromotionMessage($seller->shop->name, $promoTitle, $discountPercent);
            
            $sent = $this->sendPushNotification(
                $user->fcm_token,
                $message['title'],
                $message['body'],
                [
                    'type' => 'seller_promotion',
                    'seller_id' => $sellerId,
                    'url' => '/vendor-shop/' . $seller->shop->slug,
                ]
            );

            if ($sent) {
                $this->logNotification($user->id, 'seller_promotion', null);
                $results['sent']++;
            } else {
                $results['errors']++;
            }
        }

        return $results;
    }

    /**
     * Generate seller promotion message
     */
    private function generateSellerPromotionMessage(string $shopName, string $promoTitle, int $discountPercent): array
    {
        $templates = [
            [
                'title' => "🔥 Flash Sale at {$shopName}!",
                'body' => "{$promoTitle} - {$discountPercent}% off for limited time. Shop now →",
            ],
            [
                'title' => "⚡ {$shopName} Promo!",
                'body' => "Don't miss out! {$discountPercent}% off selected items. Limited time only!",
            ],
            [
                'title' => "🎉 Special Offer!",
                'body' => "{$shopName}: {$promoTitle}. Save {$discountPercent}% today only!",
            ],
        ];

        return $templates[array_rand($templates)];
    }

    /**
     * Process win-back notifications for inactive users
     * PRD: "User inactive 14+ days"
     */
    public function processWinBack(): array
    {
        $results = ['sent' => 0, 'skipped' => 0, 'errors' => 0];

        $inactiveDays = self::TRIGGERS['win_back']['inactive_days'];

        // Find users inactive for 14+ days with FCM tokens
        $inactiveUsers = User::whereNotNull('fcm_token')
            ->where('updated_at', '<', now()->subDays($inactiveDays))
            ->whereDoesntHave('orders', function ($q) use ($inactiveDays) {
                $q->where('created_at', '>', now()->subDays($inactiveDays));
            })
            ->take(100) // Batch limit
            ->get();

        foreach ($inactiveUsers as $user) {
            if ($this->hasRecentNotification($user->id, 'win_back')) {
                $results['skipped']++;
                continue;
            }

            if ($this->exceedsDailyLimit($user->id)) {
                $results['skipped']++;
                continue;
            }

            $message = $this->generateWinBackMessage($user);
            
            $sent = $this->sendPushNotification(
                $user->fcm_token,
                $message['title'],
                $message['body'],
                [
                    'type' => 'win_back',
                    'url' => '/products?sort_by=latest',
                ]
            );

            if ($sent) {
                $this->logNotification($user->id, 'win_back', null);
                $results['sent']++;
            } else {
                $results['errors']++;
            }
        }

        return $results;
    }

    /**
     * Generate win-back message
     */
    private function generateWinBackMessage($user): array
    {
        $userName = $user->f_name ?? 'there';
        
        // Get lowest prices for popular categories
        $lowestPhone = Product::active()->where('name', 'like', '%phone%')->min('unit_price');
        $lowestLaptop = Product::active()->where('name', 'like', '%laptop%')->min('unit_price');

        $templates = [
            [
                'title' => "👋 We miss you, {$userName}!",
                'body' => "Phones from UGX " . number_format($lowestPhone ?? 350000) . ", laptops from " . number_format($lowestLaptop ?? 1200000) . ". Webale! 🛍️",
            ],
            [
                'title' => "🎁 Special deals waiting!",
                'body' => "Hey {$userName}! New arrivals and hot prices await. Come back and save big!",
            ],
            [
                'title' => "⭐ {$userName}, check this out!",
                'body' => "Fresh electronics just dropped on Yoola. Your next upgrade is waiting!",
            ],
        ];

        return $templates[array_rand($templates)];
    }

    /**
     * Generate AI-powered notification message using Claude Haiku
     * PRD: AI content generation for personalized notifications
     */
    public function generateAIMessage(string $type, array $context): ?array
    {
        $apiKey = config('services.claude.api_key') ?? config('services.ai.api_key');
        
        if (empty($apiKey)) {
            return null; // Fall back to template messages
        }

        $prompts = [
            'cart_abandonment' => "Write a short, friendly push notification (title max 40 chars, body max 100 chars) for a Ugandan customer who left {product_name} (UGX {price}) in their cart. Mention Mobile Money payment is easy. Be warm, use occasional Luganda like 'Webale'.",
            'price_drop' => "Write a push notification (title max 40 chars, body max 100 chars) for a price drop alert. Product: {product_name}, was UGX {old_price}, now UGX {new_price}. Create urgency.",
            'win_back' => "Write a friendly win-back push notification (title max 40 chars, body max 100 chars) for a Ugandan electronics shopper named {user_name} who hasn't visited in 2 weeks. Mention deals available.",
        ];

        $prompt = $prompts[$type] ?? null;
        if (!$prompt) {
            return null;
        }

        // Replace placeholders
        foreach ($context as $key => $value) {
            $prompt = str_replace("{{$key}}", $value, $prompt);
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-haiku-20240307',
                'max_tokens' => 150,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt . "\n\nReturn JSON only: {\"title\": \"...\", \"body\": \"...\"}"]
                ],
            ]);

            if ($response->successful()) {
                $text = $response->json()['content'][0]['text'] ?? '';
                $parsed = json_decode($text, true);
                if ($parsed && isset($parsed['title']) && isset($parsed['body'])) {
                    return $parsed;
                }
            }
        } catch (\Exception $e) {
            Log::debug('AI message generation failed', ['error' => $e->getMessage()]);
        }

        return null; // Fall back to templates
    }

    /**
     * Get notification statistics for admin dashboard
     */
    public function getStatistics(): array
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $thisWeek = now()->startOfWeek();

        return [
            'today' => [
                'sent' => \DB::table('push_notification_logs')
                    ->where('sent_at', '>=', $today)
                    ->count(),
                'by_type' => \DB::table('push_notification_logs')
                    ->where('sent_at', '>=', $today)
                    ->groupBy('type')
                    ->selectRaw('type, count(*) as count')
                    ->pluck('count', 'type')
                    ->toArray(),
            ],
            'yesterday' => [
                'sent' => \DB::table('push_notification_logs')
                    ->whereBetween('sent_at', [$yesterday, $today])
                    ->count(),
            ],
            'this_week' => [
                'sent' => \DB::table('push_notification_logs')
                    ->where('sent_at', '>=', $thisWeek)
                    ->count(),
            ],
            'triggers_enabled' => collect(self::TRIGGERS)
                ->filter(fn($t) => $t['enabled'])
                ->keys()
                ->toArray(),
        ];
    }
}
