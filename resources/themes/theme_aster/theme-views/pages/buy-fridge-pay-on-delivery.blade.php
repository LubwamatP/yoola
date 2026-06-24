@extends('theme-views.layouts.app')
@section('title', 'Buy Fridge Pay on Delivery Uganda | No Deposit | Yoola')
@section('meta')
<meta name="description" content="Buy fridge with pay on delivery in Uganda. No upfront payment. Inspect before paying. Samsung, Hisense fridges.">
<link rel="canonical" href="https://yoola.ug/buy/fridge-pay-on-delivery">
@endsection
@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb small"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Fridge Pay on Delivery</li></ol></nav>
    <div class="text-center mb-4">
        <h1 class="fw-bold" style="color: var(--bs-primary);">Buy Fridge — Pay on Delivery</h1>
        <p class="text-muted">No upfront payment. Inspect before paying. Zero risk.</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
            <span class="badge bg-success px-3 py-2">No Deposit</span>
            <span class="badge bg-primary px-3 py-2">Inspect First</span>
            <span class="badge bg-warning text-dark px-3 py-2">Free Delivery</span>
        </div>
    </div>
    <div class="alert alert-success mb-4">😰 <strong>Worried about scams?</strong> You only pay AFTER you receive and inspect your fridge. No risk.</div>
    <h2 class="h5 mb-3">Fridges & Freezers</h2>
    <div class="row g-3 mb-5">
        @forelse($products as $product)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100">
                <span class="position-absolute badge bg-success" style="top:10px;right:10px">Pay on Delivery</span>
                <a href="{{ route('product', $product->slug) }}"><img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" class="card-img-top p-2" alt="{{ $product->name }}" style="height:180px;object-fit:contain;"></a>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title small text-truncate">{{ Str::limit($product->name, 35) }}</h6>
                    <div class="mt-auto">
                        <p class="text-primary fw-bold mb-2">{{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}</p>
                        <a href="https://wa.me/256704229768?text=Hi!%20Pay%20on%20delivery%20for%20{{ urlencode($product->name) }}" class="btn btn-success btn-sm w-100"><i class="bi bi-whatsapp me-1"></i> Order</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12"><div class="alert alert-info">No fridges available. <a href="https://wa.me/256704229768">Contact us</a>.</div></div>
        @endforelse
    </div>
    <div class="text-center py-4"><a href="https://wa.me/256704229768?text=fridge%20pay%20on%20delivery" class="btn btn-lg" style="background:#25D366;color:white"><i class="bi bi-whatsapp me-2"></i> WhatsApp Us</a></div>
</div>
@endsection
