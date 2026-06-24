@extends('theme-views.layouts.app')
@section('title', 'Genuine Products Guarantee | 100% Authentic Electronics | Yoola Uganda')
@push('css_or_js')
<meta name="description" content="Yoola guarantees 100% genuine electronics. No counterfeits. Authentic TVs, fridges, appliances with manufacturer warranty. Buy with confidence in Uganda.">
<link rel="canonical" href="https://yoola.ug/genuine-guarantee">
@endpush
@section('content')
<div class="bg-primary text-white py-5"><div class="container text-center"><h1 class="fw-bold text-white mb-3">✓ 100% Genuine Products Guarantee</h1><p class="lead text-white-50 mb-0">Every product at Yoola is authentic. No counterfeits. Ever.</p></div></div>
<div class="container py-5">
<div class="row mb-5">
<div class="col-md-4 mb-4"><div class="card h-100 border-0 shadow-sm text-center"><div class="card-body py-4"><div class="display-4 text-primary mb-3">�icing</div><h3 class="h5">Direct from Brands</h3><p class="text-muted mb-0">We source directly from authorized distributors - Hisense, Samsung, TCL, LG, and more.</p></div></div></div>
<div class="col-md-4 mb-4"><div class="card h-100 border-0 shadow-sm text-center"><div class="card-body py-4"><div class="display-4 text-primary mb-3">📋</div><h3 class="h5">Full Warranty</h3><p class="text-muted mb-0">Every product comes with manufacturer warranty. Register your product for service support.</p></div></div></div>
<div class="col-md-4 mb-4"><div class="card h-100 border-0 shadow-sm text-center"><div class="card-body py-4"><div class="display-4 text-primary mb-3">🔒</div><h3 class="h5">Serial Number Verified</h3><p class="text-muted mb-0">All products have genuine serial numbers verifiable with the manufacturer.</p></div></div></div>
</div>
<div class="card border-0 shadow-sm mb-5"><div class="card-header bg-danger text-white"><h2 class="h5 mb-0">⚠️ How to Spot Fake Electronics</h2></div><div class="card-body">
<ul class="mb-0">
<li class="mb-2"><strong>Too cheap:</strong> If the price is 50% below market, it's likely fake</li>
<li class="mb-2"><strong>No warranty card:</strong> Genuine products always include warranty documentation</li>
<li class="mb-2"><strong>Poor packaging:</strong> Misspellings, blurry logos, damaged boxes</li>
<li class="mb-2"><strong>No serial number:</strong> Every genuine product has a unique serial number</li>
<li class="mb-0"><strong>Cash only:</strong> Sellers refusing receipts or formal payment methods</li>
</ul>
</div></div>
<div class="card border-0 shadow-sm mb-5"><div class="card-header bg-success text-white"><h2 class="h5 mb-0">✓ What You Get with Yoola</h2></div><div class="card-body">
<ul class="mb-0">
<li class="mb-2">Original manufacturer packaging</li>
<li class="mb-2">Warranty card with serial number</li>
<li class="mb-2">Official receipt for your records</li>
<li class="mb-2">Access to authorized service centers</li>
<li class="mb-0">Money-back guarantee if product is not genuine</li>
</ul>
</div></div>
<div class="text-center"><a href="{{ url('/') }}" class="btn btn-primary btn-lg">Shop Genuine Electronics</a> <a href="https://wa.me/256704229768" class="btn btn-success btn-lg ms-2">Ask Us Anything</a></div>
</div>
@endsection
