@extends('theme-views.layouts.app')

@section('title', $category->seo?->title ?? $category->name . ' - Best Prices in Uganda | Yoola')

@push('meta-tags')
    <meta name="description" content="{{ $category->seo?->description ?? 'Shop ' . $category->name . ' at the best prices in Uganda. Genuine products, warranty included, fast delivery in Kampala.' }}">
    <meta property="og:title" content="{{ $category->seo?->title ?? $category->name . ' | Yoola Uganda' }}">
    <meta property="og:description" content="{{ $category->seo?->description ?? 'Shop ' . $category->name . ' at best prices' }}">
    @if($category->hero_image_full_url)
        <meta property="og:image" content="{{ $category->hero_image_full_url['path'] ?? '' }}">
    @endif
@endpush

@section('content')
<div class="category-landing-page">
    
    {{-- PROMO BANNER (if set) --}}
    @if($category->promo_banner_text)
    <div class="promo-banner text-center py-2" style="background-color: {{ $category->promo_banner_color ?? '#dc3545' }}">
        <span class="text-white fw-semibold">
            <i class="bi bi-lightning-fill"></i> {{ $category->promo_banner_text }}
        </span>
    </div>
    @endif

    {{-- HERO SECTION --}}
    <section class="hero-section position-relative overflow-hidden" 
             style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); min-height: 400px;">
        @if($category->hero_image_full_url)
            <div class="hero-bg position-absolute w-100 h-100" 
                 style="background-image: url('{{ $category->hero_image_full_url['path'] }}'); 
                        background-size: cover; background-position: center; opacity: 0.3;"></div>
        @endif
        
        <div class="container position-relative py-5">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-7">
                    {{-- Breadcrumb --}}
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb breadcrumb-light mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Home</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ $category->name }}</li>
                        </ol>
                    </nav>

                    {{-- Hero Title --}}
                    <h1 class="display-4 fw-bold text-white mb-3">
                        {{ $category->hero_title ?? $category->name }}
                    </h1>
                    
                    @if($category->hero_subtitle)
                    <p class="lead text-white-50 mb-4">{{ $category->hero_subtitle }}</p>
                    @else
                    <p class="lead text-white-50 mb-4">
                        Shop the best {{ strtolower($category->name) }} at unbeatable prices in Uganda. 
                        Genuine products with warranty.
                    </p>
                    @endif

                    {{-- Stats Row --}}
                    <div class="d-flex flex-wrap gap-4 mb-4">
                        <div class="text-white">
                            <span class="h3 fw-bold text-warning">{{ $totalProducts ?? $products->total() }}+</span>
                            <span class="text-white-50 ms-1">Products</span>
                        </div>
                        @if($category->avg_rating_display)
                        <div class="text-white">
                            <span class="h3 fw-bold text-warning">{{ number_format($category->avg_rating_display, 1) }}</span>
                            <span class="text-white-50 ms-1"><i class="bi bi-star-fill text-warning"></i> Rating</span>
                        </div>
                        @endif
                        @if($category->review_count_display)
                        <div class="text-white">
                            <span class="h3 fw-bold text-warning">{{ number_format($category->review_count_display) }}+</span>
                            <span class="text-white-50 ms-1">Reviews</span>
                        </div>
                        @endif
                    </div>

                    {{-- CTA Buttons --}}
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#products" class="btn btn-warning btn-lg px-4 fw-semibold">
                            <i class="bi bi-grid"></i> {{ $category->hero_cta_text ?? 'Browse Products' }}
                        </a>
                        <a href="{{ $category->whatsapp_link }}" target="_blank" class="btn btn-success btn-lg px-4 fw-semibold">
                            <i class="bi bi-whatsapp"></i> Chat on WhatsApp
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 d-none d-lg-block">
                    {{-- Featured Product Preview or Category Image --}}
                    @if(isset($featuredProduct))
                    <div class="card border-0 shadow-lg bg-white rounded-4 p-3">
                        <div class="badge bg-danger position-absolute top-0 start-0 m-3">Featured</div>
                        <img src="{{ getStorageImages(path: $featuredProduct->thumbnail_full_url, type: 'product') }}" 
                             class="card-img-top rounded-3" alt="{{ $featuredProduct->name }}">
                        <div class="card-body text-center">
                            <h5 class="fw-bold">{{ Str::limit($featuredProduct->name, 40) }}</h5>
                            <p class="h4 text-primary fw-bold mb-0">{{ webCurrencyConverter($featuredProduct->unit_price) }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- TRUST BADGES BAR --}}
    @if($category->trust_badges && count($category->trust_badges) > 0)
    <section class="trust-badges-section bg-light py-3 border-bottom">
        <div class="container">
            <div class="row justify-content-center">
                @foreach(collect($category->trust_badges)->where('active', true)->take(6) as $badge)
                <div class="col-6 col-md-4 col-lg-2 text-center py-2">
                    <i class="bi {{ $badge['icon'] ?? 'bi-check-circle' }} text-primary fs-4"></i>
                    <div class="small fw-semibold text-dark">{{ $badge['text'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- VALUE PROPOSITION SECTION (Hormozi-style) --}}
    @if($category->value_props && count($category->value_props) > 0)
    <section class="value-props-section py-5 bg-white">
        <div class="container">
            @if($category->value_prop_headline)
            <h2 class="text-center fw-bold mb-4">{{ $category->value_prop_headline }}</h2>
            @else
            <h2 class="text-center fw-bold mb-4">Why Buy {{ $category->name }} from Yoola?</h2>
            @endif
            
            <div class="row g-4">
                @foreach($category->value_props as $prop)
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                <i class="bi {{ $prop['icon'] ?? 'bi-star' }} text-primary fs-2"></i>
                            </div>
                            <h5 class="fw-bold">{{ $prop['title'] }}</h5>
                            <p class="text-muted mb-0">{{ $prop['description'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- CUSTOM CONTENT TOP --}}
    @if($category->content_top)
    <section class="custom-content-top py-4 bg-light">
        <div class="container">
            {!! $category->content_top !!}
        </div>
    </section>
    @endif

    {{-- URGENCY BANNER --}}
    @if($category->urgency_text)
    <div class="urgency-banner bg-warning text-dark text-center py-2 fw-semibold">
        <i class="bi bi-clock-history"></i> {{ $category->urgency_text }}
    </div>
    @endif

    {{-- MAIN PRODUCTS SECTION --}}
    <section id="products" class="products-section py-5">
        <div class="container">
            <div class="row g-4">
                {{-- Filters Sidebar --}}
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                        <div class="card-body">
                            <h5 class="fw-bold mb-4"><i class="bi bi-funnel"></i> {{ translate('Filter') }}</h5>
                            
                            {{-- Price Range --}}
                            <div class="mb-4">
                                <label class="fw-semibold mb-2">{{ translate('Price Range') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="number" class="form-control form-control-sm" placeholder="Min" id="price_min" value="{{ request('price_min') }}">
                                    <input type="number" class="form-control form-control-sm" placeholder="Max" id="price_max" value="{{ request('price_max') }}">
                                </div>
                                <button class="btn btn-outline-primary btn-sm w-100 mt-2" onclick="applyPriceFilter()">
                                    {{ translate('Apply') }}
                                </button>
                            </div>

                            {{-- Brands --}}
                            @if(isset($brands) && $brands->count() > 0)
                            <div class="mb-4">
                                <label class="fw-semibold mb-2">{{ translate('Brands') }}</label>
                                <div style="max-height: 200px; overflow-y: auto;">
                                    @foreach($brands as $brand)
                                    <div class="form-check">
                                        <input class="form-check-input brand-filter" type="checkbox" 
                                               value="{{ $brand->id }}" id="brand_{{ $brand->id }}"
                                               {{ in_array($brand->id, (array)request('brand_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="brand_{{ $brand->id }}">
                                            {{ $brand->name }} <span class="text-muted">({{ $brand->products_count ?? 0 }})</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Subcategories --}}
                            @if($category->childes->count() > 0)
                            <div class="mb-4">
                                <label class="fw-semibold mb-2">{{ translate('Subcategories') }}</label>
                                @foreach($category->childes as $child)
                                <a href="{{ route('category-products', $child->slug) }}" 
                                   class="d-block py-1 text-decoration-none {{ request('sub_category_id') == $child->id ? 'text-primary fw-bold' : 'text-dark' }}">
                                    {{ $child->name }}
                                </a>
                                @endforeach
                            </div>
                            @endif

                            {{-- In Stock Only --}}
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="inStockOnly" 
                                       {{ request('in_stock') ? 'checked' : '' }}
                                       onchange="window.location.href='{{ request()->fullUrlWithQuery(['in_stock' => request('in_stock') ? '' : 1]) }}'">
                                <label class="form-check-label" for="inStockOnly">
                                    {{ translate('In Stock Only') }}
                                </label>
                            </div>

                            {{-- On Sale --}}
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="onSaleOnly"
                                       {{ request('discount') ? 'checked' : '' }}
                                       onchange="window.location.href='{{ request()->fullUrlWithQuery(['discount' => request('discount') ? '' : 1]) }}'">
                                <label class="form-check-label" for="onSaleOnly">
                                    {{ translate('On Sale') }}
                                </label>
                            </div>

                            {{-- Clear Filters --}}
                            <a href="{{ route('category-products', $category->slug) }}" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-x-circle"></i> {{ translate('Clear Filters') }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Products Grid --}}
                <div class="col-lg-9">
                    {{-- Sort Bar --}}
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div>
                            <span class="text-muted">{{ translate('Showing') }} {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} {{ translate('of') }} {{ $products->total() }} {{ translate('products') }}</span>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <label class="mb-0 text-nowrap">{{ translate('Sort by:') }}</label>
                            <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
                                <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'popularity']) }}" {{ request('sort_by') == 'popularity' ? 'selected' : '' }}>{{ translate('Popularity') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'low-high']) }}" {{ request('sort_by') == 'low-high' ? 'selected' : '' }}>{{ translate('Price: Low to High') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'high-low']) }}" {{ request('sort_by') == 'high-low' ? 'selected' : '' }}>{{ translate('Price: High to Low') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'latest']) }}" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>{{ translate('Newest') }}</option>
                                <option value="{{ request()->fullUrlWithQuery(['sort_by' => 'rating']) }}" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>{{ translate('Rating') }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- Products --}}
                    @if($products->count() > 0)
                    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                        @foreach($products as $product)
                        <div class="col">
                            @include('theme-views.partials._product-card-landing', ['product' => $product])
                        </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-box-seam display-1 text-muted"></i>
                        <h4 class="mt-3">{{ translate('No products found') }}</h4>
                        <p class="text-muted">{{ translate('Try adjusting your filters') }}</p>
                        <a href="{{ route('category-products', $category->slug) }}" class="btn btn-primary">
                            {{ translate('Clear Filters') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- BUYING GUIDE SECTION --}}
    @if($category->buying_guide)
    <section class="buying-guide-section py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-lg-5">
                            <h2 class="fw-bold mb-4">
                                <i class="bi bi-journal-bookmark text-primary"></i> 
                                {{ $category->buying_guide_title ?? 'How to Choose the Right ' . $category->name }}
                            </h2>
                            <div class="buying-guide-content">
                                {!! Str::markdown($category->buying_guide) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- FAQ SECTION (SEO Gold) --}}
    @if($category->faqs && count($category->faqs) > 0)
    <section class="faq-section py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="text-center fw-bold mb-4">
                        <i class="bi bi-question-circle text-primary"></i> 
                        {{ translate('Frequently Asked Questions') }}
                    </h2>
                    
                    <div class="accordion" id="faqAccordion">
                        @foreach($category->faqs as $index => $faq)
                        <div class="accordion-item border-0 mb-2 shadow-sm">
                            <h3 class="accordion-header">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }} fw-semibold" 
                                        type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#faq{{ $index }}">
                                    {{ $faq['question'] }}
                                </button>
                            </h3>
                            <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    {{ $faq['answer'] }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ Schema for SEO --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            @foreach($category->faqs as $index => $faq)
            {
                "@type": "Question",
                "name": "{{ $faq['question'] }}",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "{{ $faq['answer'] }}"
                }
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ]
    }
    </script>
    @endif

    {{-- CUSTOM CONTENT BOTTOM --}}
    @if($category->content_bottom)
    <section class="custom-content-bottom py-5 bg-light">
        <div class="container">
            {!! $category->content_bottom !!}
        </div>
    </section>
    @endif

    {{-- FINAL CTA --}}
    <section class="final-cta-section py-5 bg-primary text-white text-center">
        <div class="container">
            <h2 class="fw-bold mb-3">Ready to Order?</h2>
            <p class="lead mb-4">Get the best {{ strtolower($category->name) }} delivered to your doorstep in Kampala</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ $category->whatsapp_link }}" target="_blank" class="btn btn-success btn-lg px-4">
                    <i class="bi bi-whatsapp"></i> Order via WhatsApp
                </a>
                <a href="tel:{{ getWebConfig(name: 'phone') }}" class="btn btn-outline-light btn-lg px-4">
                    <i class="bi bi-telephone"></i> Call Now
                </a>
            </div>
        </div>
    </section>

    {{-- FLOATING WHATSAPP BUTTON --}}
    @if($category->show_whatsapp_float)
    <a href="{{ $category->whatsapp_link }}" target="_blank" 
       class="whatsapp-float position-fixed shadow-lg rounded-circle d-flex align-items-center justify-content-center"
       style="bottom: 20px; right: 20px; width: 60px; height: 60px; background: #25D366; z-index: 1000;">
        <i class="bi bi-whatsapp text-white fs-3"></i>
    </a>
    @endif
