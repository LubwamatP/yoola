<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Programmatic SEO - Price Pages
 * 
 * Creates SEO-optimized pages for "[Product] price in Uganda" searches
 * Each page dynamically pulls products and displays current prices
 */
class PricePage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'meta_description',
        'h1',
        'intro_text',
        'buying_guide',
        'faqs',
        'related_slugs',
        'category_id',
        'brand_id',
        'product_type',
        'size_filter',
        'feature_filter',
        'brand_filter',
        'min_price',
        'max_price',
        'hero_image',
        'is_active',
        'is_indexed',
        'priority',
    ];

    protected $casts = [
        'faqs' => 'array',
        'related_slugs' => 'array',
        'is_active' => 'boolean',
        'is_indexed' => 'boolean',
    ];

    /**
     * Get the category for this price page
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand for this price page
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get products matching this price page's filters
     */
    public function getProducts()
    {
        $query = Product::where('status', 1)
            ->where('current_stock', '>', 0);

        // Filter by category
        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        // Filter by brand (by ID)
        if ($this->brand_id) {
            $query->where('brand_id', $this->brand_id);
        }

        // Filter by brand (by name match in product name)
        if ($this->brand_filter) {
            $query->where('name', 'LIKE', '%' . $this->brand_filter . '%');
        }

        // Filter by product type (search in name)
        if ($this->product_type) {
            $query->where('name', 'LIKE', '%' . $this->product_type . '%');
        }

        // Filter by size (search in name)
        if ($this->size_filter) {
            $query->where('name', 'LIKE', '%' . $this->size_filter . '%');
        }

        // Filter by feature (search in name or details)
        if ($this->feature_filter) {
            $query->where(function ($q) {
                $q->where('name', 'LIKE', '%' . $this->feature_filter . '%')
                  ->orWhere('details', 'LIKE', '%' . $this->feature_filter . '%');
            });
        }

        return $query->orderBy('unit_price', 'asc')->get();
    }

    /**
     * Get the price range for display
     */
    public function getPriceRangeAttribute(): array
    {
        $products = $this->getProducts();
        
        if ($products->isEmpty()) {
            return [
                'min' => $this->min_price ?? 0,
                'max' => $this->max_price ?? 0,
            ];
        }

        return [
            'min' => $products->min('unit_price'),
            'max' => $products->max('unit_price'),
        ];
    }

    /**
     * Get related price pages
     */
    public function getRelatedPages()
    {
        if (empty($this->related_slugs)) {
            return collect();
        }

        return self::whereIn('slug', $this->related_slugs)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get the canonical URL
     */
    public function getUrlAttribute(): string
    {
        return url('/prices/' . $this->slug);
    }

    /**
     * Scope for active pages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for indexed pages (for sitemap)
     */
    public function scopeIndexed($query)
    {
        return $query->where('is_indexed', true)->where('is_active', true);
    }

    /**
     * Generate FAQ Schema markup
     */
    public function getFaqSchemaAttribute(): array
    {
        if (empty($this->faqs)) {
            return [];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect($this->faqs)->map(function ($faq) {
                return [
                    '@type' => 'Question',
                    'name' => html_entity_decode($faq['question'] ?? '', ENT_QUOTES, 'UTF-8'),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => html_entity_decode($faq['answer'] ?? '', ENT_QUOTES, 'UTF-8'),
                    ],
                ];
            })->toArray(),
        ];
    }
}
