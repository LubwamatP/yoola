{{-- 
    Product Schema Markup for SEO (JSON-LD)
    - Handles discounted prices automatically
    - Works for all products including new ones
    - Includes breadcrumbs for better SERP display
--}}
@if(isset($product) && $product)
@php
    // Calculate the base unit price (considering variations)
    $basePrice = $product->unit_price;
    
    // Check for variations and use first variation price if exists
    if (!empty($product->variation)) {
        $variations = is_string($product->variation) ? json_decode($product->variation, true) : $product->variation;
        if (is_array($variations) && count($variations) > 0 && isset($variations[0]['price'])) {
            $basePrice = $variations[0]['price'];
        }
    }
    
    // Check for digital variations
    if (isset($product->digitalVariation) && $product->digitalVariation && count($product->digitalVariation) > 0) {
        $digitalVariations = $product->digitalVariation->toArray();
        if (isset($digitalVariations[0]['price'])) {
            $basePrice = $digitalVariations[0]['price'];
        }
    }
    
    // Calculate discount amount
    $discountAmount = 0;
    $hasDiscount = false;
    
    // Check for clearance sale first
    if (isset($product->clearanceSale) && $product->clearanceSale) {
        $hasDiscount = true;
        // Use clearance sale discount calculation
        if ($product->clearanceSale->discount_type == 'percentage') {
            $discountAmount = ($basePrice * $product->clearanceSale->discount_amount) / 100;
        } else {
            $discountAmount = $product->clearanceSale->discount_amount ?? 0;
        }
    }
    // Otherwise check regular product discount
    elseif (isset($product->discount) && $product->discount > 0) {
        $hasDiscount = true;
        if ($product->discount_type == 'percent') {
            $discountAmount = ($basePrice * $product->discount) / 100;
        } else {
            $discountAmount = $product->discount;
        }
    }
    
    // Final price after discount
    $finalPrice = max(0, $basePrice - $discountAmount);
    
    // Get product images
    $images = [];
    if (isset($product->thumbnail_full_url['path']) && $product->thumbnail_full_url['path']) {
        $images[] = $product->thumbnail_full_url['path'];
    }
    if (isset($product->images_full_url) && is_iterable($product->images_full_url)) {
        foreach ($product->images_full_url as $img) {
            if (isset($img['path']) && $img['path']) {
                $images[] = $img['path'];
            } elseif (is_string($img) && $img) {
                $images[] = $img;
            }
        }
    }
    if (empty($images)) {
        $images[] = url('/storage/app/public/product/placeholder.png');
    }
    
    // Get brand name safely
    $brandName = 'Yoola';
    if (isset($product->brand) && $product->brand && isset($product->brand->name)) {
        $brandName = $product->brand->name;
    }
    
    // Get category name safely
    $categoryName = 'Electronics';
    if (isset($product->category) && $product->category && isset($product->category->name)) {
        $categoryName = $product->category->name;
    }
    
    // Build aggregate rating if reviews exist
    $aggregateRating = null;
    if (isset($product->reviews_count) && $product->reviews_count > 0) {
        $aggregateRating = [
            '@type' => 'AggregateRating',
            'ratingValue' => number_format($product->average_rating ?? 0, 1),
            'reviewCount' => (int) $product->reviews_count,
            'bestRating' => '5',
            'worstRating' => '1'
        ];
    }
    
    // Build offers object
    $offers = [
        '@type' => 'Offer',
        'url' => route('product', $product->slug),
        'priceCurrency' => 'UGX',
        'price' => round($finalPrice),
        'availability' => (isset($product->current_stock) && $product->current_stock > 0) 
            ? 'https://schema.org/InStock' 
            : 'https://schema.org/OutOfStock',
        'seller' => [
            '@type' => 'Organization',
            'name' => 'Yoola Uganda',
            'url' => 'https://yoola.ug'
        ],
        'priceValidUntil' => now()->addMonths(3)->format('Y-m-d'),
        'itemCondition' => 'https://schema.org/NewCondition',
        'shippingDetails' => [
            '@type' => 'OfferShippingDetails',
            'shippingDestination' => [
                '@type' => 'DefinedRegion',
                'addressCountry' => 'UG'
            ],
            'deliveryTime' => [
                '@type' => 'ShippingDeliveryTime',
                'handlingTime' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => 0,
                    'maxValue' => 1,
                    'unitCode' => 'DAY'
                ],
                'transitTime' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => 1,
                    'maxValue' => 3,
                    'unitCode' => 'DAY'
                ]
            ]
        ]
    ];
    
    // Add high price if there's a discount (shows strikethrough in some SERPs)
    if ($hasDiscount && $discountAmount > 0) {
        $offers['highPrice'] = round($basePrice);
    }
    
    // Build the main product schema
    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => Str::limit(strip_tags($product->details ?? $product->name ?? ''), 500),
        'image' => $images,
        'sku' => (string) ($product->id ?? ''),
        'mpn' => (string) ($product->id ?? ''),
        'brand' => [
            '@type' => 'Brand',
            'name' => $brandName
        ],
        'category' => $categoryName,
        'offers' => $offers
    ];
    
    // Add aggregate rating only if we have reviews
    if ($aggregateRating) {
        $productSchema['aggregateRating'] = $aggregateRating;
    }
    
    // Add GTIN if available
    if (isset($product->gtin) && $product->gtin) {
        $productSchema['gtin13'] = $product->gtin;
    }
@endphp
<script type="application/ld+json">
{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

{{-- BreadcrumbList Schema --}}
@php
    $breadcrumbs = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => url('/')
        ]
    ];
    
    $position = 2;
    
    // Add parent category if exists
    if (isset($product->category) && $product->category) {
        // Add parent category first if exists
        if (isset($product->category->parent) && $product->category->parent) {
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $product->category->parent->name,
                'item' => route('products', ['category' => $product->category->parent->slug])
            ];
        }
        
        // Add main category
        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $product->category->name,
            'item' => route('products', ['category' => $product->category->slug])
        ];
    }
    
    // Add product as final item
    $breadcrumbs[] = [
        '@type' => 'ListItem',
        'position' => $position,
        'name' => $product->name,
        'item' => route('product', $product->slug)
    ];
    
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbs
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
