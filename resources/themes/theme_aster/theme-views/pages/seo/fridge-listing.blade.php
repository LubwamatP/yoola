@extends('theme-views.layouts.app')
@section('title', $pageTitle ?? 'Smart TVs Uganda')
@push('css_or_js')
<meta name="description" content="{{ $metaDescription ?? '' }}">
<link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="/smart-tv-prices-uganda">Smart TVs</a></li>
        <li class="breadcrumb-item active">{{ $h1 ?? 'TVs' }}</li>
    </ol></nav>

    <h1 class="mb-3" style="color:#C41E3A">{{ $h1 ?? 'Smart TVs' }}</h1>
    
    <p class="text-muted"><i class="fa fa-tv"></i> {{ $products->count() }} TVs found
    @if(isset($maxPrice)) under UGX {{ number_format($maxPrice) }} @endif
    @if(isset($size)) | {{ $size }}" screen @endif
    @if(isset($brand)) | {{ $brand['name'] }} @endif
    </p>

    @if($products->count() > 0)
    <div class="row g-3">
        @foreach($products as $p)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm">
                <a href="{{ route('product', $p->slug) }}">
                    <img src="{{ $p->thumbnail_full_url['path'] ?? asset('assets/images/placeholder.png') }}" 
                         class="card-img-top" alt="{{ $p->name }}" loading="lazy" style="height:160px;object-fit:contain">
                </a>
                @if($p->discount > 0)<span class="badge bg-danger position-absolute" style="top:8px;left:8px">-{{ $p->discount }}%</span>@endif
                <div class="card-body p-2">
                    <h3 class="fs-6 mb-1"><a href="{{ route('product', $p->slug) }}" class="text-dark text-decoration-none">{{ Str::limit($p->name, 45) }}</a></h3>
                    @if($p->discount > 0)<small class="text-muted text-decoration-line-through">UGX {{ number_format($p->unit_price) }}</small>@endif
                    <p class="fw-bold mb-1" style="color:#C41E3A">UGX {{ number_format($p->unit_price - ($p->unit_price * $p->discount / 100)) }}</p>
                    <a href="https://wa.me/256704229768?text=Hi! I want: {{ urlencode($p->name) }}" class="btn btn-success btn-sm w-100" target="_blank"><i class="fab fa-whatsapp"></i> Order</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="alert alert-info">No TVs found. <a href="/smart-tv-prices-uganda">View all TVs</a></div>
    @endif

    <div class="row mt-5 text-center">
        <div class="col-3"><i class="fas fa-truck fa-2x text-success"></i><p class="small"><b>Free Delivery</b></p></div>
        <div class="col-3"><i class="fas fa-shield-alt fa-2x text-primary"></i><p class="small"><b>Warranty</b></p></div>
        <div class="col-3"><i class="fas fa-money-bill fa-2x text-warning"></i><p class="small"><b>Pay on Delivery</b></p></div>
        <div class="col-3"><i class="fab fa-whatsapp fa-2x text-success"></i><p class="small"><b>0704 229 768</b></p></div>
    </div>
</div>
@endsection
