<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AIChatService
{
    /**
     * AI Provider: claude (Claude Haiku) or gemini (Gemini Flash)
     */
    private string $provider;
    private ?string $apiKey;
    private const MAX_TOKENS = 500;
    private const CACHE_TTL = 86400; // 24 hours

    /**
     * Auto-escalation thresholds
     */
    private const ESCALATION_RULES = [
        'order_value_threshold' => 2000000, // UGX 2M
        'confidence_threshold' => 0.70,
        'frustration_keywords' => ['frustrated', 'angry', 'scam', 'terrible', 'worst', 'refund', 'report', 'lawyer'],
        'escalation_keywords' => ['manager', 'human', 'real person', 'speak to someone', 'customer service'],
        'competitor_keywords' => ['jumia', 'jiji', 'kilimall', 'amazon'],
    ];

    private bool $enabled;

    public function __construct()
    {
        $this->enabled = config('services.ai.chat_enabled', false);
        $this->provider = config('services.ai.provider', 'claude');
        $this->apiKey = config('services.ai.api_key') ?? config('services.claude.api_key');
    }

    /**
     * Check if AI chat is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiKey);
    }

    /**
     * Process incoming chat message and generate AI response
     */
    public function processMessage(int $conversationId, string $userMessage, array $context = []): array
    {
        // Return fallback if AI is disabled
        if (!$this->isEnabled()) {
            return [
                'response' => "Thank you for your message! Our team will respond shortly. For urgent inquiries, call us at 0200 942214 or WhatsApp 0780221421.",
                'from_cache' => false,
                'should_escalate' => true,
                'escalation_reason' => 'ai_disabled',
                'confidence' => 1.0,
            ];
        }

        // Check for cached response (exact match)
        $cacheKey = 'ai_response_' . md5(strtolower(trim($userMessage)));
        $cachedResponse = Cache::get($cacheKey);
        
        if ($cachedResponse && !$this->requiresFreshData($userMessage)) {
            return [
                'response' => $cachedResponse,
                'from_cache' => true,
                'should_escalate' => false,
                'confidence' => 1.0,
            ];
        }

        // Check if message should be auto-escalated
        $escalationCheck = $this->checkForEscalation($userMessage, $context);
        if ($escalationCheck['should_escalate']) {
            $this->markConversationEscalated($conversationId, $escalationCheck['reason']);
            return [
                'response' => $this->getEscalationResponse($escalationCheck['reason']),
                'from_cache' => false,
                'should_escalate' => true,
                'escalation_reason' => $escalationCheck['reason'],
                'confidence' => 1.0,
            ];
        }

        // Build context for AI
        $systemPrompt = $this->buildSystemPrompt($context);
        $productContext = $this->getProductContext($userMessage, $context);

        // Generate AI response
        $aiResponse = $this->callAI($systemPrompt, $userMessage, $productContext);

        // Cache successful responses for common questions
        if ($aiResponse['success'] && $aiResponse['confidence'] > 0.8) {
            Cache::put($cacheKey, $aiResponse['response'], self::CACHE_TTL);
        }

        // Log the interaction
        $this->logMessage($conversationId, 'ai', $aiResponse['response'], [
            'confidence' => $aiResponse['confidence'],
            'product_context' => $productContext,
        ]);

        return [
            'response' => $aiResponse['response'],
            'from_cache' => false,
            'should_escalate' => $aiResponse['confidence'] < self::ESCALATION_RULES['confidence_threshold'],
            'confidence' => $aiResponse['confidence'],
            'suggested_products' => $aiResponse['suggested_products'] ?? [],
        ];
    }

    /**
     * Build system prompt with Yoola context
     */
    private function buildSystemPrompt(array $context): string
    {
        $sellerInfo = '';
        if (!empty($context['seller_id'])) {
            $seller = Seller::find($context['seller_id']);
            if ($seller) {
                $sellerInfo = "Current seller: {$seller->shop->name}. ";
                if ($seller->shop->delivery_info) {
                    $sellerInfo .= "Delivery info: {$seller->shop->delivery_info}. ";
                }
            }
        }

        $additionalContext = $context['additional_context'] ?? 'General inquiry';

        return <<<PROMPT
You are Yoola Assistant, the helpful AI for Yoola.ug - Uganda's trusted multi-vendor electronics marketplace.

PERSONALITY:
- Friendly, helpful, and confident
- Use simple, clear English (our customers are Ugandan)
- Occasionally use Luganda phrases like "Webale!" (thank you) or "Gyebale ko!" (good job/well done)
- Be enthusiastic about helping customers find the right electronics

YOOLA FACTS:
- We are a MULTI-VENDOR marketplace - multiple verified sellers compete on price
- FREE delivery in Kampala on orders above UGX 100,000
- Payment methods: MTN Mobile Money, Airtel Money, Cash on Delivery
- 7-day return policy on all products
- All electronics come with warranty
- Physical location: Burton St, Kampala (Aponye City Mall)
- WhatsApp: +256780221421
{$sellerInfo}

WHAT YOU CAN DO:
1. Answer product questions (specs, availability, price)
2. Explain delivery options and costs
3. Guide customers through payment (especially Mobile Money)
4. Compare products from different sellers
5. Recommend alternatives if something is out of stock
6. Address trust concerns (we're verified, warranty included, etc.)

WHAT YOU CANNOT DO:
1. Process refunds or returns (escalate to admin)
2. Change order details (escalate to admin)
3. Negotiate prices beyond seller's set discounts
4. Make promises about delivery times without checking

RESPONSE STYLE:
- Keep responses SHORT (2-3 sentences max)
- Include relevant product links when helpful
- Always include a helpful next step or question
- If unsure, offer to connect them with a human

CURRENT CONTEXT:
{$additionalContext}
PROMPT;
    }

    /**
     * Get product context from the message
     */
    private function getProductContext(string $message, array $context): array
    {
        $productContext = [];

        // If there's a specific product in context
        if (!empty($context['product_id'])) {
            $product = Product::with(['seller', 'category'])->find($context['product_id']);
            if ($product) {
                $productContext['current_product'] = [
                    'name' => $product->name,
                    'price' => 'UGX ' . number_format($product->unit_price),
                    'stock' => $product->current_stock > 0 ? 'In Stock' : 'Out of Stock',
                    'seller' => $product->seller?->shop?->name ?? 'Yoola Direct',
                    'category' => $product->category?->name,
                    'warranty' => $product->warranty ?? 'Standard warranty included',
                ];
            }
        }

        // Search for products mentioned in message
        $searchTerms = $this->extractProductTerms($message);
        if (!empty($searchTerms)) {
            $products = Product::active()
                ->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q->orWhere('name', 'like', "%{$term}%");
                    }
                })
                ->take(3)
                ->get();

            $productContext['mentioned_products'] = $products->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => 'UGX ' . number_format($p->unit_price),
                    'stock' => $p->current_stock > 0 ? 'In Stock' : 'Out of Stock',
                    'url' => '/product/' . $p->slug,
                ];
            })->toArray();
        }

        return $productContext;
    }

    /**
     * Extract product search terms from message
     */
    private function extractProductTerms(string $message): array
    {
        // Common product keywords to look for
        $brands = ['samsung', 'iphone', 'apple', 'xiaomi', 'infinix', 'tecno', 'oppo', 'vivo', 'nokia', 'huawei', 'hp', 'dell', 'lenovo', 'asus', 'acer', 'lg', 'sony', 'hisense', 'tcl', 'skyworth'];
        $categories = ['phone', 'laptop', 'tv', 'television', 'fridge', 'refrigerator', 'speaker', 'headphone', 'earphone', 'charger', 'case', 'screen protector', 'power bank', 'tablet', 'smart watch', 'camera'];

        $message = strtolower($message);
        $terms = [];

        foreach (array_merge($brands, $categories) as $keyword) {
            if (strpos($message, $keyword) !== false) {
                $terms[] = $keyword;
            }
        }

        return array_unique($terms);
    }

    /**
     * Call AI API (Claude or Gemini)
     */
    private function callAI(string $systemPrompt, string $userMessage, array $productContext): array
    {
        $contextString = !empty($productContext) ?? "\n\nPRODUCT CONTEXT:\n". json_encode($productContext, JSON_PRETTY_PRINT)
            : '';

        try {
            if ($this->provider === 'claude') {
                return $this->callClaude($systemPrompt . $contextString, $userMessage);
            } else {
                return $this->callGemini($systemPrompt . $contextString, $userMessage);
            }
        } catch (\Exception $e) {
            Log::error('AI API Error', ['error' => $e->getMessage()]);
            
            // Fallback response
            return [
                'success' => false,
                'response' => "I'm having a small technical issue. Let me connect you with our team who can help right away. You can also WhatsApp us at +256780221421! 📱",
                'confidence' => 0.5,
            ];
        }
    }

    /**
     * Call Claude Haiku API
     */
    private function callClaude(string $systemPrompt, string $userMessage): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => self::MAX_TOKENS,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $userMessage]
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $text = $data['content'][0]['text'] ?? '';
            
            return [
                'success' => true,
                'response' => $text,
                'confidence' => 0.9, // Claude is generally confident
                'tokens_used' => $data['usage']['output_tokens'] ?? 0,
            ];
        }

        throw new \Exception('Claude API failed: ' . $response->body());
    }

    /**
     * Call Gemini Flash API (free tier)
     */
    private function callGemini(string $systemPrompt, string $userMessage): array
    {
        $apiKey = config('services.gemini.api_key');
        
        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nUser: " . $userMessage]
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => self::MAX_TOKENS,
                'temperature' => 0.7,
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            return [
                'success' => true,
                'response' => $text,
                'confidence' => 0.85,
            ];
        }

        throw new \Exception('Gemini API failed: ' . $response->body());
    }

    /**
     * Check if message should trigger escalation
     */
    private function checkForEscalation(string $message, array $context): array
    {
        $message = strtolower($message);

        // Check for explicit escalation requests
        foreach (self::ESCALATION_RULES['escalation_keywords'] as $keyword) {
            if (strpos($message, $keyword) !== false) {
                return ['should_escalate' => true, 'reason' => 'customer_requested'];
            }
        }

        // Check for frustration indicators
        foreach (self::ESCALATION_RULES['frustration_keywords'] as $keyword) {
            if (strpos($message, $keyword) !== false) {
                return ['should_escalate' => true, 'reason' => 'customer_frustrated'];
            }
        }

        // Check for competitor mentions
        foreach (self::ESCALATION_RULES['competitor_keywords'] as $keyword) {
            if (strpos($message, $keyword) !== false) {
                return ['should_escalate' => true, 'reason' => 'competitor_mentioned'];
            }
        }

        // Check order value threshold
        if (!empty($context['order_value']) && $context['order_value'] >= self::ESCALATION_RULES['order_value_threshold']) {
            return ['should_escalate' => true, 'reason' => 'high_value_order'];
        }

        // Check for refund/dispute mentions
        if (preg_match('/\b(refund|return|broken|defective|not working|fake|dispute)\b/', $message)) {
            return ['should_escalate' => true, 'reason' => 'potential_dispute'];
        }

        // Check for bulk/wholesale inquiries
        if (preg_match('/\b(bulk|wholesale|reseller|many units|large order)\b/', $message)) {
            return ['should_escalate' => true, 'reason' => 'bulk_inquiry'];
        }

        return ['should_escalate' => false, 'reason' => null];
    }

    /**
     * Get appropriate response for escalation
     */
    private function getEscalationResponse(string $reason): string
    {
        $responses = [
            'customer_requested' => "Of course! I'm connecting you with our team right now. Someone will respond within a few minutes. You can also reach us instantly on WhatsApp: +256780221421 📱",
            'customer_frustrated' => "I'm really sorry you're having trouble. Let me get our customer service team to help you personally - they'll take great care of you. Connecting now... 🙏",
            'competitor_mentioned' => "I understand you're comparing options - that's smart shopping! Let me connect you with our team who can give you the best deal. One moment please...",
            'high_value_order' => "For orders of this value, our senior team will personally assist you to ensure everything goes perfectly. Connecting you now... ⭐",
            'potential_dispute' => "I want to make sure this is resolved properly for you. I'm connecting you with our customer service team who can help immediately. 🤝",
            'bulk_inquiry' => "Great news - we do offer special pricing for bulk orders! Let me connect you with our sales team who handles wholesale inquiries. One moment...",
        ];

        return $responses[$reason] ?? $responses['customer_requested'];
    }

    /**
     * Mark conversation as escalated
     */
    private function markConversationEscalated(int $conversationId, string $reason): void
    {
        DB::table('ai_chat_conversations')
            ->where('id', $conversationId)
            ->update([
                'status' => 'escalated',
                'escalation_reason' => $reason,
                'updated_at' => now(),
            ]);
    }

    /**
     * Admin takes over a conversation
     */
    public function adminTakeover(int $conversationId, int $adminId): bool
    {
        return DB::table('ai_chat_conversations')
            ->where('id', $conversationId)
            ->update([
                'status' => 'admin_active',
                'taken_over_by' => $adminId,
                'taken_over_at' => now(),
                'updated_at' => now(),
            ]) > 0;
    }

    /**
     * Hand conversation back to AI
     */
    public function handBackToAI(int $conversationId): bool
    {
        return DB::table('ai_chat_conversations')
            ->where('id', $conversationId)
            ->update([
                'status' => 'ai_active',
                'taken_over_by' => null,
                'taken_over_at' => null,
                'updated_at' => now(),
            ]) > 0;
    }

    /**
     * Log a chat message
     */
    private function logMessage(int $conversationId, string $senderType, string $message, array $context = []): void
    {
        DB::table('ai_chat_messages')->insert([
            'conversation_id' => $conversationId,
            'sender_type' => $senderType,
            'message' => $message,
            'ai_context' => json_encode($context),
            'ai_confidence' => $context['confidence'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Check if message requires fresh data (not cacheable)
     */
    private function requiresFreshData(string $message): bool
    {
        $freshDataKeywords = ['stock', 'available', 'in stock', 'price', 'cost', 'how much', 'delivery today', 'order status'];
        $message = strtolower($message);
        
        foreach ($freshDataKeywords as $keyword) {
            if (strpos($message, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get conversation statistics for admin dashboard
     */
    public function getStatistics(): array
    {
        $today = now()->startOfDay();

        return [
            'total_today' => DB::table('ai_chat_conversations')
                ->where('created_at', '>=', $today)
                ->count(),
            'ai_resolved' => DB::table('ai_chat_conversations')
                ->where('created_at', '>=', $today)
                ->where('status', 'resolved')
                ->whereNull('taken_over_by')
                ->count(),
            'escalated' => DB::table('ai_chat_conversations')
                ->where('created_at', '>=', $today)
                ->whereIn('status', ['escalated', 'admin_active'])
                ->count(),
            'avg_messages' => DB::table('ai_chat_messages')
                ->where('created_at', '>=', $today)
                ->count() / max(1, DB::table('ai_chat_conversations')->where('created_at', '>=', $today)->count()),
            'by_status' => DB::table('ai_chat_conversations')
                ->where('created_at', '>=', $today)
                ->groupBy('status')
                ->selectRaw('status, count(*) as count')
                ->pluck('count', 'status')
                ->toArray(),
        ];
    }

    /**
     * Get active conversations for admin monitoring
     */
    public function getActiveConversations(): array
    {
        return DB::table('ai_chat_conversations')
            ->whereIn('ai_chat_conversations.status', ['ai_active', 'escalated'])
            ->where('ai_chat_conversations.updated_at', '>=', now()->subHours(24))
            ->leftJoin('users', 'users.id', '=', 'ai_chat_conversations.user_id')
            ->select([
                'ai_chat_conversations.*',
                'users.f_name as user_name',
                'users.phone as user_phone',
            ])
            ->orderByRaw("FIELD(ai_chat_conversations.status, 'escalated', 'ai_active')")
            ->orderBy('ai_chat_conversations.updated_at', 'desc')
            ->get()
            ->toArray();
    }
}
