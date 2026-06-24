@php use App\Utils\Helpers; @endphp
@if(isset($hotNowProducts) && $hotNowProducts->count() > 0)
<section class="hot-now-section">
    <div class="container">
        <div class="card bg-gradient-danger-light border-0">
            <div class="card-body p-2 p-sm-3">
                <div class="d-flex flex-wrap justify-content-between gap-3 mb-3 align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger text-white pulse-animation">
                            <i class="bi bi-lightning-charge-fill"></i> {{ translate('LIVE') }}
                        </span>
                        <h3 class="fw-extra-bold text-capitalize mb-0">
                            <span class="text-danger">{{ translate('hot') }}</span>
                            {{ translate('right_now') }}
                        </h3>
                    </div>
                    <div class="text-muted fs-12">
                        <i class="bi bi-people-fill me-1"></i>
                        {{ translate('people_are_viewing_these') }}
                    </div>
                </div>
                <div class="row g-2">
                    @foreach($hotNowProducts->take(6) as $product)
                        <div class="col-6 col-md-4 col-lg-2">
                            <a href="{{ route('product', $product->slug) }}" class="product-card-hot text-center d-block p-2 border rounded-10 hover-zoom-in bg-white h-100">
                                <div class="position-relative mb-2">
                                    <img alt="{{ $product->name }}" loading="lazy"
                                         class="dark-support rounded aspect-1 w-100"
                                         src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}">
                                    @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                        <span class="discount-badge position-absolute top-0 start-0">
                                            -{{ getProductPriceByType(product: $product, type: 'discount', result: 'string') }}
                                        </span>
                                    @endif
                                    <span class="badge bg-danger position-absolute bottom-0 end-0 m-1 pulse-badge">
                                        <i class="bi bi-eye-fill"></i>
                                    </span>
                                </div>
                                <div class="fs-12 fw-semibold text-dark line-clamp-2 mb-1">{{ $product->name }}</div>
                                <div class="product__price">
                                    <ins class="product__new-price fs-14 fw-bold text-primary">
                                        {{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}
                                    </ins>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.bg-gradient-danger-light {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, rgba(220, 53, 69, 0.1) 100%);
}
.pulse-animation {
    animation: pulse 2s infinite;
}
.pulse-badge {
    animation: pulse-badge 1.5s infinite;
}
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.6; }
    100% { opacity: 1; }
}
@keyframes pulse-badge {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
</style>
@endif
