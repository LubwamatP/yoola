@extends('layouts.front-end.app')

@section('title', $pricePage->title)

@push('css_or_js')
    {{-- SEO Meta Tags --}}
    <meta name="description" content="{{ $pricePage->meta_description }}">
    <meta name="robots" content="{{ $pricePage->is_indexed ? 'index, follow' : 'noindex, follow' }}">
    <link rel="canonical" href="{{ $pricePage->url }}">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $pricePage->title }}">
    <meta property="og:description" content="{{ $pricePage->meta_description }}">
    <meta property="og:url" content="{{ $pricePage->url }}">
    <meta property="og:type" content="website">
    @if($pricePage->hero_image)
        <meta property="og:image" content="{{ asset('storage/' . $pricePage->hero_image) }}">
    @endif
    
    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pricePage->title }}">
    <meta name="twitter:description" content="{{ $pricePage->meta_description }}">

    <style>
        .price-hero {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 3rem 0;
        }
        .price-hero h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .price-range {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .price-range strong {
            color: #ffc107;
        }
        .whatsapp-cta {
            background: #25D366;
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }
        .whatsapp-cta:hover {
            transform: scale(1.05);
            color: white;
        }
        .price-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .price-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .price-table .price {
            font-weight: 700;
            color: #dc3545;
            font-size: 1.1rem;
        }
        .stock-badge {
            background: #28a745;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        .trust-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin: 2rem 0;
        }
        .trust-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .trust-badge i {
            color: #28a745;
        }
        .buying-guide {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        .faq-item {
            border-bottom: 1px solid #eee;
            padding: 1.5rem 0;
        }
        .faq-item h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .faq-item p {
            color: #666;
            margin: 0;
        }
        .related-pages {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .related-page-link {
            padding: 0.75rem 1.5rem;
            background: #e9ecef;
            border-radius: 25px;
            text-decoration: none;
            color: #333;
            transition: background 0.2s;
        }
        .related-page-link:hover {
            background: #dc3545;
            color: white;
        }
        .sticky-cta {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        @media (max-width: 768px) {
            .price-hero h1 {
                font-size: 1.5rem;
            }
            .price-range {
                font-size: 1.2rem;
            }
        }
    </style>
@endpush

@section('content')
<main class="main-content">
    {{-- Hero Section --}}
    <section class="price-hero">
        <div class="container">
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0" style="background: transparent;">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('price-pages.index') }}" class="text-white-50">Prices</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">{{ $pricePage->h1 }}</li>
                </ol>
            </nav>

            <h1>{{ $pricePage->h1 }}</h1>
            
            <p class="price-range">
                Prices from <strong>{{ number_format($priceRange['min']) }}/=</strong>
                @if($priceRange['max'] > $priceRange['min'])
                    to <strong>{{ number_format($priceRange['max']) }}/=</strong>
                @endif
            </p>

            <a href="https://wa.me/256780221421?text={{ urlencode('Hi! I\'m interested in ' . $pricePage->h1 . '. What\'s available?') }}" 
               class="whatsapp-cta" target="_blank">
                <i class="bi bi-whatsapp"></i> Get Best Price on WhatsApp →
            </a>
        </div>
    </section>

    {{-- Trust Badges --}}
    <div class="container">
        <div class="trust-badges">
            <div class="trust-badge">
                <i class="bi bi-patch-check-fill"></i>
                <span>Official Warranty</span>
            </div>
            <div class="trust-badge">
                <i class="bi bi-truck"></i>
                <span>Free Kampala Delivery</span>
            </div>
            <div class="trust-badge">
                <i class="bi bi-shop"></i>
                <span>Physical Shop: Burton St</span>
            </div>
            <div class="trust-badge">
                <i class="bi bi-clock"></i>
                <span>Same-Day Delivery</span>
            </div>
        </div>
    </div>

    {{-- Intro Text --}}
    <div class="container py-4">
        <p class="lead">{{ $pricePage->intro_text }}</p>
    </div>

    {{-- Price Table --}}
    <div class="container py-4">
        <h2 class="mb-4">Current Prices ({{ now()->format('F Y') }})</h2>
        
        @if($products->count() > 0)
            <div class="table-responsive price-table">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($product->thumbnail_full_url['path'] ?? null)
                                        <img src="{{ $product->thumbnail_full_url['path'] }}" 
                                             alt="{{ $product->name }}" 
                                             style="width: 60px; height: 60px; object-fit: contain;">
                                    @endif
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                        @if($product->brand)
                                            <br><small class="text-muted">{{ $product->brand->name }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="price">{{ number_format($product->unit_price) }}/=</td>
                            <td>
                                @if($product->current_stock > 0)
                                    <span class="stock-badge">✓ In Stock</span>
                                @else
                                    <span class="badge bg-warning">Pre-order</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('product', $product->slug) }}" class="btn btn-sm btn-outline-danger">
                                    View Details →
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Products are being updated. 
                <a href="https://wa.me/256780221421" class="alert-link">WhatsApp us</a> for current availability.
            </div>
        @endif
    </div>

    {{-- Buying Guide --}}
    @if($pricePage->buying_guide)
    <div class="container">
        <div class="buying-guide">
            <h2><i class="bi bi-book"></i> Buying Guide</h2>
            {!! $pricePage->buying_guide !!}
        </div>
    </div>
    @endif

    {{-- FAQs --}}
    @if($pricePage->faqs && count($pricePage->faqs) > 0)
    <div class="container py-4">
        <h2 class="mb-4">Frequently Asked Questions</h2>
        @foreach($pricePage->faqs as $faq)
        <div class="faq-item">
            <h3>{{ $faq['question'] }}</h3>
            <p>{{ $faq['answer'] }}</p>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Related Pages --}}
    @if($relatedPages->count() > 0)
    <div class="container py-4">
        <h2 class="mb-4">Related Searches</h2>
        <div class="related-pages">
            @foreach($relatedPages as $related)
                <a href="{{ $related->url }}" class="related-page-link">
                    {{ $related->h1 }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Bottom CTA --}}
    <div class="container py-5">
        <div class="text-center p-5 bg-light rounded">
            <h2>Ready to Buy?</h2>
            <p class="lead mb-4">Get the best prices on WhatsApp or visit our shop</p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="https://wa.me/256780221421" class="btn btn-success btn-lg">
                    <i class="bi bi-whatsapp"></i> WhatsApp: +256780221421
                </a>
                <a href="https://maps.google.com/?q=Burton+Street+Aponye+Mall+Kampala" 
                   class="btn btn-outline-dark btn-lg" target="_blank">
                    <i class="bi bi-geo-alt"></i> Burton St, Aponye Mall
                </a>
            </div>
        </div>
    </div>

    {{-- Sticky WhatsApp CTA (Mobile) --}}
    <div class="sticky-cta d-md-none">
        <a href="https://wa.me/256780221421" class="whatsapp-cta">
            <i class="bi bi-whatsapp"></i> WhatsApp
        </a>
    </div>
</main>

{{-- FAQ Schema --}}
@if($pricePage->faqs && count($pricePage->faqs) > 0)
<script type="application/ld+json">
{!! json_encode($pricePage->faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif

{{-- Product List Schema --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => $pricePage->h1,
    'description' => $pricePage->meta_description,
    'numberOfItems' => $products->count(),
    'itemListElement' => $products->take(10)->map(function($product, $index) {
        return [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'item' => [
                '@type' => 'Product',
                'name' => $product->name,
                'url' => route('product', $product->slug),
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $product->unit_price,
                    'priceCurrency' => 'UGX',
                    'availability' => $product->current_stock > 0 
                        ? 'https://schema.org/InStock' 
                        : 'https://schema.org/PreOrder',
                ],
            ],
        ];
    })->toArray(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- Breadcrumb Schema --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Prices',
            'item' => route('price-pages.index'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $pricePage->h1,
            'item' => $pricePage->url,
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection
