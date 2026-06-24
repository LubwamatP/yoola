@extends('theme-views.layouts.app')
@section('title', 'Cheap TV in Uganda 2026 | Budget TVs from UGX 400K | Yoola')
@section('meta')
<meta name="description" content="Cheap TVs in Uganda from UGX 400,000. Budget Samsung, Hisense, CHiQ TVs. Free delivery Kampala. Genuine with warranty.">
<link rel="canonical" href="https://yoola.ug/deals/cheap-tv-uganda">
@endsection
@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb small"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Cheap TVs</li></ol></nav>
    <div class="text-center mb-4">
        <h1 class="fw-bold" style="color: var(--bs-primary);">Cheap TVs in Uganda</h1>
        <p class="text-muted">Quality TVs at Budget-Friendly Prices — From UGX 400,000</p>
        <div class="alert alert-warning d-inline-block mt-2">💡 Cheap does NOT mean fake! All TVs are brand new with warranty.</div>
    </div>
    <h2 class="h5 mb-3">Budget TVs Under 1.5M</h2>
    <div class="row g-3 mb-5">
        @forelse($products as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100">
                @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)
                <span class="position-absolute badge bg-danger" style="top:10px;left:10px;z-index:10">-{{ getProductPriceByType(product: $product, type: 'discount', result: 'string') }}</span>
                @endif
                <a href="{{ route('product', $product->slug) }}"><img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" class="card-img-top p-2" alt="{{ $product->name }}" style="height:180px;object-fit:contain;"></a>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title small text-truncate">{{ Str::limit($product->name, 35) }}</h6>
                    <div class="mt-auto">
                        @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)<del class="text-muted small">{{ webCurrencyConverter($product->unit_price) }}</del>@endif
                        <p class="text-primary fw-bold mb-2">{{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}</p>
                        <a href="https://wa.me/256704229768?text=Hi!%20I%20want%20{{ urlencode($product->name) }}" class="btn btn-success btn-sm w-100"><i class="bi bi-whatsapp me-1"></i> Order</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12"><div class="alert alert-info">No budget TVs available. <a href="https://wa.me/256704229768">Contact us</a>.</div></div>
        @endforelse
    </div>
    <div class="card border-0 mb-4" style="background:linear-gradient(135deg,var(--bs-primary),#8B0000);">
        <div class="card-body text-white p-4 text-center">
            <h3 class="h5 text-white mb-3">Why Buy Cheap TVs from Yoola?</h3>
            <div class="row"><div class="col-6 col-md-3 mb-2"><i class="bi bi-patch-check fs-3"></i><br><small>Genuine</small></div><div class="col-6 col-md-3 mb-2"><i class="bi bi-shield-check fs-3"></i><br><small>Warranty</small></div><div class="col-6 col-md-3 mb-2"><i class="bi bi-truck fs-3"></i><br><small>Free Delivery</small></div><div class="col-6 col-md-3 mb-2"><i class="bi bi-cash fs-3"></i><br><small>Pay on Delivery</small></div></div>
        </div>
    </div>
    <div class="text-center py-4"><a href="https://wa.me/256704229768?text=I%20need%20a%20cheap%20TV.%20Budget:" class="btn btn-lg" style="background:#25D366;color:white"><i class="bi bi-whatsapp me-2"></i> Get Recommendations</a></div>
</div>
@endsection
