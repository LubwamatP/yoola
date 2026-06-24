<?php

namespace Modules\AI\app\PromptTemplates;

use Modules\AI\app\Contracts\PromptTemplateInterface;

class SearchIntentTemplate implements PromptTemplateInterface
{
    protected string $templateType = 'search_intent';

    protected string $systemContext = <<<CONTEXT
You are a search specialist for Yoola.ug, Uganda's premier electronics and appliances e-commerce store.
Your role is to understand customer search queries and extract structured intent for product search.

IMPORTANT CONTEXT:
- Currency: Uganda Shillings (UGX)
- "k" or "K" means thousand (e.g., 500k = 500,000 UGX)
- "M" means million (e.g., 2M = 2,000,000 UGX)
- Common local brands: Hisense, Changhong, Ramtons, Nunix, ADH, Venus, Vitron, Nobel, Polystar
- DC/Solar products are popular (12V, 24V appliances for solar power)
- Multi-language support: English, Luganda, Swahili

PRODUCT CATEGORIES:
- TVs: digital tv, smart tv, dc tv, battery tv, solar tv, led tv, oled tv
- Refrigeration: fridge, refrigerator, freezer, dc fridge, solar fridge, deep freezer
- Cooking: cooker, gas cooker, electric cooker, microwave, air fryer, rice cooker
- Laundry: washing machine, twin tub, front loader, iron, dryer
- Cooling: air conditioner, fan, water dispenser
- Audio: speaker, bluetooth speaker, soundbar, home theater, headphones
- Power: inverter, solar panel, solar battery, generator, stabilizer, power bank
- Small Appliances: blender, kettle, toaster, coffee maker, vacuum cleaner
- Electronics: phone, smartphone, laptop, tablet, camera
CONTEXT;

    /**
     * Get the template type
     */
    public function getType(): string
    {
        return $this->templateType;
    }

    /**
     * Build the intent extraction prompt
     */
    public function build(?string $context = null, ?string $langCode = null, ?string $description = null, ?array $options = null): string
    {
        // Handle both string and array context
        $contextData = is_string($context) ? ['query' => $context] : ($options['context'] ?? []);
        $query = $contextData['query'] ?? $context ?? '';
        $conversationContext = $contextData['conversation_context'] ?? null;

        $contextSection = '';
        if ($conversationContext) {
            $contextSection = "\n\nPREVIOUS SEARCH CONTEXT:\n" . json_encode($conversationContext, JSON_PRETTY_PRINT);
        }

        $prompt = <<<PROMPT
{$this->systemContext}
{$contextSection}

CURRENT SEARCH QUERY: "{$query}"

TASK: Analyze this query and extract the customer's search intent.

RESPOND WITH VALID JSON ONLY (no markdown, no explanation):
{
  "intent": "find_product" | "compare" | "budget_search" | "brand_search" | "use_case_search" | "specification_search",
  "product_types": ["category1", "category2"],
  "budget_ugx": {
    "min": null,
    "max": null
  },
  "budget_mentioned": true | false,
  "primary_features": ["feature1", "feature2"],
  "use_case": "gaming" | "photography" | "productivity" | "entertainment" | "home_use" | "office" | "outdoor" | "business" | null,
  "brands": ["brand1", "brand2"],
  "specifications": {
    "size": "32 inch" | "43 inch" | "55 inch" | null,
    "capacity": "200L" | "300L" | null,
    "power": "12V" | "24V" | "AC" | null,
    "color": null
  },
  "comparison_requested": false,
  "entities": ["searchable", "terms", "from", "query"],
  "confidence": 0.85,
  "did_you_mean": null,
  "search_summary": "Customer wants X product with Y features in Z budget range"
}

RULES:
1. Extract budget from phrases like "under 500k", "below 2M", "around 1.5 million"
2. Recognize Ugandan brand names and their misspellings
3. Identify DC/solar products when mentioned
4. Map informal terms to product categories (e.g., "fridge" → "refrigerator")
5. Handle typos gracefully (e.g., "samsong" → "samsung")
6. Set confidence based on query clarity (0.0-1.0)
7. If confidence < 0.6, suggest "did_you_mean" alternatives
8. Keep entities concise and searchable
PROMPT;

        return $prompt;
    }

    /**
     * Build a prompt for generating product explanations
     */
    public function buildExplanationPrompt(array $product, array $intent): string
    {
        $productInfo = json_encode([
            'name' => $product['name'] ?? '',
            'price' => $product['unit_price'] ?? 0,
            'brand' => $product['brand']['name'] ?? 'Unknown',
            'category' => $product['category']['name'] ?? 'Unknown',
        ], JSON_PRETTY_PRINT);

        $intentInfo = json_encode([
            'intent' => $intent['intent'] ?? '',
            'use_case' => $intent['use_case'] ?? '',
            'budget' => $intent['budget_ugx'] ?? [],
            'features' => $intent['primary_features'] ?? [],
        ], JSON_PRETTY_PRINT);

        return <<<PROMPT
You are a helpful shopping assistant for Yoola.ug.

Product Information:
{$productInfo}

Customer's Search Intent:
{$intentInfo}

Generate a brief (1-2 sentences) explanation of why this product matches the customer's search.
Focus on:
- How it meets their use case
- Value for money relative to budget
- Key features that match their needs

Respond with just the explanation text, no JSON or formatting.
PROMPT;
    }

    /**
     * Build a prompt for suggesting related searches
     */
    public function buildSuggestionPrompt(array $intent, array $facets): string
    {
        $intentInfo = json_encode($intent, JSON_PRETTY_PRINT);
        $facetInfo = json_encode($facets, JSON_PRETTY_PRINT);

        return <<<PROMPT
{$this->systemContext}

Based on this customer's search intent:
{$intentInfo}

And these available product facets:
{$facetInfo}

Generate 3-5 related search suggestions that might help the customer find what they need.

Respond with a JSON array of strings:
["suggestion 1", "suggestion 2", "suggestion 3"]

Make suggestions specific and actionable (e.g., "Hisense 43 inch smart TV" not "TVs").
PROMPT;
    }

    /**
     * Build prompt for handling follow-up queries
     */
    public function buildFollowUpPrompt(string $followUpQuery, array $previousContext): string
    {
        $contextInfo = json_encode($previousContext, JSON_PRETTY_PRINT);

        return <<<PROMPT
{$this->systemContext}

PREVIOUS SEARCH CONTEXT:
{$contextInfo}

FOLLOW-UP QUERY: "{$followUpQuery}"

The customer is refining their previous search. Interpret this follow-up in context of their previous search.

Common follow-up patterns:
- "What about [brand]?" → Filter by brand
- "Show me cheaper options" → Reduce budget
- "In [color]" → Add color filter
- "With [feature]" → Add feature requirement
- "Compare these" → Comparison mode

RESPOND WITH VALID JSON ONLY:
{
  "action": "refine" | "new_search" | "compare" | "clarify",
  "modifications": {
    "add_brands": [],
    "remove_brands": [],
    "budget_adjustment": null,
    "add_features": [],
    "add_specifications": {}
  },
  "merged_intent": {
    // Full updated intent object combining previous + new
  },
  "confidence": 0.85
}
PROMPT;
    }
}