</div>

@push('css_or_js')
<style>
.hover-shadow { transition: all 0.3s ease; }
.hover-shadow:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important; }
.transition-all { transition: all 0.3s ease; }
.min-vh-50 { min-height: 50vh; }
.breadcrumb-light .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.5); }
.whatsapp-float:hover { transform: scale(1.1); }
.buying-guide-content h3 { font-size: 1.25rem; margin-top: 1.5rem; }
.buying-guide-content ul { padding-left: 1.5rem; }
</style>
@endpush

@push('script')
<script>
function applyPriceFilter() {
    const min = document.getElementById('price_min').value;
    const max = document.getElementById('price_max').value;
    const url = new URL(window.location.href);
    if (min) url.searchParams.set('price_min', min);
    else url.searchParams.delete('price_min');
    if (max) url.searchParams.set('price_max', max);
    else url.searchParams.delete('price_max');
    window.location.href = url.toString();
}

// Brand filter
document.querySelectorAll('.brand-filter').forEach(cb => {
    cb.addEventListener('change', function() {
        const checked = Array.from(document.querySelectorAll('.brand-filter:checked')).map(c => c.value);
        const url = new URL(window.location.href);
        url.searchParams.delete('brand_ids[]');
        checked.forEach(id => url.searchParams.append('brand_ids[]', id));
        window.location.href = url.toString();
    });
});
</script>
@endpush
@endsection
