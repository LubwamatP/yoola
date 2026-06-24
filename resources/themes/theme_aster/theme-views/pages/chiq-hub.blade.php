@extends('theme-views.layouts.app')

@section('title', 'CHiQ Products in Uganda | Affordable Smart TVs & Appliances | Yoola.ug')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold text-white">CHiQ Smart Value for Uganda</h1>
        <p class="text-white">Premium features at affordable prices. Android TVs, fridges & more. Free Kampala delivery.</p>
    </div>
</div>

<div class="container py-5">
    <h2 class="mb-4">CHiQ Smart TVs</h2>
    <div class="row g-3 mb-5">
        @forelse($tvs->take(8) as $product)
        <div class="col-6 col-md-3">
            <div class="card h-100">
                <a href="{{ route('product', $product->slug) }}">
                    <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" class="card-img-top" alt="{{ $product->name }}" style="height:150px;object-fit:contain;">
                </a>
                <div class="card-body">
                    <h6 class="card-title small">{{ Str::limit($product->name, 40) }}</h6>
                    <p class="text-primary fw-bold mb-2">{{ webCurrencyConverter($product->unit_price) }}</p>
                    <a href="https://wa.me/256704229768?text=Hi! I want {{ urlencode($product->name) }}" class="btn btn-success btn-sm w-100">Order</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12"><p>No CHiQ TVs available</p></div>
        @endforelse
    </div>

    <h2 class="mb-4">CHiQ Refrigerators</h2>
    <div class="row g-3 mb-5">
        @forelse($fridges->take(4) as $product)
        <div class="col-6 col-md-3">
            <div class="card h-100">
                <a href="{{ route('product', $product->slug) }}">
                    <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" class="card-img-top" alt="{{ $product->name }}" style="height:150px;object-fit:contain;">
                </a>
                <div class="card-body">
                    <h6 class="card-title small">{{ Str::limit($product->name, 40) }}</h6>
                    <p class="text-primary fw-bold mb-2">{{ webCurrencyConverter($product->unit_price) }}</p>
                    <a href="https://wa.me/256704229768?text=Hi! I want {{ urlencode($product->name) }}" class="btn btn-success btn-sm w-100">Order</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12"><p>No CHiQ fridges available</p></div>
        @endforelse
    </div>

    <h2 class="mb-4">CHiQ Freezers</h2>
    <div class="row g-3 mb-5">
        @forelse($freezers->take(4) as $product)
        <div class="col-6 col-md-3">
            <div class="card h-100">
                <a href="{{ route('product', $product->slug) }}">
                    <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" class="card-img-top" alt="{{ $product->name }}" style="height:150px;object-fit:contain;">
                </a>
                <div class="card-body">
                    <h6 class="card-title small">{{ Str::limit($product->name, 40) }}</h6>
                    <p class="text-primary fw-bold mb-2">{{ webCurrencyConverter($product->unit_price) }}</p>
                    <a href="https://wa.me/256704229768?text=Hi! I want {{ urlencode($product->name) }}" class="btn btn-success btn-sm w-100">Order</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12"><p>No CHiQ freezers available</p></div>
        @endforelse
    </div>
</div>

<div class="container py-4">
    <h3 class="mb-3">Frequently Asked Questions</h3>
    <div class="accordion" id="faqAccordion">
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                    What is CHiQ brand?
                </button>
            </h4>
            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    CHiQ is a premium electronics brand known for affordable Smart TVs with Android OS. CHiQ TVs offer 4K resolution, built-in Netflix and YouTube, at prices lower than competitors. Popular in Uganda for value-conscious buyers.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                    Are CHiQ TVs good quality?
                </button>
            </h4>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Yes! CHiQ TVs offer excellent picture quality with 4K UHD resolution, Android smart features, and reliable performance. They provide premium features at budget-friendly prices, making them popular in Uganda.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                    Where can I buy CHiQ products in Uganda?
                </button>
            </h4>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Yoola.ug stocks genuine CHiQ TVs, fridges, and freezers with warranty. We offer free delivery in Kampala and competitive prices. Order via WhatsApp: 0704 229 768.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-primary text-white py-4 text-center">
    <h3 class="fw-bold text-white">Order CHiQ on WhatsApp: 0704 229 768</h3>
</div>
@endsection
