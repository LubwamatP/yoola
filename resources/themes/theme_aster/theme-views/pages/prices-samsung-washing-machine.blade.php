@extends('theme-views.layouts.app')
@section('title', 'Samsung Washing Machine Prices Uganda 2026 | 40% Cheaper | Yoola')
@section('meta')
<meta name="description" content="Samsung washing machine prices in Uganda. Twin tub, automatic & front load. Up to 40% cheaper than competitors! Free delivery Kampala.">
<link rel="canonical" href="https://yoola.ug/prices/samsung-washing-machine">
@endsection
@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb small"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item"><a href="/products">Products</a></li><li class="breadcrumb-item active">Samsung Washing Machine</li></ol></nav>
    <div class="text-center mb-4">
        <h1 class="fw-bold" style="color: var(--bs-primary);">Samsung Washing Machine Prices Uganda</h1>
        <p class="text-muted">Up to 40% Cheaper than Competitors!</p>
        <span class="badge bg-danger fs-6 px-3 py-2">40% CHEAPER than Jumia</span>
    </div>
    <div class="alert alert-success mb-4"><h5 class="mb-0">💰 Why Pay More?</h5>Samsung 9kg Twin Tub: <strong>UGX 1,076,000</strong> (Others charge 1,800,000!)</div>
    <h2 class="h5 mb-3">Samsung Washing Machines</h2>
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
        <div class="col-12"><div class="alert alert-info">No Samsung washing machines currently available. <a href="https://wa.me/256704229768">Contact us</a>.</div></div>
        @endforelse
    </div>
    <div class="text-center py-4"><a href="https://wa.me/256704229768?text=Samsung%20washing%20machine" class="btn btn-lg" style="background:#25D366;color:white"><i class="bi bi-whatsapp me-2"></i> Order on WhatsApp</a></div>
</div>
@endsection
