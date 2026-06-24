@extends('theme-views.layouts.app')
@section('title', $pageTitle ?? 'FAQ')
@push('css_or_js')
<meta name="description" content="{{ $metaDescription ?? '' }}">
<link rel="canonical" href="{{ url()->current() }}">
@endpush

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item active">{{ $h1 ?? 'FAQ' }}</li>
    </ol></nav>

    <h1 class="mb-4" style="color:#C41E3A">{{ $h1 ?? 'FAQ' }}</h1>
    
    @isset($intro)<p class="lead mb-4">{{ $intro }}</p>@endisset

    <div class="accordion" id="faqAccordion">
        @foreach($faqs as $i => $faq)
        <div class="accordion-item mb-2">
            <h2 class="accordion-header">
                <button class="accordion-button @if($i > 0) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">
                    <strong>{{ $faq['q'] }}</strong>
                </button>
            </h2>
            <div id="faq{{ $i }}" class="accordion-collapse collapse @if($i == 0) show @endif" data-bs-parent="#faqAccordion">
                <div class="accordion-body">{!! $faq['a'] !!}</div>
            </div>
        </div>
        @endforeach
    </div>

    @if(isset($products) && count($products) > 0)
    <h2 class="mt-5 mb-3">Related Products</h2>
    <div class="row g-3">
        @foreach($products->take(8) as $p)
        <div class="col-6 col-md-3">
            <div class="card h-100 shadow-sm">
                <a href="{{ route('product', $p->slug) }}">
                    <img src="{{ $p->thumbnail_full_url['path'] ?? '/assets/images/placeholder.png' }}" class="card-img-top" alt="{{ $p->name }}" style="height:140px;object-fit:contain">
                </a>
                <div class="card-body p-2">
                    <p class="small mb-1">{{ Str::limit($p->name, 40) }}</p>
                    <p class="fw-bold mb-1" style="color:#C41E3A">UGX {{ number_format($p->unit_price) }}</p>
                    <a href="https://wa.me/256704229768?text=Hi! {{ urlencode($p->name) }}" class="btn btn-success btn-sm w-100"><i class="fab fa-whatsapp"></i> Order</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="mt-5 p-4 bg-light rounded text-center">
        <h3>Still have questions?</h3>
        <p>Chat with us on WhatsApp!</p>
        <a href="https://wa.me/256704229768" class="btn btn-success btn-lg"><i class="fab fa-whatsapp"></i> 0704 229 768</a>
    </div>
</div>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    @foreach($faqs as $i => $faq)
    {
      "@type": "Question",
      "name": @json($faq['q']),
      "acceptedAnswer": {
        "@type": "Answer", 
        "text": @json(strip_tags($faq['a']))
      }
    }@if($i < count($faqs) - 1),@endif
    @endforeach
  ]
}
</script>
@endsection
