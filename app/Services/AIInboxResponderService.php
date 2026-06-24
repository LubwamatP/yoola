<?php

namespace App\Services;

use App\Models\Chatting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class AIInboxResponderService
{
    /**
     * AI Provider settings
     */
    private string $provider;
    private ?string $apiKey;
    
    /**
     * Auto-escalation rules
     */
    private const ESCALATION_TRIGGERS = [
        'order_value' => 2000000, // UGX 2M
        'confidence_threshold' => 0.7,
        'frustration_keywords' => ['angry', 'scam', 'terrible', 'worst', 'refund', 'report', 'lawyer', 'cheat'],
        'human_request' => ['manager', 'human', 'real person', 'speak to someone', 'customer service', 'call me'],
        'competitor_keywords' => ['jumia', 'jiji', 'kilimall', 'amazon'],
    ];

    /**
     * Quick responses for common queries (saves API calls)
     */
    private const QUICK_RESPONSES = [
        'delivery' => "🚚 We offer FREE delivery within Kampala for orders above UGX 100,000. Delivery takes 1-3 business days. For upcountry deliveries, rates vary by location. Would you like me to check delivery options for your area?",
        'payment' => "💳 We accept:\n• Mobile Money (MTN MoMo & Airtel Money)\n• Cash on Delivery\n• Bank Transfer\n\nMobile Money is fastest - you'll get instant confirmation!",
        'warranty' => "🛡️ All electronics come with manufacturer warranty:\n• TVs & Fridges: 1-2 years\n• Phones: 1 year\n• Small appliances: 6-12 months\n\nKeep your receipt for warranty claims!",
        'return' => "↩️ We have a 7-day return policy for defective items. Items must be unused and in original packaging. Contact us within 7 days of delivery to initiate a return.",
        'hello' => "👋 Hello! Welcome to Yoola - Uganda's trusted electronics store. How can I help you today?\n\n• Looking for a specific product?\n• Questions about delivery?\n• Need help with an order?",
    ];

    private bool $enabled;

    public function __construct()
    {
        $this->enabled = config('services.ai.inbox_enabled', false);
        $this->provider = config('services.ai.provider', 'gemini');
        $this->apiKey = $this->provider === 'claude' 
            ?? config('services.claude.api_key')
            : config('gemini.api_key');
    }

    /**
     * Check if AI inbox responder is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiKey);
    }

    /**
     * Process unread customer messages and auto-respond
     */
    public function processNewMessages(): array
    {
        $results = ['processed' => 0, 'responded' => 0, 'escalated' => 0, 'errors' => 0, 'skipped' => false];

        // Skip if AI inbox responder is disabled
        if (!$this->isEnabled()) {
            $results['skipped'] = true;
            $results['skip_reason'] = 'AI inbox responder disabled';
            return $results;
        }

        try {
            // Get unread messages from customers that haven't been AI-processed
            $unreadMessages = DB::table('chattings')
                ->where('seen_by_admin', 0)
                ->whereNotNull('user_id')
                ->where('admin_id', 0)
                ->where('sent_by_customer', 1)
                ->whereNull('ai_processed_at') // New column we'll add
                ->orderBy('created_at', 'asc')
                ->take(20)
                ->get();

            foreach ($unreadMessages as $message) {
                $results['processed']++;
                
                try {
                    $response = $this->generateResponse($message);
                    
                    if ($response['should_escalate']) {
                        $this->markForEscalation($message, $response['escalation_reason']);
                        $results['escalated']++;
                    } else {
                        $this->sendAutoResponse($message, $response['message']);
                        $results['responded']++;
                    }
                    
                    // Mark as AI-processed
                    DB::table('chattings')
                        ->where('id', $message->id)
                        ->update(['ai_processed_at' => now()]);
                        
                } catch (\Exception $e) {
                    Log::error('AI Inbox responder error', [
                        'message_id' => $message->id,
                        'error' => $e->getMessage()
                    ]);
                    $results['errors']++;
                }
            }
        } catch (\Exception $e) {
            Log::error('AI Inbox responder failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Generate AI response for a message
     */
    protected function generateResponse(object $message): array
    {
        $userMessage = strtolower(trim($message->message ?? ''));
        
        // Check for escalation triggers first
        $escalationCheck = $this->checkEscalationTriggers($userMessage, $message);
        if ($escalationCheck['should_escalate']) {
            return $escalationCheck;
        }

        // Check for quick responses (no API needed)
        $quickResponse = $this->checkQuickResponses($userMessage);
        if ($quickResponse) {
            return [
                'message' => $quickResponse,
                'should_escalate' => false,
                'confidence' => 1.0,
                'source' => 'quick_response',
            ];
        }

        // Get customer context
        $customerContext = $this->getCustomerContext($message->user_id);
        
        // Call AI API
        return $this->callAI($message->message, $customerContext);
    }

    /**
     * Check for quick responses without AI
     */
    protected function checkQuickResponses(string $message): ?string
    {
        // Greeting patterns
        if (preg_match('/^(hi|hello|hey|good morning|good afternoon|good evening|jambo|oli otya)/i', $message)) {
            return self::QUICK_RESPONSES['hello'];
        }

        // Delivery questions
        if (preg_match('/deliver|shipping|transport|how long|when.*arrive/i', $message)) {
            return self::QUICK_RESPONSES['delivery'];
        }

        // Payment questions
        if (preg_match('/pay|momo|mobile money|airtel|cash|payment method/i', $message)) {
            return self::QUICK_RESPONSES['payment'];
        }

        // Warranty questions
        if (preg_match('/warranty|guarantee|defect|broken|not working/i', $message)) {
            return self::QUICK_RESPONSES['warranty'];
        }

        // Return questions
        if (preg_match('/return|refund|exchange|give back/i', $message)) {
            return self::QUICK_RESPONSES['return'];
        }

        return null;
    }

    /**
     * Check if message should be escalated to human
     */
    protected function checkEscalationTriggers(string $message, object $chatMessage): array
    {
        // Check frustration keywords
        foreach (self::ESCALATION_TRIGGERS['frustration_keywords'] as $keyword) {
            if (str_contains($message, $keyword)) {
                return [
                    'should_escalate' => true,
                    'escalation_reason' => 'frustration_detected',
                    'message' => "I understand you're frustrated. Let me connect you with our customer service team who can help resolve this quickly. A human agent will respond shortly. 🙏",
                ];
            }
        }

        // Check human request
        foreach (self::ESCALATION_TRIGGERS['human_request'] as $phrase) {
            if (str_contains($message, $phrase)) {
                return [
                    'should_escalate' => true,
                    'escalation_reason' => 'human_requested',
                    'message' => "Of course! I'm connecting you with our customer service team. A human agent will respond to you shortly. Thank you for your patience! 🤝",
                ];
            }
        }

        // Check competitor mention (retention risk)
        foreach (self::ESCALATION_TRIGGERS['competitor_keywords'] as $competitor) {
            if (str_contains($message, $competitor)) {
                return [
                    'should_escalate' => true,
                    'escalation_reason' => 'competitor_mentioned',
                    'message' => "I'd love to help you find the best deal here at Yoola! Let me connect you with our sales team who can provide personalized offers. 💯",
                ];
            }
        }

        // Check for order-related issues (high priority)
        if (preg_match('/order.*(wrong|late|missing|cancel|problem)|my order|where.*order/i', $message)) {
            // Check if this is a high-value customer
            $customerOrders = DB::table('orders')
                ->where('customer_id', $chatMessage->user_id)
                ->sum('order_amount');
            
            if ($customerOrders > self::ESCALATION_TRIGGERS['order_value']) {
                return [
                    'should_escalate' => true,
                    'escalation_reason' => 'high_value_customer_issue',
                    'message' => "I see you're a valued customer and have a concern about your order. Let me connect you with our priority support team right away! 🌟",
                ];
            }
        }

        return ['should_escalate' => false];
    }

    /**
     * Get customer context for personalized responses
     */
    protected function getCustomerContext(int $userId): array
    {
        $customer = DB::table('users')->where('id', $userId)->first();
        
        $recentOrders = DB::table('orders')
            ->where('customer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $wishlistItems = DB::table('wishlists')
            ->join('products', 'products.id', '=', 'wishlists.product_id')
            ->where('wishlists.customer_id', $userId)
            ->select('products.name', 'products.unit_price')
            ->take(5)
            ->get();

        return [
            'customer_name' => $customer->f_name ?? 'Customer',
            'is_returning' => $recentOrders->count() > 0,
            'recent_orders' => $recentOrders,
            'wishlist' => $wishlistItems,
            'total_spent' => $recentOrders->sum('order_amount'),
        ];
    }

    /**
     * Call AI API for complex queries
     */
    protected function callAI(string $message, array $context): array
    {
        $systemPrompt = $this->buildSystemPrompt($context);

        try {
            if ($this->provider === 'gemini') {
                return $this->callGemini($systemPrompt, $message);
            } else {
                return $this->callClaude($systemPrompt, $message);
            }
        } catch (\Exception $e) {
            Log::error('AI API call failed', ['error' => $e->getMessage()]);
            return [
                'message' => "Thank you for your message! Our team will get back to you shortly. For urgent inquiries, call us at 0200 942214 or WhatsApp us. 📞",
                'should_escalate' => true,
                'escalation_reason' => 'ai_error',
                'confidence' => 0,
            ];
        }
    }

    /**
     * Build system prompt for AI
     */
    protected function buildSystemPrompt(array $context): string
    {
        $customerName = $context['customer_name'];
        $isReturning = $context['is_returning'] ? 'returning customer' : 'new customer';

        return <<<PROMPT
You are a helpful customer service assistant for Yoola (yoola.ug), Uganda's trusted electronics store.

ABOUT YOOLA:
- Location: Burton St, Aponye City Mall, Kampala
- Phone: 0200 942214
- Products: TVs, fridges, washing machines, laptops, phones, home appliances
- Free Kampala delivery on orders above UGX 100,000
- Payment: Mobile Money (MTN MoMo, Airtel Money), Cash on Delivery
- 7-day return policy for defective items
- All electronics include manufacturer warranty

CUSTOMER CONTEXT:
- Name: {$customerName}
- Status: {$isReturning}

RESPONSE GUIDELINES:
1. Be friendly, helpful, and professional
2. Use emojis sparingly to be warm (1-2 per message)
3. Keep responses concise (2-3 short paragraphs max)
4. If asked about a specific product, offer to check availability
5. For complex issues (returns, complaints, order problems), collect details then escalate
6. Occasionally use Luganda phrases like "Webale" (thank you) for rapport
7. Always end with a question or call-to-action
8. If unsure, offer to connect with human support

NEVER:
- Make up product information or prices
- Promise specific delivery times without checking
- Process refunds or order changes (escalate these)
- Share other customer information
PROMPT;
    }

    /**
     * Call Gemini API
     */
    protected function callGemini(string $systemPrompt, string $message): array
    {
        $response = Http::timeout(10)->post(config('gemini.endpoint'), [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nCustomer message: " . $message]
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => 300,
                'temperature' => 0.7,
            ],
        ]);

        if ($response->successful()) {
            $text = $response->json('candidates.0.content.parts.0.text') ?? '';
            return [
                'message' => $text,
                'should_escalate' => false,
                'confidence' => 0.85,
                'source' => 'gemini',
            ];
        }

        throw new \Exception('Gemini API error: ' . $response->body());
    }

    /**
     * Call Claude API
     */
    protected function callClaude(string $systemPrompt, string $message): array
    {
        $response = Http::timeout(10)
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-haiku-20240307',
                'max_tokens' => 300,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $message]
                ],
            ]);

        if ($response->successful()) {
            $text = $response->json('content.0.text') ?? '';
            return [
                'message' => $text,
                'should_escalate' => false,
                'confidence' => 0.9,
                'source' => 'claude',
            ];
        }

        throw new \Exception('Claude API error: ' . $response->body());
    }

    /**
     * Send auto-response to customer
     */
    protected function sendAutoResponse(object $originalMessage, string $responseText): void
    {
        DB::table('chattings')->insert([
            'user_id' => $originalMessage->user_id,
            'admin_id' => 0,
            'seller_id' => null,
            'message' => $responseText,
            'sent_by_customer' => 0,
            'sent_by_seller' => 0,
            'seen_by_customer' => 0,
            'seen_by_admin' => 1,
            'notification_receiver' => 'customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('AI auto-response sent', [
            'user_id' => $originalMessage->user_id,
            'original_message' => substr($originalMessage->message, 0, 100),
        ]);
    }

    /**
     * Mark message for human escalation
     */
    protected function markForEscalation(object $message, string $reason): void
    {
        // Get customer info
        $customer = DB::table('users')->where('id', $message->user_id)->first();
        $customerName = $customer ? ($customer->f_name . ' ' . $customer->l_name) : 'Customer #' . $message->user_id;
        $customerPhone = $customer ? ($customer->country_code . $customer->phone) : 'N/A';

        // First send the escalation message to customer
        DB::table('chattings')->insert([
            'user_id' => $message->user_id,
            'admin_id' => 0,
            'message' => $this->getEscalationMessage($reason),
            'sent_by_customer' => 0,
            'seen_by_customer' => 0,
            'seen_by_admin' => 0, // Keep unread for admin
            'notification_receiver' => 'customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log escalation for admin dashboard
        DB::table('ai_escalations')->insert([
            'chatting_id' => $message->id,
            'user_id' => $message->user_id,
            'reason' => $reason,
            'original_message' => $message->message,
            'created_at' => now(),
        ]);

        // Send email notification to admin
        $this->sendEscalationEmail($customerName, $customerPhone, $message->message, $reason);

        Log::info('Chat escalated to human', [
            'user_id' => $message->user_id,
            'reason' => $reason,
        ]);
    }

    /**
     * Send escalation email to admin
     */
    protected function sendEscalationEmail(string $customerName, string $customerPhone, string $originalMessage, string $reason): void
    {
        try {
            $adminEmail = getWebConfig('company_email') ?? config('mail.from.address');
            
            if (!$adminEmail) {
                Log::warning('No admin email configured for escalations');
                return;
            }

            $reasonLabels = [
                'frustration_detected' => '😤 Customer Frustration Detected',
                'human_requested' => '🙋 Human Agent Requested',
                'competitor_mentioned' => '⚠️ Competitor Mentioned (Retention Risk)',
                'high_value_customer_issue' => '🌟 High-Value Customer Issue',
                'ai_error' => '🤖 AI Processing Error',
            ];

            $subject = "[URGENT] Yoola Chat Escalation: " . ($reasonLabels[$reason] ?? 'Needs Attention');
            
            $body = "
🚨 CHAT ESCALATION ALERT

Customer: {$customerName}
Phone: {$customerPhone}
Reason: " . ($reasonLabels[$reason] ?? $reason) . "

Original Message:
\"{$originalMessage}\"

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Action Required: Please respond to this customer in the admin inbox.
Admin Panel: " . url('/admin/messages/customer') . "
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

This is an automated message from Yoola AI Support.
            ";

            Mail::raw($body, function ($mail) use ($adminEmail, $subject) {
                $mail->to($adminEmail)
                     ->subject($subject);
            });

            Log::info('Escalation email sent', ['to' => $adminEmail, 'reason' => $reason]);

        } catch (\Exception $e) {
            Log::error('Failed to send escalation email', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get escalation message based on reason
     */
    protected function getEscalationMessage(string $reason): string
    {
        return match ($reason) {
            'frustration_detected' => "I understand this is frustrating. I'm connecting you with our customer service team who can help resolve this quickly. A human agent will respond shortly. 🙏",
            'human_requested' => "Of course! I'm connecting you with our customer service team. A human agent will respond to you shortly. Thank you for your patience! 🤝",
            'competitor_mentioned' => "I'd love to help you find the best deal here at Yoola! Let me connect you with our sales team who can provide personalized offers. 💯",
            'high_value_customer_issue' => "I see you're a valued customer. Let me connect you with our priority support team right away! 🌟",
            default => "I'm connecting you with our customer service team for personalized assistance. Someone will respond shortly! 🙋",
        };
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics(): array
    {
        $today = now()->startOfDay();

        return [
            'messages_today' => DB::table('chattings')
                ->where('created_at', '>=', $today)
                ->whereNotNull('ai_processed_at')
                ->count(),
            'auto_responded' => DB::table('chattings')
                ->where('created_at', '>=', $today)
                ->where('sent_by_customer', 0)
                ->where('admin_id', 0)
                ->whereNotNull('user_id')
                ->count(),
            'escalated' => DB::table('ai_escalations')
                ->where('created_at', '>=', $today)
                ->count(),
            'pending_human' => DB::table('chattings')
                ->where('seen_by_admin', 0)
                ->where('sent_by_customer', 1)
                ->count(),
        ];
    }
}
