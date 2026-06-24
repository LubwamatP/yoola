@php use App\Utils\Helpers; @endphp
@if(isset($trendingProducts) && $trendingProducts->count() > 0)
<section class="trending-products-section py-3">
    <div class="container">
        <div class="flexible-grid lg-down-1 gap-3">
            {{-- Sidebar Banner (Left) - Desktop Only --}}
            <div class="d-none d-sm-block">
                @if(isset($bannerTypeSidebarBanner) && $bannerTypeSidebarBanner)
                    <a href="{{ $bannerTypeSidebarBanner['url'] }}" class="d-block h-100">
                        <img alt="Promo Banner" class="dark-support rounded w-100 h-100 object-fit-cover"
                            src="{{ getStorageImages(path: $bannerTypeSidebarBanner['photo_full_url'], type:'banner') }}">
                    </a>
                @else
                    {{-- Fallback: Yoola Promo Banner --}}
                    <div class="bg-gradient-primary rounded p-4 h-100 d-flex flex-column justify-content-center text-white">
                        <div class="mb-3">
                            <span class="badge bg-warning text-dark mb-2">🔥 HOT DEALS</span>
                            <h3 class="fw-bold mb-2">Quality Electronics</h3>
                            <p class="mb-3 opacity-90">Best prices in Uganda. Free delivery in Kampala!</p>
                        </div>
                        <div class="mt-auto">
                            <a href="{{ route('products') }}" class="btn btn-light btn-sm fw-semibold">
                                Shop Now <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="mt-3 pt-3 border-top border-light border-opacity-25">
                            <div class="d-flex align-items-center gap-2 small">
                                <i class="bi bi-shield-check"></i>
                                <span>Warranty Included</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 small mt-1">
                                <i class="bi bi-truck"></i>
                                <span>Free Kampala Delivery</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Mobile Banner --}}
            @if(isset($bannerTypeFooterBanner[0]))
                <div class="col-12 d-sm-none">
                    <a href="{{ $bannerTypeFooterBanner[0]['url'] }}" class="ad-hover">
                        <img loading="lazy" class="dark-support rounded w-100" alt="{{ $product['name'] ?? 'Product' }}"
                            src="{{ getStorageImages(path: $bannerTypeFooterBanner[0]['photo_full_url'], type:'banner') }}">
                    </a>
                </div>
            @endif

            {{-- Trending Products (Right) --}}
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-gradient-danger text-white py-3 px-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fs-2 animate-pulse"><i class="bi bi-fire"></i></span>
                            <div>
                                <h2 class="fw-extra-bold text-uppercase mb-0 fs-3">
                                    {{ translate('trending') }} {{ translate('now') }}
                                </h2>
                                <small class="opacity-75">{{ translate('Most popular products right now') }}</small>
                            </div>
                        </div>
                        <a href="{{ route('products', ['sort_by' => 'popularity']) }}" class="btn btn-light btn-sm fw-semibold">
                            {{ translate('View_All') }}
                            <i class="bi bi-chevron-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-3 p-sm-4">
                    <div class="row g-3">
                        @foreach($trendingProducts->take(10) as $index => $product)
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2-4">
                                <a href="{{ route('product', $product->slug) }}" class="trending-product-card d-block bg-white border rounded-10 overflow-hidden h-100 text-decoration-none hover-shadow transition-all">
                                    <div class="position-relative">
                                        <img alt="{{ $product->name }}" loading="lazy"
                                             class="dark-support w-100 aspect-1 img-fit"
                                             src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}">
                                        @if($index < 3)
                                            <span class="position-absolute top-0 start-0 m-2 badge bg-danger rounded-pill px-2 py-1">
                                                <i class="bi bi-fire me-1"></i>#{{ $index + 1 }}
                                            </span>
                                        @endif
                                        @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                            <span class="discount-badge position-absolute top-0 end-0 m-2">
                                                -{{ getProductPriceByType(product: $product, type: 'discount', result: 'string') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="p-2">
                                        @if($product->brand)
                                            <div class="fs-10 text-muted mb-1">{{ $product->brand->name }}</div>
                                        @endif
                                        <div class="fs-12 fw-semibold text-dark line-clamp-2 mb-1" style="min-height: 32px;">
                                            {{ $product->name }}
                                        </div>
                                        <div class="product__price d-flex flex-wrap align-items-baseline gap-1">
                                            @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                                <del class="product__old-price fs-10 opacity-50">
                                                    {{ webCurrencyConverter($product->unit_price) }}
                                                </del>
                                            @endif
                                            <ins class="product__new-price fs-14 fw-bold text-primary text-decoration-none">
                                                {{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}
                                            </ins>
                                        </div>
                                        @php
                                            $avgRating = $product->reviews ? $product->reviews->avg('rating') : 0;
                                            $reviewCount = $product->reviews ? $product->reviews->count() : 0;
                                        @endphp
                                        @if($reviewCount > 0)
                                            <div class="d-flex gap-1 align-items-center mt-1">
                                                <div class="star-rating text-gold fs-10">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= round($avgRating))
                                                            <i class="bi bi-star-fill"></i>
                                                        @else
                                                            <i class="bi bi-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="fs-10 text-muted">({{ $reviewCount }})</span>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
}
.animate-pulse {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
.trending-product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.transition-all {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.col-xl-2-4 {
    flex: 0 0 auto;
    width: 20%;
}
@media (max-width: 1199.98px) {
    .col-xl-2-4 {
        width: 25%;
    }
}
@media (max-width: 767.98px) {
    .col-xl-2-4 {
        width: 33.333%;
    }
}
@media (max-width: 575.98px) {
    .col-xl-2-4 {
        width: 50%;
    }
}
</style>
@endif
