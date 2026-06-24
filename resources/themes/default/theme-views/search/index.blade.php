@extends('layouts.front-end.app')

@section('title', translate('Search Results for').' '.$query)

@push('css_or_js')
<style>
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-3px);
}
.transition-all {
    transition: all 0.25s ease-in-out;
}
.hover-primary:hover {
    background-color: var(--bs-primary) !important;
    color: white !important;
}
.product-img {
    height: 180px;
    object-fit: contain;
    transition: transform 0.3s ease;
    background: #f8f9fa;
    padding: 10px;
}
.product-card:hover .product-img {
    transform: scale(1.05);
}
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.sticky-sidebar {
    position: sticky;
    top: 80px;
    z-index: 100;
}
.filter-section {
    max-height: calc(100vh - 100px);
    overflow-y: auto;
}
</style>
@endpush

@section('content')
<div class="container py-4" dir="{{ session('direction') }}">

    <!-- Search Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-3 mb-4">
        <div>
            <h2 class="h4 mb-0 font-weight-bold">{{ translate('Search Results') }}</h2>
            <p class="text-muted mb-0" style="font-size: 14px;">
                {{ translate('Found') }} <strong class="text-primary">{{ $total }}</strong> {{ translate('items for') }} "<span class="font-italic">{{ $query }}</span>"
                @if(!empty($ai_powered))
                    <span class="badge badge-success ml-2" title="{{ translate('Powered by AI') }}">
                        <i class="fa fa-magic"></i> {{ translate('AI Search') }}
                    </span>
                @endif
            </p>

            {{-- Did You Mean (Typo Correction) --}}
            @if(!empty($did_you_mean))
                <div class="mt-2 p-2 rounded" style="background: rgba(255, 193, 7, 0.1);">
                    <span class="text-muted">{{ translate('Did you mean:') }}</span>
                    <a href="{{ route('search', ['q' => $did_you_mean]) }}" class="font-weight-bold text-primary text-decoration-underline">
                        {{ $did_you_mean }}
                    </a>?
                </div>
            @endif

            {{-- Active Filters Display --}}
            @if(!empty($filters) && (isset($filters['brand']) || isset($filters['category']) || isset($filters['price_min']) || isset($filters['min_rating'])))
                <div class="mt-2 d-flex flex-wrap align-items-center" style="gap: 0.5rem;">
                    <small class="text-muted">{{ translate('Active filters:') }}</small>
                    @if(!empty($filters['brand']))
                        <span class="badge badge-primary">
                            {{ translate('Brand:') }} {{ $filters['brand'] }}
                            <a href="{{ route('search', array_merge(request()->except('brand'), ['q' => $query])) }}" class="text-white ml-1">&times;</a>
                        </span>
                    @endif
                    @if(!empty($filters['category']))
                        <span class="badge badge-info">
                            {{ translate('Category:') }} {{ $filters['category'] }}
                            <a href="{{ route('search', array_merge(request()->except('category'), ['q' => $query])) }}" class="text-white ml-1">&times;</a>
                        </span>
                    @endif
                    @if(!empty($filters['price_min']) || !empty($filters['price_max']))
                        <span class="badge badge-secondary">
                            {{ translate('Price:') }} {{ number_format($filters['price_min'] ?? 0) }} - {{ $filters['price_max'] ? number_format($filters['price_max']) : '∞' }} UGX
                            <a href="{{ route('search', array_merge(request()->except(['price_min', 'price_max']), ['q' => $query])) }}" class="text-white ml-1">&times;</a>
                        </span>
                    @endif
                    @if(!empty($filters['min_rating']))
                        <span class="badge badge-warning text-dark">
                            {{ $filters['min_rating'] }}+ {{ translate('Stars') }}
                            <a href="{{ route('search', array_merge(request()->except('rating'), ['q' => $query])) }}" class="text-dark ml-1">&times;</a>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sort Dropdown -->
        <div class="d-flex align-items-center" style="gap: 1rem;">
            @if(!empty($sort_options))
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="tio-sort"></i>
                    {{ $sort_options[$filters['sort'] ?? 'relevance'] ?? translate('Sort By') }}
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    @foreach($sort_options as $sortKey => $sortLabel)
                        <li>
                            <a class="dropdown-item {{ ($filters['sort'] ?? 'relevance') === $sortKey ? 'active' : '' }}"
                               href="{{ route('search', array_merge(request()->all(), ['sort' => $sortKey])) }}">
                                {{ $sortLabel }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card border-0 shadow-sm sticky-sidebar">
                <div class="card-body filter-section">
                    <h5 class="font-weight-bold border-bottom pb-2 mb-3">
                        <i class="tio-filter-list"></i> {{ translate('Filter Results') }}
                    </h5>

                    {{-- In Stock Filter --}}
                    <div class="mb-3">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="inStockFilter"
                                   {{ request()->boolean('in_stock') ? 'checked' : '' }}
                                   onchange="window.location='{{ route('search', array_merge(request()->all(), ['in_stock' => request()->boolean('in_stock') ? '' : '1'])) }}'">
                            <label class="custom-control-label" for="inStockFilter">
                                {{ translate('In Stock Only') }}
                                @if(!empty($facets['in_stock_count']))
                                    <span class="text-muted">({{ $facets['in_stock_count'] }})</span>
                                @endif
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox mt-2">
                            <input class="custom-control-input" type="checkbox" id="discountFilter"
                                   {{ request()->boolean('discount') ? 'checked' : '' }}
                                   onchange="window.location='{{ route('search', array_merge(request()->all(), ['discount' => request()->boolean('discount') ? '' : '1'])) }}'">
                            <label class="custom-control-label" for="discountFilter">
                                {{ translate('Discount') }}
                                @if(!empty($facets['with_discount_count']))
                                    <span class="text-muted">({{ $facets['with_discount_count'] }})</span>
                                @endif
                            </label>
                        </div>
                    </div>

                    {{-- Brand Filters --}}
                    @if(!empty($facets['brands']) && count($facets['brands']) > 0)
                    <div class="mb-4">
                        <label class="font-weight-bold mb-2 d-flex justify-content-between">
                            {{ translate('Brands') }}
                            <span class="badge badge-light text-muted">{{ count($facets['brands']) }}</span>
                        </label>
                        <ul class="list-unstyled mb-0" style="max-height: 200px; overflow-y: auto;">
                            @foreach($facets['brands'] as $brandName => $count)
                                <li class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="{{ route('search', array_merge(request()->all(), ['brand' => $brandName])) }}"
                                       class="text-decoration-none {{ request('brand') == $brandName ? 'font-weight-bold text-primary' : 'text-dark' }}">
                                        @if(request('brand') == $brandName)
                                            <i class="tio-checkmark-circle text-primary mr-1"></i>
                                        @endif
                                        {{ $brandName }}
                                    </a>
                                    <span class="badge badge-light text-muted">{{ $count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Category Filters --}}
                    @if(!empty($facets['categories']) && count($facets['categories']) > 0)
                    <div class="mb-4">
                        <label class="font-weight-bold mb-2">{{ translate('Categories') }}</label>
                        <ul class="list-unstyled mb-0">
                            @foreach($facets['categories'] as $categoryName => $count)
                                <li class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="{{ route('search', array_merge(request()->all(), ['category' => $categoryName])) }}"
                                       class="text-decoration-none {{ request('category') == $categoryName ? 'font-weight-bold text-primary' : 'text-dark' }}">
                                        @if(request('category') == $categoryName)
                                            <i class="tio-checkmark-circle text-primary mr-1"></i>
                                        @endif
                                        {{ $categoryName }}
                                    </a>
                                    <span class="badge badge-light text-muted">{{ $count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Price Range Filters --}}
                    @if(!empty($facets['price_ranges']) && count($facets['price_ranges']) > 0)
                    <div class="mb-4">
                        <label class="font-weight-bold mb-2">{{ translate('Price Range') }}</label>
                        <ul class="list-unstyled mb-0">
                            @foreach($facets['price_ranges'] as $range)
                                @php
                                    $isActive = (request('price_min') == $range['min']) && (request('price_max') == ($range['max'] ?? ''));
                                @endphp
                                <li class="mb-2">
                                    <a href="{{ route('search', array_merge(request()->except(['price_min', 'price_max']), ['q' => $query, 'price_min' => $range['min'], 'price_max' => $range['max'] ?? ''])) }}"
                                       class="text-decoration-none {{ $isActive ? 'font-weight-bold text-primary' : 'text-muted' }}">
                                        @if($isActive)
                                            <i class="tio-checkmark-circle text-primary mr-1"></i>
                                        @endif
                                        {{ $range['label'] }}
                                        <small class="text-muted">({{ $range['count'] }})</small>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Rating Filters --}}
                    @if(!empty($facets['ratings']) && count($facets['ratings']) > 0)
                    <div class="mb-4">
                        <label class="font-weight-bold mb-2">{{ translate('Customer Rating') }}</label>
                        <ul class="list-unstyled mb-0">
                            @foreach($facets['ratings'] as $rating)
                                @php $isActive = request('rating') == $rating['rating']; @endphp
                                <li class="mb-2">
                                    <a href="{{ route('search', array_merge(request()->all(), ['rating' => $rating['rating']])) }}"
                                       class="text-decoration-none d-flex align-items-center {{ $isActive ? 'font-weight-bold' : '' }}">
                                        <span class="text-warning mr-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $rating['rating'] ? 'tio-star' : 'tio-star-outlined' }}"></i>
                                            @endfor
                                        </span>
                                        <span class="text-muted">& Up ({{ $rating['count'] }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Clear Filters --}}
                    @if(request()->hasAny(['brand', 'price_min', 'price_max', 'category', 'rating', 'in_stock', 'discount']))
                        <a href="{{ route('search', ['q' => $query]) }}" class="btn btn-outline-danger btn-block btn-sm">
                            <i class="tio-clear-circle"></i> {{ translate('Clear All Filters') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            @if($products->count() > 0)

                {{-- Suggestions Tags --}}
                @if(!empty($suggestions) && count($suggestions) > 0)
                <div class="mb-3">
                    <small class="text-muted mr-2">{{ translate('Related searches:') }}</small>
                    @foreach($suggestions as $suggestion)
                        <a href="{{ route('search', ['q' => $suggestion]) }}" class="badge badge-light text-dark border font-weight-normal mr-1 mb-1 text-decoration-none">
                            {{ $suggestion }}
                        </a>
                    @endforeach
                </div>
                @endif

                {{-- Results Count Bar --}}
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <span class="text-muted" style="font-size: 14px;">
                        {{ translate('Showing') }}
                        {{ ($products->currentPage() - 1) * $products->perPage() + 1 }}-{{ min($products->currentPage() * $products->perPage(), $total) }}
                        {{ translate('of') }} {{ $total }} {{ translate('results') }}
                    </span>
                    @if(isset($response_time) && config('app.debug'))
                        <small class="text-muted">
                            <i class="tio-time"></i> {{ $response_time }}ms
                        </small>
                    @endif
                </div>

                {{-- Product Grid --}}
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-6 col-md-4 col-xl-3 mb-4">
                            <div class="product-card card h-100 border-0 shadow-sm hover-shadow transition-all">
                                <!-- Image -->
                                <div class="card-header border-0 bg-transparent p-0 position-relative overflow-hidden">
                                    <a href="{{route('product',$product->slug)}}" class="d-block">
                                        <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}"
                                             onerror="this.src='{{ dynamicAsset(path: 'public/assets/front-end/img/image-place-holder.png') }}'"
                                             class="card-img-top img-fluid product-img"
                                             alt="{{ $product->name }}">
                                    </a>
                                    @if($product->discount > 0)
                                        <span class="position-absolute badge badge-danger" style="top: 10px; left: 10px;">
                                            @if($product->discount_type == 'percent')
                                                -{{ round($product->discount) }}%
                                            @else
                                                {{ translate('Sale') }}
                                            @endif
                                        </span>
                                    @endif
                                    @if($product->current_stock <= 0)
                                        <span class="position-absolute badge badge-secondary" style="top: 10px; right: 10px;">
                                            {{ translate('Out of Stock') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Body -->
                                <div class="card-body p-3 d-flex flex-column">
                                    {{-- Brand --}}
                                    @if($product->brand)
                                        <div class="mb-1 text-muted" style="font-size: 12px;">{{ $product->brand->name }}</div>
                                    @endif

                                    {{-- Product Name --}}
                                    <h6 class="card-title text-truncate-2 font-weight-bold mb-2" style="min-height: 40px;">
                                        <a href="{{route('product',$product->slug)}}" class="text-dark text-decoration-none" title="{{ $product->name }}">
                                            {{ Str::limit($product->name, 50) }}
                                        </a>
                                    </h6>

                                    {{-- Rating --}}
                                    @php
                                        $avgRating = $product->reviews ? $product->reviews->avg('rating') : 0;
                                        $reviewCount = $product->reviews ? $product->reviews->count() : 0;
                                    @endphp
                                    @if($reviewCount > 0)
                                    <div class="mb-2">
                                        <span class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= round($avgRating) ? 'tio-star' : 'tio-star-outlined' }}" style="font-size: 12px;"></i>
                                            @endfor
                                        </span>
                                        <small class="text-muted">({{ $reviewCount }})</small>
                                    </div>
                                    @endif

                                    <!-- Price -->
                                    @php
                                        $discount = 0;
                                        if($product->discount_type == 'percent'){
                                            $discount = ($product->unit_price * $product->discount) / 100;
                                        } else {
                                            $discount = $product->discount ?? 0;
                                        }
                                        $final_price = $product->unit_price - $discount;
                                    @endphp

                                    <div class="mt-auto">
                                        <div class="d-flex align-items-center flex-wrap" style="gap: 0.25rem;">
                                            <span class="font-weight-bold text-primary">
                                                {{ webCurrencyConverter($final_price) }}
                                            </span>
                                            @if($product->discount > 0)
                                                <span class="text-muted text-decoration-line-through" style="font-size: 12px;">
                                                    {{ webCurrencyConverter($product->unit_price) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-4 d-flex justify-content-center">
                    @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->hasPages())
                        {{ $products->appends(request()->query())->links() }}
                    @endif
                </div>

            @else
                <!-- No Results -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="tio-search display-1 text-muted"></i>
                    </div>
                    <h3 class="h5 font-weight-bold text-muted">{{ translate('No products found for') }} "{{ $query }}"</h3>
                    <p class="text-muted mb-4">{{ translate('Try adjusting your search or filters to find what you\'re looking for.') }}</p>

                    {{-- Suggestions --}}
                    <div class="mb-4">
                        <p class="font-weight-bold mb-2">{{ translate('Suggestions:') }}</p>
                        <ul class="list-unstyled text-muted">
                            <li><i class="tio-checkmark-circle"></i> {{ translate('Check your spelling') }}</li>
                            <li><i class="tio-checkmark-circle"></i> {{ translate('Try more general keywords') }}</li>
                            <li><i class="tio-checkmark-circle"></i> {{ translate('Try different keywords') }}</li>
                        </ul>
                    </div>

                    @if(!empty($suggestions) && count($suggestions) > 0)
                    <div class="mb-4">
                        <p class="text-muted mb-2">{{ translate('Try searching for:') }}</p>
                        @foreach($suggestions as $suggestion)
                            <a href="{{ route('search', ['q' => $suggestion]) }}" class="btn btn-outline-primary btn-sm mr-1 mb-1">
                                {{ $suggestion }}
                            </a>
                        @endforeach
                    </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="tio-home"></i> {{ translate('Back to Home') }}
                        </a>
                        <a href="{{ route('products') }}" class="btn btn-outline-primary ml-2">
                            <i class="tio-category"></i> {{ translate('Browse All Products') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Mobile Filter Drawer --}}
<div class="d-lg-none">
    <button class="btn btn-primary position-fixed rounded-pill shadow-lg px-4"
            data-toggle="modal" data-target="#filterModal"
            style="z-index: 1000; bottom: 20px; left: 50%; transform: translateX(-50%);">
        <i class="tio-filter-list"></i> {{ translate('Filters') }}
        @if(request()->hasAny(['brand', 'price_min', 'price_max', 'category', 'rating']))
            <span class="badge badge-danger ml-1">!</span>
        @endif
    </button>

    <div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title font-weight-bold">
                        <i class="tio-filter-list"></i> {{ translate('Filter Results') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- Quick Filters --}}
                    <div class="mb-4">
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input class="custom-control-input" type="checkbox" id="mobileInStock"
                                   {{ request()->boolean('in_stock') ? 'checked' : '' }}
                                   onchange="window.location='{{ route('search', array_merge(request()->all(), ['in_stock' => request()->boolean('in_stock') ? '' : '1'])) }}'">
                            <label class="custom-control-label" for="mobileInStock">{{ translate('In Stock') }}</label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input class="custom-control-input" type="checkbox" id="mobileDiscount"
                                   {{ request()->boolean('discount') ? 'checked' : '' }}
                                   onchange="window.location='{{ route('search', array_merge(request()->all(), ['discount' => request()->boolean('discount') ? '' : '1'])) }}'">
                            <label class="custom-control-label" for="mobileDiscount">{{ translate('Discount') }}</label>
                        </div>
                    </div>

                    {{-- Brands --}}
                    @if(!empty($facets['brands']) && count($facets['brands']) > 0)
                    <div class="mb-4">
                        <label class="font-weight-bold mb-2">{{ translate('Brands') }}</label>
                        <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                            @foreach($facets['brands'] as $brandName => $count)
                                <a href="{{ route('search', array_merge(request()->all(), ['brand' => $brandName])) }}"
                                   class="badge {{ request('brand') == $brandName ? 'badge-primary' : 'badge-light text-dark border' }} text-decoration-none py-2 px-3">
                                    {{ $brandName }} ({{ $count }})
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Price Ranges --}}
                    @if(!empty($facets['price_ranges']) && count($facets['price_ranges']) > 0)
                    <div class="mb-4">
                        <label class="font-weight-bold mb-2">{{ translate('Price') }}</label>
                        <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                            @foreach($facets['price_ranges'] as $range)
                                <a href="{{ route('search', array_merge(request()->except(['price_min', 'price_max']), ['q' => $query, 'price_min' => $range['min'], 'price_max' => $range['max'] ?? ''])) }}"
                                   class="badge badge-light text-dark border text-decoration-none py-2 px-3">
                                    {{ $range['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Clear Filters --}}
                    @if(request()->hasAny(['brand', 'price_min', 'price_max', 'category', 'rating', 'in_stock', 'discount']))
                        <div class="mt-4">
                            <a href="{{ route('search', ['q' => $query]) }}" class="btn btn-outline-danger btn-block">
                                <i class="tio-clear-circle"></i> {{ translate('Clear All Filters') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
