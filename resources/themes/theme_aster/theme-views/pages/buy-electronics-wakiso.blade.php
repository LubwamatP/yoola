@extends('theme-views.layouts.app')
@section('title', 'Buy Electronics in Wakiso | Free Delivery | Yoola')
@push('css_or_js')
<meta name="description" content="Buy genuine electronics in Wakiso with free next-day delivery. TVs, fridges, washing machines, ACs & more. Pay on delivery.">
<link rel="canonical" href="https://yoola.ug/buy/electronics-wakiso">
@endpush
@section('content')
<nav class="bg-light py-2"><div class="container"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Electronics Wakiso</li></ol></div></nav>
<div class="bg-primary text-white py-4"><div class="container"><h1 class="fw-bold text-white mb-2">Buy Electronics in Wakiso</h1><p class="text-white-50 mb-0">Free next-day delivery - Pay on delivery</p></div></div>
<div class="bg-light border-bottom py-2"><div class="container text-center small"><strong>✓ Genuine Products</strong> | <strong>✓ Free Delivery</strong> | <strong>✓ Pay on Delivery</strong></div></div>
<div class="container py-4">
<h2 class="h5 mb-4">Shop by Category</h2>
<div class="row g-3 mb-4">
@foreach($categories as $cat)
<div class="col-6 col-md-4"><a href="{{ url($cat['url']) }}" class="card h-100 shadow-sm text-decoration-none"><div class="card-body text-center py-4"><div class="display-4 mb-2">{{ $cat['icon'] }}</div><h3 class="h6 mb-0">{{ $cat['name'] }}</h3></div></a></div>
@endforeach
</div>
<div class="card mb-4 border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">📍 Wakiso Delivery Areas</h2></div><div class="card-body">
<p class="mb-2"><strong>Free next-day delivery:</strong> Nansana, Kasangati, Kira, Gayaza, Entebbe Road, Bweyogerere, Namugongo</p>
<p class="mb-0"><strong>Same-day available:</strong> Order before 10 AM for same-day delivery (extra UGX 10,000)</p>
</div></div>
<div class="card mb-4 border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h6 mb-0">❓ FAQ</h2></div><div class="card-body"><div class="accordion accordion-flush" id="faq">
<div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button py-2" type="button" data-bs-toggle="collapse" data-bs-target="#f1">How long is delivery to Wakiso?</button></h3><div id="f1" class="accordion-collapse collapse show" data-bs-parent="#faq"><div class="accordion-body small py-2">Next-day delivery for all Wakiso areas. Same-day available for UGX 10,000 extra.</div></div></div>
</div></div></div>
<div class="card bg-success text-white"><div class="card-body text-center py-3"><p class="mb-2">Ready to order? <a href="https://wa.me/256704229768" class="btn btn-light btn-sm ms-2">WhatsApp Us</a></p></div></div>
</div>
@endsection
