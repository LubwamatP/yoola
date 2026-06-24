@extends('theme-views.layouts.app')
@section('title', 'Electronics Delivery to {{ $area }} | Free Delivery | Yoola Uganda')
@push('css_or_js')
<meta name="description" content="Free {{ $time }} electronics delivery to {{ $area }}. TVs, fridges, washing machines & more. Pay on delivery. Order via WhatsApp.">
<link rel="canonical" href="https://yoola.ug/delivery/{{ strtolower($area) }}">
@endpush
@section('content')
<div class="bg-success text-white py-5"><div class="container text-center"><h1 class="fw-bold text-white mb-3">📍 {{ $fee }} Delivery to {{ $area }}</h1><p class="lead text-white-50 mb-0">{{ $time }} delivery • Pay on arrival • Genuine products</p></div></div>
<div class="container py-5">
<div class="row mb-5">
<div class="col-md-4 mb-4"><div class="card h-100 border-0 shadow-sm text-center"><div class="card-body py-4"><div class="display-4 text-success mb-3">🚚</div><h3 class="h5">{{ $time }} Delivery</h3><p class="text-muted mb-0">Order before 2 PM for same-day delivery to {{ $area }}</p></div></div></div>
<div class="col-md-4 mb-4"><div class="card h-100 border-0 shadow-sm text-center"><div class="card-body py-4"><div class="display-4 text-success mb-3">💵</div><h3 class="h5">Pay on Delivery</h3><p class="text-muted mb-0">Cash or Mobile Money when you receive your order</p></div></div></div>
<div class="col-md-4 mb-4"><div class="card h-100 border-0 shadow-sm text-center"><div class="card-body py-4"><div class="display-4 text-success mb-3">✓</div><h3 class="h5">{{ $fee }}</h3><p class="text-muted mb-0">No delivery charges for {{ $area }} residents</p></div></div></div>
</div>
<div class="card border-0 shadow-sm mb-5"><div class="card-header bg-white"><h2 class="h5 mb-0">📦 What We Deliver to {{ $area }}</h2></div><div class="card-body">
<div class="row">
<div class="col-6 col-md-4 mb-2"><a href="{{ url('/buy/tvs-uganda') }}" class="text-decoration-none">📺 TVs</a></div>
<div class="col-6 col-md-4 mb-2"><a href="{{ url('/buy/fridges-uganda') }}" class="text-decoration-none">❄️ Fridges</a></div>
<div class="col-6 col-md-4 mb-2"><a href="{{ url('/buy/washing-machines-uganda') }}" class="text-decoration-none">🧺 Washing Machines</a></div>
<div class="col-6 col-md-4 mb-2"><a href="{{ url('/buy/air-conditioners-uganda') }}" class="text-decoration-none">❄️ Air Conditioners</a></div>
<div class="col-6 col-md-4 mb-2"><a href="{{ url('/buy/cookers-uganda') }}" class="text-decoration-none">🍳 Cookers</a></div>
<div class="col-6 col-md-4 mb-2"><a href="{{ url('/buy/speakers-uganda') }}" class="text-decoration-none">🔊 Speakers</a></div>
</div>
</div></div>
<div class="card border-0 shadow-sm mb-5"><div class="card-header bg-white"><h2 class="h5 mb-0">📍 How to Order</h2></div><div class="card-body">
<ol class="mb-0">
<li class="mb-2">Browse products on yoola.ug or tell us what you need</li>
<li class="mb-2">WhatsApp us at 0704 229 768 with your order</li>
<li class="mb-2">Confirm your {{ $area }} delivery address</li>
<li class="mb-0">We deliver, you inspect, then pay!</li>
</ol>
</div></div>
<div class="text-center"><a href="https://wa.me/256704229768?text=Hi! I want delivery to {{ $area }}" class="btn btn-success btn-lg">Order Now - Delivery to {{ $area }}</a></div>
</div>
@endsection
