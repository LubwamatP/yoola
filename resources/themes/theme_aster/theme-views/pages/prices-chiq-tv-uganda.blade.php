@extends('theme-views.layouts.app')

@section('title', 'CHiQ TV Prices in Uganda 2026 | Best Deals | Yoola')

@section('meta')
<meta name="description" content="Compare CHiQ TV prices in Uganda. From 32 inch to 65 inch Smart TVs. Free delivery in Kampala. Pay on delivery. Genuine products with warranty.">
<meta name="keywords" content="CHiQ TV price Uganda, CHiQ TV Uganda, buy CHiQ TV Kampala, CHiQ smart TV price">
<link rel="canonical" href="https://yoola.ug/prices/chiq-tv-uganda">
@endsection

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="/products">Products</a></li>
            <li class="breadcrumb-item active">CHiQ TV Prices</li>
        </ol>
    </nav>

    <div class="text-center mb-4">
        <h1 class="fw-bold" style="color: var(--bs-primary);">CHiQ TV Prices in Uganda 2026</h1>
        <p class="text-muted">Compare prices and find the best deals on CHiQ TVs</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
            <span class="badge bg-success px-3 py-2">Free Delivery Kampala</span>
            <span class="badge bg-primary px-3 py-2">Pay on Delivery</span>
            <span class="badge bg-warning text-dark px-3 py-2">Full Warranty</span>
        </div>
    </div>

    <h2 class="h5 mb-3">CHiQ TVs Available</h2>
    <div class="row g-3 mb-5">
        @forelse($products as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 product-card">
                @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                <span class="position-absolute badge bg-danger" style="top:10px;left:10px;z-index:10">
                    -{{ getProductPriceByType(product: $product, type: 'discount', result: 'string') }}
                </span>
                @endif
                <a href="{{ route('product', $product->slug) }}">
                    <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" 
                         class="card-img-top p-2" alt="{{ $product->name }}" 
                         style="height:180px;object-fit:contain;">
                </a>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title small text-truncate" title="{{ $product->name }}">{{ Str::limit($product->name, 35) }}</h6>
                    <div class="mt-auto">
                        @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                        <del class="text-muted small">{{ webCurrencyConverter($product->unit_price) }}</del>
                        @endif
                        <p class="text-primary fw-bold mb-2">{{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}</p>
                        <a href="https://wa.me/256704229768?text=Hi!%20I%20want%20{{ urlencode($product->name) }}" 
                           class="btn btn-success btn-sm w-100">
                            <i class="bi bi-whatsapp me-1"></i> Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">No CHiQ TVs currently available. <a href="https://wa.me/256704229768">Contact us</a> for stock updates.</div>
        </div>
        @endforelse
    </div>

    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, var(--bs-primary), #8B0000);">
        <div class="card-body text-white p-4">
            <h3 class="h5 mb-3 text-white">Why Buy CHiQ from Yoola?</h3>
            <div class="row text-center">
                <div class="col-6 col-md-3 mb-2"><i class="bi bi-truck fs-3"></i><br><small>Free Delivery</small></div>
                <div class="col-6 col-md-3 mb-2"><i class="bi bi-cash fs-3"></i><br><small>Pay on Delivery</small></div>
                <div class="col-6 col-md-3 mb-2"><i class="bi bi-shield-check fs-3"></i><br><small>Genuine Products</small></div>
                <div class="col-6 col-md-3 mb-2"><i class="bi bi-headset fs-3"></i><br><small>Support: 0704 229 768</small></div>
            </div>
        </div>
    </div>

    <div class="text-center py-4">
        <h3 class="h5 mb-3">Need Help Choosing?</h3>
        <a href="https://wa.me/256704229768?text=Hi!%20I%20need%20help%20choosing%20a%20CHiQ%20TV" class="btn btn-lg" style="background:#25D366;color:white">
            <i class="bi bi-whatsapp me-2"></i> Chat with Us
        </a>
    </div>
</div>
@endsection
