@extends('theme-views.layouts.app')
@section('title', 'Cash on Delivery | Pay When You Receive | Yoola Uganda')
@push('css_or_js')
<meta name="description" content="Pay for your electronics when they arrive at your door. Cash on delivery or Mobile Money on delivery. Inspect before you pay. Free delivery Kampala & Wakiso.">
<link rel="canonical" href="https://yoola.ug/cash-on-delivery">
@endpush
@section('content')
<div class="bg-success text-white py-5"><div class="container text-center"><h1 class="fw-bold text-white mb-3">💵 Pay When You Receive</h1><p class="lead text-white-50 mb-0">Inspect your product first. Then pay. Zero risk.</p></div></div>
<div class="container py-5">
<div class="row mb-5">
<div class="col-md-4 mb-4"><div class="card h-100 border-0 shadow-sm text-center"><div class="card-body py-4"><div class="display-4 text-success mb-3">📦</div><h3 class="h5">Inspect First</h3><p class="text-muted mb-0">Open the box, check the product, make sure it's exactly what you ordered.</p></div></div></div>
<div class="col-md-4 mb-4"><div class="card h-100 border-0 shadow-sm text-center"><div class="card-body py-4"><div class="display-4 text-success mb-3">💵</div><h3 class="h5">Pay Cash or MoMo</h3><p class="text-muted mb-0">Pay with cash or Mobile Money (MTN, Airtel) right at your doorstep.</p></div></div></div>
<div class="col-md-4 mb-4"><div class="card h-100 border-0 shadow-sm text-center"><div class="card-body py-4"><div class="display-4 text-success mb-3">🧾</div><h3 class="h5">Get Your Receipt</h3><p class="text-muted mb-0">Official receipt for warranty claims and your records.</p></div></div></div>
</div>
<div class="card border-0 shadow-sm mb-5"><div class="card-header bg-primary text-white"><h2 class="h5 mb-0">📍 How It Works</h2></div><div class="card-body">
<ol class="mb-0">
<li class="mb-3"><strong>Order via WhatsApp or Website:</strong> Tell us what you want</li>
<li class="mb-3"><strong>We Confirm:</strong> Price, delivery time, and your location</li>
<li class="mb-3"><strong>We Deliver:</strong> Same-day Kampala, next-day Wakiso</li>
<li class="mb-3"><strong>You Inspect:</strong> Open the box, check everything</li>
<li class="mb-0"><strong>You Pay:</strong> Cash or Mobile Money to our delivery agent</li>
</ol>
</div></div>
<div class="card border-0 shadow-sm mb-5"><div class="card-header bg-white"><h2 class="h5 mb-0">❓ FAQ</h2></div><div class="card-body">
<p class="mb-3"><strong>Q: What if I don't like the product?</strong><br>A: You can refuse delivery. No payment needed. We take it back.</p>
<p class="mb-3"><strong>Q: Is there a COD fee?</strong><br>A: No extra charge for cash on delivery.</p>
<p class="mb-0"><strong>Q: Can I pay part now, part on delivery?</strong><br>A: Yes! Contact us to arrange partial payment.</p>
</div></div>
<div class="text-center"><a href="{{ url('/') }}" class="btn btn-success btn-lg">Shop Now - Pay on Delivery</a> <a href="https://wa.me/256704229768" class="btn btn-outline-success btn-lg ms-2">Order via WhatsApp</a></div>
</div>
@endsection
