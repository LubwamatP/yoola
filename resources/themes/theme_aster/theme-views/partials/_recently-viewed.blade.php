@php use App\Utils\Helpers; @endphp
@if(isset($recentlyViewedProducts) && $recentlyViewedProducts->count() > 0)
<section class="recently-viewed-section">
    <div class="container">
        <div class="card">
            <div class="card-body p-2 p-sm-3">
                <div class="d-flex flex-wrap justify-content-between gap-3 mb-3 align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-info fs-4"><i class="bi bi-clock-history"></i></span>
                        <h3 class="fw-extra-bold text-capitalize mb-0">
                            <span class="text-info">{{ translate('recently') }}</span>
                            {{ translate('viewed') }}
                        </h3>
                    </div>
                    <a href="{{ route('products') }}" class="btn-link">
                        {{ translate('Continue_Shopping') }}
                        <i class="bi bi-chevron-right text-primary"></i>
                    </a>
                </div>
                <div class="auto-col">
                    @foreach($recentlyViewedProducts->take(8) as $product)
                        @if($product)
                        <a href="{{ route('product', $product->slug) }}" class="product__inline-card d-flex align-items-center gap-2 p-2 border rounded-10 hover-zoom-in">
                            <div class="img">
                                <img width="64" height="64" alt="{{ $product->name }}" loading="lazy"
                                     class="dark-support rounded aspect-1 border"
                                     src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}">
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fs-14 fw-semibold text-dark line-clamp-1">{{ $product->name }}</div>
                                <div class="product__price d-flex flex-wrap align-items-baseline gap-1">
                                    @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                        <del class="product__old-price fs-12 opacity-50">
                                            {{ webCurrencyConverter($product->unit_price) }}
                                        </del>
                                    @endif
                                    <ins class="product__new-price fs-14 fw-bold text-primary">
                                        {{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}
                                    </ins>
                                </div>
                                @if(isset($product->brand))
                                    <div class="fs-10 text-muted">{{ $product->brand->name ?? '' }}</div>
                                @endif
                            </div>
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif
