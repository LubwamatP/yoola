{{-- Product Card for Landing Pages - Optimized for Conversion --}}
@php
    $discount = 0;
    if($product->discount_type == 'percent'){
        $discount = ($product->unit_price * $product->discount) / 100;
    } else {
        $discount = $product->discount ?? 0;
    }
    $final_price = $product->unit_price - $discount;
    $avgRating = $product->reviews_avg_rating ?? ($product->reviews ? $product->reviews->avg('rating') : 0);
    $reviewCount = $product->reviews_count ?? ($product->reviews ? $product->reviews->count() : 0);
@endphp

<div class="product-card card h-100 border-0 shadow-sm hover-shadow transition-all rounded-3 overflow-hidden">
    {{-- Image Section --}}
    <div class="position-relative">
        <a href="{{ route('product', $product->slug) }}" class="d-block">
            <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}"
                 onerror="this.src='{{ theme_asset('assets/img/image-place-holder.png') }}'"
                 class="card-img-top product-img"
                 alt="{{ $product->name }}"
                 loading="lazy">
        </a>
        
        {{-- Badges --}}
        @if($product->discount > 0)
        <span class="position-absolute top-0 start-0 m-2 badge bg-danger rounded-pill px-2 py-1">
            @if($product->discount_type == 'percent')
                -{{ round($product->discount) }}%
            @else
                {{ translate('Sale') }}
            @endif
        </span>
        @endif
        
        @if($product->current_stock <= 0)
        <span class="position-absolute top-0 end-0 m-2 badge bg-secondary">
            {{ translate('Out of Stock') }}
        </span>
        @elseif($product->current_stock <= 5)
        <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark small">
            Only {{ $product->current_stock }} left!
        </span>
        @endif

        {{-- Quick Actions --}}
        <div class="quick-actions position-absolute bottom-0 start-0 end-0 p-2 bg-gradient-dark opacity-0 transition-all">
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('product', $product->slug) }}" class="btn btn-light btn-sm rounded-circle" title="{{ translate('View') }}">
                    <i class="bi bi-eye"></i>
                </a>
                <button class="btn btn-light btn-sm rounded-circle add-to-wishlist" 
                        data-product-id="{{ $product->id }}" title="{{ translate('Wishlist') }}">
                    <i class="bi bi-heart"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="card-body p-3 d-flex flex-column">
        {{-- Brand --}}
        @if($product->brand)
        <div class="text-muted fs-12 mb-1">{{ $product->brand->name }}</div>
        @endif

        {{-- Product Name --}}
        <h6 class="card-title fw-semibold mb-2 text-truncate-2" style="min-height: 40px;">
            <a href="{{ route('product', $product->slug) }}" class="text-dark text-decoration-none" title="{{ $product->name }}">
                {{ Str::limit($product->name, 50) }}
            </a>
        </h6>

        {{-- Rating --}}
        @if($reviewCount > 0)
        <div class="mb-2 d-flex align-items-center gap-1">
            <div class="text-warning">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= round($avgRating) ? 'bi-star-fill' : 'bi-star' }} fs-12"></i>
                @endfor
            </div>
            <small class="text-muted">({{ $reviewCount }})</small>
        </div>
        @endif

        {{-- Price Section --}}
        <div class="mt-auto">
            <div class="d-flex align-items-center flex-wrap gap-1 mb-2">
                <span class="h5 fw-bold text-primary mb-0">
                    {{ webCurrencyConverter($final_price) }}
                </span>
                @if($product->discount > 0)
                <span class="text-muted text-decoration-line-through fs-12">
                    {{ webCurrencyConverter($product->unit_price) }}
                </span>
                @endif
            </div>
            
            {{-- Savings Badge --}}
            @if($product->discount > 0)
            <div class="mb-2">
                <span class="badge bg-success bg-opacity-10 text-success small">
                    <i class="bi bi-tag"></i> Save {{ webCurrencyConverter($discount) }}
                </span>
            </div>
            @endif

            {{-- CTA Buttons --}}
            <div class="d-grid gap-2">
                @if($product->current_stock > 0)
                <button class="btn btn-primary btn-sm fw-semibold add-to-cart-landing" 
                        data-product-id="{{ $product->id }}">
                    <i class="bi bi-cart-plus"></i> {{ translate('Add to Cart') }}
                </button>
                @else
                <button class="btn btn-secondary btn-sm" disabled>
                    <i class="bi bi-x-circle"></i> {{ translate('Out of Stock') }}
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.product-card { transition: all 0.3s ease; }
.product-card:hover { transform: translateY(-5px); }
.product-card:hover .quick-actions { opacity: 1; }
.product-img { height: 180px; object-fit: contain; background: #f8f9fa; padding: 10px; transition: transform 0.3s; }
.product-card:hover .product-img { transform: scale(1.05); }
.text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.bg-gradient-dark { background: linear-gradient(to top, rgba(0,0,0,0.7), transparent); }
.transition-all { transition: all 0.3s ease; }
.fs-12 { font-size: 12px; }
</style>
