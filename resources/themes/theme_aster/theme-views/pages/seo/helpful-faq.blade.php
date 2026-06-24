@extends('theme-views.layouts.app')
@section('title', $pageTitle ?? 'FAQ')
@push('css_or_js')
<meta name="description" content="{{ $metaDescription ?? '' }}">
<link rel="canonical" href="{{ url()->current() }}">
{{-- FAQPage Structured Data (JSON-LD) --}}
@if(isset($faqs) && count($faqs) > 0)
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        @foreach($faqs as $i => $faq)
        {
            "@type": "Question",
            "name": "{{ addslashes($faq['q']) }}",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "{{ addslashes(strip_tags($faq['a'])) }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif
<style>
.faq-section { background: #f8f9fa; border-radius: 10px; padding: 30px; margin-bottom: 30px; }
.accordion-button:not(.collapsed) { background-color: #C41E3A; color: white; }
.trust-badge { background: white; border-radius: 10px; padding: 20px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.solution-box { background: #d4edda; border-left: 4px solid #28a745; padding: 20px; margin: 20px 0; border-radius: 5px; }
.intro-text { font-size: 1.2rem; color: #555; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="/faqs">Help Center</a></li>
        <li class="breadcrumb-item active">{{ $h1 ?? 'FAQ' }}</li>
    </ol></nav>

    <h1 class="mb-3" style="color:#C41E3A">{{ $h1 ?? 'FAQ' }}</h1>
    
    @isset($intro)
    <p class="intro-text mb-4">{{ $intro }}</p>
    @endisset

    @isset($solution)
    <div class="solution-box">
        <h4><i class="fas fa-check-circle text-success"></i> {{ $solution['title'] ?? 'Our Solution' }}</h4>
        <p class="mb-0">{{ $solution['text'] }}</p>
    </div>
    @endisset

    <div class="faq-section">
        <h2 class="h4 mb-4"><i class="fas fa-question-circle text-primary"></i> Frequently Asked Questions</h2>
        <div class="accordion" id="faqAccordion">
            @foreach($faqs ?? [] as $i => $faq)
            <div class="accordion-item mb-2 border">
                <h3 class="accordion-header">
                    <button class="accordion-button @if($i > 0) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">
                        {{ $faq['q'] }}
                    </button>
                </h3>
                <div id="faq{{ $i }}" class="accordion-collapse collapse @if($i == 0) show @endif" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">{!! $faq['a'] !!}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Trust Badges -->
    <div class="row mb-5">
        <div class="col-md-3 col-6 mb-3">
            <div class="trust-badge"><i class="fas fa-shield-alt fa-2x text-success mb-2"></i><h6>100% Genuine</h6><small class="text-muted">All products original</small></div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="trust-badge"><i class="fas fa-certificate fa-2x text-primary mb-2"></i><h6>Real Warranty</h6><small class="text-muted">Manufacturer backed</small></div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="trust-badge"><i class="fas fa-truck fa-2x text-warning mb-2"></i><h6>Free Delivery</h6><small class="text-muted">Kampala & Wakiso</small></div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="trust-badge"><i class="fab fa-whatsapp fa-2x text-success mb-2"></i><h6>WhatsApp Support</h6><small class="text-muted">0704 229 768</small></div>
        </div>
    </div>

    @if(isset($products) && count($products) > 0)
    <h2 class="mb-3">Shop with Confidence</h2>
    <div class="row g-3 mb-4">
        @foreach($products->take(8) as $p)
        <div class="col-6 col-md-3">
            <div class="card h-100 shadow-sm">
                <a href="{{ route('product', $p->slug) }}">
                    <img src="{{ $p->thumbnail_full_url['path'] ?? '/assets/images/placeholder.png' }}" class="card-img-top" alt="{{ $p->name }}" style="height:140px;object-fit:contain">
                </a>
                <div class="card-body p-2">
                    <p class="small mb-1">{{ Str::limit($p->name, 40) }}</p>
                    <p class="fw-bold mb-1" style="color:#C41E3A">UGX {{ number_format($p->unit_price) }}</p>
                    <a href="https://wa.me/256704229768?text=Hi! I want {{ urlencode($p->name) }}" class="btn btn-success btn-sm w-100"><i class="fab fa-whatsapp"></i> Order</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="text-center p-4 bg-light rounded">
        <h3>Still Have Questions?</h3>
        <p>Our team is ready to help you find the perfect electronics!</p>
        <a href="https://wa.me/256704229768" class="btn btn-success btn-lg"><i class="fab fa-whatsapp"></i> Chat Now: 0704 229 768</a>
    </div>
</div>
@endsection
