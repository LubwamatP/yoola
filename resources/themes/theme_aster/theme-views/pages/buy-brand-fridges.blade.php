@extends('theme-views.layouts.app')
@section('title', 'Buy {{ $brand }} Fridges in Uganda | Best Prices | Yoola')
@push('css_or_js')
<meta name="description" content="Buy genuine {{ $brand }} fridges & refrigerators in Uganda. {{ $brandInfo['tagline'] }}. Free delivery Kampala & Wakiso.">
<link rel="canonical" href="https://yoola.ug/buy/{{ strtolower($brand) }}-fridges-uganda">
@endpush
@section('content')
<nav class="bg-light py-2"><div class="container"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ url('/buy/fridges-uganda') }}">Fridges</a></li><li class="breadcrumb-item active">{{ $brand }}</li></ol></div></nav>
<div class="bg-primary text-white py-4"><div class="container"><h1 class="fw-bold text-white mb-2">Buy {{ $brand }} Fridges in Uganda</h1><p class="text-white-50 mb-0">{{ $products->count() }} {{ $brand }} Fridges - {{ $brandInfo['tagline'] }}</p></div></div>
<div class="bg-light border-bottom py-2"><div class="container text-center small"><strong>✓ Genuine {{ $brand }}</strong> | <strong>✓ {{ $brandInfo['warranty'] }} Warranty</strong> | <strong>✓ Free Delivery</strong></div></div>
<div class="container py-4">
<div class="card mb-4 border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">❄️ About {{ $brand }} Fridges</h2></div><div class="card-body">
<p class="mb-2"><strong>Origin:</strong> {{ $brandInfo['origin'] }}</p>
<p class="mb-0"><strong>Warranty:</strong> {{ $brandInfo['warranty'] }} manufacturer warranty + compressor warranty up to 10 years</p>
</div></div>
<h2 class="h5 mb-3">All {{ $brand }} Fridges</h2>
<div class="row g-3 mb-4">
@forelse($products as $product)
<div class="col-6 col-md-4 col-lg-3"><div class="card h-100 shadow-sm"><a href="{{ route('product', $product->slug) }}"><img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" class="card-img-top p-2" alt="{{ $product->name }}" loading="lazy" style="height:150px;object-fit:contain;"></a><div class="card-body p-2"><h3 class="small mb-1" style="font-size:0.8rem;">{{ Str::limit($product->name, 42) }}</h3><p class="text-primary fw-bold mb-2">{{ webCurrencyConverter($product->unit_price) }}</p><a href="https://wa.me/256704229768?text=Order: {{ urlencode($product->name) }}" class="btn btn-success btn-sm w-100">Order</a></div></div></div>
@empty<p class="text-muted">No {{ $brand }} fridges available.</p>@endforelse
</div>
<div class="card mb-4 border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h6 mb-0">🏷️ Other Fridge Brands</h2></div><div class="card-body"><div class="d-flex flex-wrap gap-2">
<a href="{{ url('/buy/hisense-fridges-uganda') }}" class="btn btn-outline-primary btn-sm">Hisense</a>
<a href="{{ url('/buy/samsung-fridges-uganda') }}" class="btn btn-outline-primary btn-sm">Samsung</a>
<a href="{{ url('/buy/lg-fridges-uganda') }}" class="btn btn-outline-primary btn-sm">LG</a>
</div></div></div>
<div class="card bg-success text-white"><div class="card-body text-center py-3"><p class="mb-2">Need help? <a href="https://wa.me/256704229768" class="btn btn-light btn-sm ms-2">WhatsApp Us</a></p></div></div>
</div>
@endsection
