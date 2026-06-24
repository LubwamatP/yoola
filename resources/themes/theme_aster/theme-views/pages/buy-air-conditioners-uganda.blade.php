@extends('theme-views.layouts.app')
@section('title', 'Buy Air Conditioners in Uganda | Best AC Prices | Yoola')
@push('css_or_js')
<meta name="description" content="Buy genuine air conditioners in Uganda at best prices. Split ACs, portable ACs, inverter technology. Free delivery & installation Kampala & Wakiso.">
<link rel="canonical" href="https://yoola.ug/buy/air-conditioners-uganda">
@endpush
@section('content')
<nav class="bg-light py-2"><div class="container"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Air Conditioners</li></ol></div></nav>

<div class="bg-primary text-white py-4">
<div class="container">
<h1 class="fw-bold text-white mb-2">Buy Air Conditioners in Uganda</h1>
<p class="text-white-50 mb-0">{{ $products->count() }} ACs - Free delivery & installation Kampala</p>
</div>
</div>

<div class="bg-light border-bottom py-2">
<div class="container text-center small">
<strong>✓ Genuine</strong> &nbsp;|&nbsp; <strong>✓ Free Installation</strong> &nbsp;|&nbsp; <strong>✓ Warranty</strong>
</div>
</div>

<div class="container py-4">

@if(isset($acSizes) && count($acSizes) > 0)
<div class="card mb-4 border-0 shadow-sm">
<div class="card-header bg-white"><h2 class="h5 mb-0">❄️ AC Prices by BTU</h2></div>
<div class="card-body p-0">
<div class="table-responsive">
<table class="table table-sm table-striped mb-0">
<thead class="table-dark"><tr><th>Capacity</th><th>Best For</th><th>Price Range</th></tr></thead>
<tbody>
@foreach($acSizes as $ac)
<tr>
<td><strong>{{ $ac['size'] }}</strong></td>
<td>{{ $ac['use'] }}</td>
<td>{{ number_format($ac['min']) }} - {{ number_format($ac['max']) }}/=</td>
</tr>
@endforeach
</tbody>
</table>
</div>
<p class="text-muted small px-3 py-2 mb-0">Prices from our current stock</p>
</div>
</div>
@endif

<div class="alert alert-info mb-4"><a href="{{ url('/power-calculator') }}" class="alert-link">Calculate AC electricity costs →</a></div>

<h2 class="h5 mb-3">All Air Conditioners</h2>
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
<p class="text-muted">No air conditioners available.</p>
@endforelse
</div>

<div class="card mb-4 border-0 shadow-sm">
<div class="card-header bg-white"><h2 class="h6 mb-0">❓ FAQ</h2></div>
<div class="card-body">
<div class="accordion accordion-flush" id="faq">
<div class="accordion-item">
<h3 class="accordion-header"><button class="accordion-button py-2" type="button" data-bs-toggle="collapse" data-bs-target="#f1">What BTU do I need?</button></h3>
<div id="f1" class="accordion-collapse collapse show" data-bs-parent="#faq"><div class="accordion-body small py-2">Bedroom (15-25 sqm): 9,000-12,000 BTU. Living room (30-50 sqm): 18,000-24,000 BTU. Large office: 30,000+ BTU.</div></div>
</div>
<div class="accordion-item">
<h3 class="accordion-header"><button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#f2">Is installation included?</button></h3>
<div id="f2" class="accordion-collapse collapse" data-bs-parent="#faq"><div class="accordion-body small py-2">Yes! Free professional installation within Kampala & Wakiso. Our technicians will handle everything.</div></div>
</div>
<div class="accordion-item">
<h3 class="accordion-header"><button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#f3">Inverter vs non-inverter?</button></h3>
<div id="f3" class="accordion-collapse collapse" data-bs-parent="#faq"><div class="accordion-body small py-2">Inverter ACs save 30-50% on electricity and run quieter. Higher upfront cost but pays for itself in 2-3 years.</div></div>
</div>
</div>
</div>
</div>

<div class="card mb-4 border-0 shadow-sm">
<div class="card-header bg-white"><h2 class="h6 mb-0">🏷️ Brands</h2></div>
<div class="card-body">
<div class="d-flex flex-wrap gap-2">
<a href="{{ url('/brand/hisense') }}" class="btn btn-outline-primary btn-sm">Hisense</a>
<a href="{{ url('/brand/samsung') }}" class="btn btn-outline-primary btn-sm">Samsung</a>
<a href="{{ url('/brand/lg') }}" class="btn btn-outline-primary btn-sm">LG</a>
<a href="{{ url('/brand/chiq') }}" class="btn btn-outline-primary btn-sm">CHiQ</a>
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
