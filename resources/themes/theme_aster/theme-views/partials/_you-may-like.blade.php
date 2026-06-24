@php use App\Utils\Helpers; @endphp
@if(isset($youMayLikeProducts) && $youMayLikeProducts->count() > 0)
<section class="you-may-like-section">
    <div class="container">
        <div class="card">
            <div class="p-3 p-sm-4">
                <div class="d-flex flex-wrap justify-content-between gap-3 mb-3 mb-sm-4">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <span class="text-primary fs-3"><i class="bi bi-heart-fill"></i></span>
                        <h2 class="text-uppercase fw-extra-bold mb-0">
                            <span class="text-primary">{{ translate('you_may') }}</span> {{ translate('like') }}
                        </h2>
                    </div>
                    <div class="swiper-nav d-flex gap-2 gap-lg-4 align-items-center">
                        <a href="{{ route('products') }}" class="btn-link text-capitalize">
                            {{ translate('view_all') }}
                            <i class="bi bi-chevron-right text-primary"></i>
                        </a>
                        <div class="d-flex gap-2 align-items-center">
                            <div class="swiper-button-prev you-may-like-nav-prev position-static rounded-10"></div>
                            <div class="swiper-button-next you-may-like-nav-next position-static rounded-10"></div>
                        </div>
                    </div>
                </div>
                <div class="swiper-container">
                    <div class="position-relative">
                        <div class="swiper" data-swiper-loop="false" data-swiper-margin="16"
                             data-swiper-pagination-el="null" data-swiper-navigation-next=".you-may-like-nav-next"
                             data-swiper-navigation-prev=".you-may-like-nav-prev"
                             data-swiper-breakpoints='{"0": {"slidesPerView": "2"}, "576": {"slidesPerView": "3"}, "768": {"slidesPerView": "4"}, "992": {"slidesPerView": "5"}, "1200": {"slidesPerView": "6"}}'>
                            <div class="swiper-wrapper">
                                @foreach($youMayLikeProducts as $product)
                                    <div class="swiper-slide">
                                        <div class="product-card bg-white rounded-10 border overflow-hidden h-100">
                                            <div class="position-relative">
                                                <a href="{{ route('product', $product->slug) }}" class="d-block">
                                                    <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}"
                                                         alt="{{ $product->name }}" loading="lazy"
                                                         class="dark-support img-fit aspect-1 w-100">
                                                </a>
                                                @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                                                    <span class="discount-badge position-absolute top-0 start-0 m-2">
                                                        -{{ getProductPriceByType(product: $product, type: 'discount', result: 'string') }}
                                                    </span>
                                                @endif
                                                <div class="product-actions position-absolute top-0 end-0 m-2">
                                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm get-quick-view"
                                                            data-product-id="{{ $product->id }}"
                                                            data-action="{{ route('quick-view') }}">
                                                        <i class="bi bi-eye fs-14"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="p-2">
                                                @if($product->brand)
                                                    <div class="fs-10 text-muted mb-1">{{ $product->brand->name }}</div>
                                                @endif
                                                <h6 class="fs-12 fw-semibold mb-1 line-clamp-2" style="min-height: 32px;">
                                                    <a href="{{ route('product', $product->slug) }}" class="text-dark text-decoration-none">
                                                        {{ $product->name }}
                                                    </a>
                                                </h6>
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
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
