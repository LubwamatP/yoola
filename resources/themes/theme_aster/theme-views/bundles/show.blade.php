@extends('theme-views.layouts.app')

@section('title', $bundle->name . ' | ' . translate('Bundle_Deal') . ' | ' . $web_config['company_name'])

@push('css_or_js')
    <meta name="description" content="{{ $bundle->description ?? 'Save ' . number_format($bundle->original_price - $bundle->bundle_price) . ' UGX with this bundle deal. ' . $bundle->products->count() . ' products included.' }}">
    <link rel="canonical" href="{{ route('bundles.show', $bundle->slug) }}">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $bundle->name }} | Bundle Deal">
    <meta property="og:description" content="Save {{ number_format($bundle->original_price - $bundle->bundle_price) }} UGX!">
    @if($bundle->image_full_url['path'] ?? null)
        <meta property="og:image" content="{{ $bundle->image_full_url['path'] }}">
    @endif
    
    <style>
        .bundle-detail-hero {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            padding: 2rem 0;
        }
        .bundle-savings-banner {
            background: #dc3545;
            color: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
        }
        .bundle-savings-banner .amount {
            font-size: 2rem;
            font-weight: 700;
        }
        .bundle-product-item {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .bundle-product-item img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .bundle-product-item .product-price {
            font-weight: 600;
            color: #666;
        }
        .bundle-summary {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 100px;
        }
        .price-breakdown {
            border-top: 1px dashed #ddd;
            padding-top: 1rem;
            margin-top: 1rem;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        .price-row.total {
            font-size: 1.5rem;
            font-weight: 700;
            color: #dc3545;
            border-top: 2px solid #dc3545;
            padding-top: 0.5rem;
            margin-top: 0.5rem;
        }
        .bundle-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .bundle-feature {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }
        .bundle-feature i {
            color: #28a745;
        }
    </style>
@endpush

@section('content')
<main class="main-content">
    {{-- Hero --}}
    <section class="bundle-detail-hero">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3" style="--bs-breadcrumb-divider-color: rgba(255,255,255,0.5);">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">{{ translate('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('bundles.index') }}" class="text-white-50">{{ translate('Bundles') }}</a></li>
                    <li class="breadcrumb-item active text-white">{{ $bundle->name }}</li>
                </ol>
            </nav>
            <h1 class="h2">{{ $bundle->name }}</h1>
            <p class="opacity-75 mb-0">{{ $bundle->products->count() }} {{ translate('products_in_this_bundle') }}</p>
        </div>
    </section>

    <div class="container py-4">
        <div class="row">
            {{-- Products List --}}
            <div class="col-lg-8">
                {{-- Savings Banner --}}
                @php
                    $savings = $bundle->original_price - $bundle->bundle_price;
                    $savingsPercent = $bundle->original_price > 0 
                        ? round(($savings / $bundle->original_price) * 100) 
                        : 0;
                @endphp
                <div class="bundle-savings-banner mb-4">
                    <div class="amount">{{ translate('Save') }} {{ number_format($savings) }}/=</div>
                    <div>{{ $savingsPercent }}% {{ translate('off_when_you_buy_together') }}</div>
                </div>

                {{-- Bundle Description --}}
                @if($bundle->description)
                    <div class="mb-4">
                        <h2 class="h5">{{ translate('About_This_Bundle') }}</h2>
                        <p>{{ $bundle->description }}</p>
                    </div>
                @endif

                {{-- Products in Bundle --}}
                <h2 class="h5 mb-3">{{ translate('Products_Included') }}</h2>
                @foreach($bundle->products as $product)
                    <div class="bundle-product-item">
                        <a href="{{ route('product', $product->slug) }}">
                            <img src="{{ $product->thumbnail_full_url['path'] ?? '' }}" 
                                 alt="{{ $product->name }}">
                        </a>
                        <div class="flex-grow-1">
                            <a href="{{ route('product', $product->slug) }}" class="text-dark text-decoration-none">
                                <h4 class="h6 mb-1">{{ $product->name }}</h4>
                            </a>
                            @if($product->brand)
                                <small class="text-muted">{{ $product->brand->name }}</small>
                            @endif
                            <div class="product-price mt-1">{{ number_format($product->unit_price) }}/=</div>
                        </div>
                        <a href="{{ route('product', $product->slug) }}" class="btn btn-sm btn-outline-secondary">
                            {{ translate('View') }}
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- Sidebar - Order Summary --}}
            <div class="col-lg-4">
                <div class="bundle-summary">
                    <h3 class="h5 mb-3">{{ translate('Bundle_Summary') }}</h3>
                    
                    {{-- Features --}}
                    <div class="bundle-features">
                        <div class="bundle-feature">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ translate('Official_Warranty') }}</span>
                        </div>
                        <div class="bundle-feature">
                            <i class="bi bi-truck"></i>
                            <span>{{ translate('Free_Delivery') }}</span>
                        </div>
                        <div class="bundle-feature">
                            <i class="bi bi-box-seam"></i>
                            <span>{{ $bundle->products->count() }} {{ translate('Items') }}</span>
                        </div>
                        <div class="bundle-feature">
                            <i class="bi bi-clock"></i>
                            <span>{{ translate('Same_Day') }}</span>
                        </div>
                    </div>

                    {{-- Price Breakdown --}}
                    <div class="price-breakdown">
                        <div class="price-row">
                            <span class="text-muted">{{ translate('Original_Price') }}</span>
                            <span class="text-decoration-line-through">{{ number_format($bundle->original_price) }}/=</span>
                        </div>
                        <div class="price-row text-success">
                            <span>{{ translate('Bundle_Savings') }}</span>
                            <span>-{{ number_format($savings) }}/=</span>
                        </div>
                        <div class="price-row total">
                            <span>{{ translate('Bundle_Price') }}</span>
                            <span>{{ number_format($bundle->bundle_price) }}/=</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-grid gap-2 mt-4">
                        <a href="https://wa.me/256704229768?text={{ urlencode('Hi! I want to order the bundle: ' . $bundle->name . ' at ' . number_format($bundle->bundle_price) . '/=') }}" 
                           class="btn btn-success btn-lg" target="_blank">
                            <i class="bi bi-whatsapp"></i> {{ translate('Order_on_WhatsApp') }}
                        </a>
                        
                        <form action="{{ route('bundles.add-to-cart', $bundle->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-lg w-100">
                                <i class="bi bi-cart-plus"></i> {{ translate('Add_to_Cart') }}
                            </button>
                        </form>
                    </div>

                    {{-- Trust --}}
                    <div class="mt-4 text-center text-muted small">
                        <i class="bi bi-shield-check"></i>
                        {{ translate('Secure_checkout_with_pay_on_delivery') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Related Bundles --}}
    @if(isset($relatedBundles) && $relatedBundles->isNotEmpty())
    <div class="container pb-5">
        <h2 class="h4 mb-4">{{ translate('You_May_Also_Like') }}</h2>
        <div class="row g-4">
            @foreach($relatedBundles->take(3) as $related)
                <div class="col-md-4">
                    <div class="bundle-product-item">
                        <a href="{{ route('bundles.show', $related->slug) }}">
                            <h5 class="mb-1">{{ $related->name }}</h5>
                            <div class="text-danger fw-bold">{{ number_format($related->bundle_price) }}/=</div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</main>

{{-- Product Schema --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $bundle->name,
    'description' => $bundle->description ?? 'Bundle deal with ' . $bundle->products->count() . ' products',
    'offers' => [
        '@type' => 'Offer',
        'price' => $bundle->bundle_price,
        'priceCurrency' => 'UGX',
        'availability' => 'https://schema.org/InStock',
        'seller' => [
            '@type' => 'Organization',
            'name' => 'Yoola Uganda',
        ],
    ],
], JSON_UNESCAPED_SLASHES) !!}
</script>
@endsection
