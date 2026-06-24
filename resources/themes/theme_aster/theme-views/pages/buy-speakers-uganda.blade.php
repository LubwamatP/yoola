@extends('theme-views.layouts.app')
@section('title', 'Buy Speakers & Sound Systems in Uganda | Best Prices | Yoola')
@push('css_or_js')
<meta name="description" content="Buy genuine speakers, soundbars, subwoofers & home theaters in Uganda. Bluetooth speakers, party speakers. Free delivery Kampala & Wakiso.">
<link rel="canonical" href="https://yoola.ug/buy/speakers-uganda">
@endpush
@section('content')
<nav class="bg-light py-2"><div class="container"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Speakers</li></ol></div></nav>

<div class="bg-primary text-white py-4">
<div class="container">
<h1 class="fw-bold text-white mb-2">Buy Speakers & Sound Systems in Uganda</h1>
<p class="text-white-50 mb-0">{{ $products->count() }} Speakers - Free delivery Kampala - Pay on delivery</p>
</div>
</div>

<div class="bg-light border-bottom py-2">
<div class="container text-center small">
<strong>✓ Genuine</strong> &nbsp;|&nbsp; <strong>✓ Free Delivery</strong> &nbsp;|&nbsp; <strong>✓ Pay on Delivery</strong> &nbsp;|&nbsp; <strong>✓ Warranty</strong>
</div>
</div>

<div class="container py-4">

@if(isset($speakerTypes) && count($speakerTypes) > 0)
<div class="card mb-4 border-0 shadow-sm">
<div class="card-header bg-white"><h2 class="h5 mb-0">🔊 Speaker Prices by Type</h2></div>
<div class="card-body p-0">
<div class="table-responsive">
<table class="table table-sm table-striped mb-0">
<thead class="table-dark"><tr><th>Type</th><th>Best For</th><th>Price Range</th></tr></thead>
<tbody>
@foreach($speakerTypes as $spk)
<tr>
<td><strong>{{ $spk['size'] }}</strong></td>
<td>{{ $spk['use'] }}</td>
<td>{{ number_format($spk['min']) }} - {{ number_format($spk['max']) }}/=</td>
</tr>
@endforeach
</tbody>
</table>
</div>
<p class="text-muted small px-3 py-2 mb-0">Prices from our current stock</p>
</div>
</div>
@endif

<h2 class="h5 mb-3">All Speakers & Sound Systems</h2>
<div class="row g-3 mb-4">
@forelse($products as $product)
<div class="col-6 col-md-4 col-lg-3">
<div class="card h-100 shadow-sm">
<a href="{{ route('product', $product->slug) }}">
<img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" class="card-img-top p-2" alt="{{ $product->name }}" loading="lazy" style="height:150px;object-fit:contain;">
</a>
<div class="card-body p-2">
<h3 class="small mb-1" style="font-size:0.8rem;">{{ Str::limit($product->name, 42) }}</h3>
<p class="text-primary fw-bold mb-2">{{ webCurrencyConverter($product->unit_price) }}</p>
<a href="https://wa.me/256704229768?text=Order: {{ urlencode($product->name) }}" class="btn btn-success btn-sm w-100">Order</a>
</div>
</div>
</div>
@empty
<p class="text-muted">No speakers available.</p>
@endforelse
</div>

<div class="card mb-4 border-0 shadow-sm">
<div class="card-header bg-white"><h2 class="h6 mb-0">❓ FAQ</h2></div>
<div class="card-body">
<div class="accordion accordion-flush" id="faq">
<div class="accordion-item">
<h3 class="accordion-header"><button class="accordion-button py-2" type="button" data-bs-toggle="collapse" data-bs-target="#f1">Soundbar or home theater?</button></h3>
<div id="f1" class="accordion-collapse collapse show" data-bs-parent="#faq"><div class="accordion-body small py-2">Soundbar: simple setup, great for small-medium rooms. Home theater: full surround sound, best for dedicated entertainment rooms.</div></div>
</div>
<div class="accordion-item">
<h3 class="accordion-header"><button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#f2">Do speakers connect to my TV?</button></h3>
<div id="f2" class="accordion-collapse collapse" data-bs-parent="#faq"><div class="accordion-body small py-2">Yes! Most connect via HDMI ARC, optical, or Bluetooth. Soundbars are the easiest option for TV audio upgrade.</div></div>
</div>
<div class="accordion-item">
<h3 class="accordion-header"><button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#f3">Best speakers for parties?</button></h3>
<div id="f3" class="accordion-collapse collapse" data-bs-parent="#faq"><div class="accordion-body small py-2">Look for subwoofers with Bluetooth. They deliver powerful bass and can be moved around easily.</div></div>
</div>
</div>
</div>
</div>

<div class="card mb-4 border-0 shadow-sm">
<div class="card-header bg-white"><h2 class="h6 mb-0">🏷️ Brands</h2></div>
<div class="card-body">
<div class="d-flex flex-wrap gap-2">
<a href="{{ url('/brand/sony') }}" class="btn btn-outline-primary btn-sm">Sony</a>
<a href="{{ url('/brand/jbl') }}" class="btn btn-outline-primary btn-sm">JBL</a>
<a href="{{ url('/brand/samsung') }}" class="btn btn-outline-primary btn-sm">Samsung</a>
<a href="{{ url('/brand/lg') }}" class="btn btn-outline-primary btn-sm">LG</a>
</div>
</div>
</div>

<div class="card bg-success text-white">
<div class="card-body text-center py-3">
<p class="mb-2">Need help choosing? <a href="https://wa.me/256704229768" class="btn btn-light btn-sm ms-2">WhatsApp Us</a></p>
</div>
</div>

</div>
@endsection
