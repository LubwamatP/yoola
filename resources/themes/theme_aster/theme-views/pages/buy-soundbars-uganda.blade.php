@extends('theme-views.layouts.app')
@section('title', 'Buy Soundbars in Uganda | Best Prices | Yoola')
@push('css_or_js')
<meta name="description" content="Buy genuine soundbars in Uganda. Upgrade your TV audio. Free delivery Kampala & Wakiso.">
<link rel="canonical" href="https://yoola.ug/buy/soundbars-uganda">
@endpush
@section('content')
<nav class="bg-light py-2"><div class="container"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Soundbars</li></ol></div></nav>
<div class="bg-primary text-white py-4"><div class="container"><h1 class="fw-bold text-white mb-2">Buy Soundbars in Uganda</h1><p class="text-white-50 mb-0">{{ $products->count() }} Soundbars - Upgrade your TV audio</p></div></div>
<div class="bg-light border-bottom py-2"><div class="container text-center small"><strong>✓ Genuine</strong> | <strong>✓ Free Delivery</strong> | <strong>✓ Warranty</strong></div></div>
<div class="container py-4">
<h2 class="h5 mb-3">All Soundbars</h2>
<div class="row g-3 mb-4">
@forelse($products as $product)
<div class="col-6 col-md-4 col-lg-3"><div class="card h-100 shadow-sm"><a href="{{ route('product', $product->slug) }}"><img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" class="card-img-top p-2" alt="{{ $product->name }}" loading="lazy" style="height:150px;object-fit:contain;"></a><div class="card-body p-2"><h3 class="small mb-1" style="font-size:0.8rem;">{{ Str::limit($product->name, 42) }}</h3><p class="text-primary fw-bold mb-2">{{ webCurrencyConverter($product->unit_price) }}</p><a href="https://wa.me/256704229768?text=Order: {{ urlencode($product->name) }}" class="btn btn-success btn-sm w-100">Order</a></div></div></div>
@empty<p class="text-muted">No soundbars available.</p>@endforelse
</div>
<div class="card bg-success text-white"><div class="card-body text-center py-3"><p class="mb-2">Need help? <a href="https://wa.me/256704229768" class="btn btn-light btn-sm ms-2">WhatsApp Us</a></p></div></div>
</div>
@endsection
