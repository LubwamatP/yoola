@extends('theme-views.layouts.app')
@section('title', 'Buy Electronics in Kampala | Free Delivery | Yoola')
@push('css_or_js')
<meta name="description" content="Buy genuine electronics in Kampala with free same-day delivery. TVs, fridges, washing machines, ACs, cookers & more. Pay on delivery.">
<link rel="canonical" href="https://yoola.ug/buy/electronics-kampala">
@endpush
@section('content')
<nav class="bg-light py-2"><div class="container"><ol class="breadcrumb mb-0 small"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Electronics Kampala</li></ol></div></nav>
<div class="bg-primary text-white py-4"><div class="container"><h1 class="fw-bold text-white mb-2">Buy Electronics in Kampala</h1><p class="text-white-50 mb-0">Free same-day delivery in Kampala & Wakiso - Pay on delivery</p></div></div>
<div class="bg-light border-bottom py-2"><div class="container text-center small"><strong>✓ Genuine Products</strong> | <strong>✓ Free Delivery</strong> | <strong>✓ Pay on Delivery</strong> | <strong>✓ Warranty</strong></div></div>
<div class="container py-4">
<h2 class="h5 mb-4">Shop by Category</h2>
<div class="row g-3 mb-4">
@foreach($categories as $cat)
<div class="col-6 col-md-4 col-lg-3">
<a href="{{ url($cat['url']) }}" class="card h-100 shadow-sm text-decoration-none">
<div class="card-body text-center py-4">
<div class="display-4 mb-2">{{ $cat['icon'] }}</div>
<h3 class="h6 mb-1">{{ $cat['name'] }}</h3>
<p class="text-muted small mb-0">{{ $cat['count'] }} products</p>
</div>
</a>
</div>
@endforeach
</div>
<div class="card mb-4 border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h5 mb-0">📍 Delivery Areas</h2></div><div class="card-body">
<p class="mb-2"><strong>Free same-day delivery:</strong> Kampala Central, Nakasero, Kololo, Bugolobi, Muyenga, Ntinda, Kira, Naalya, Kyanja</p>
<p class="mb-0"><strong>Free next-day delivery:</strong> Wakiso, Entebbe, Mukono, Nansana, Bweyogerere</p>
</div></div>
<div class="card mb-4 border-0 shadow-sm"><div class="card-header bg-white"><h2 class="h6 mb-0">❓ FAQ</h2></div><div class="card-body"><div class="accordion accordion-flush" id="faq">
<div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button py-2" type="button" data-bs-toggle="collapse" data-bs-target="#f1">How fast is delivery in Kampala?</button></h3><div id="f1" class="accordion-collapse collapse show" data-bs-parent="#faq"><div class="accordion-body small py-2">Same-day delivery for orders before 2 PM. Next-day for orders after 2 PM.</div></div></div>
<div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#f2">Do I pay before or after delivery?</button></h3><div id="f2" class="accordion-collapse collapse" data-bs-parent="#faq"><div class="accordion-body small py-2">Pay on delivery! Cash or Mobile Money. Inspect your item first, then pay.</div></div></div>
</div></div></div>
<div class="card bg-success text-white"><div class="card-body text-center py-3"><p class="mb-2">Ready to order? <a href="https://wa.me/256704229768" class="btn btn-light btn-sm ms-2">WhatsApp Us</a></p></div></div>
</div>
@endsection
