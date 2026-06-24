@extends('theme-views.layouts.app')

@section('title', 'ADH Products in Uganda | Freezers, Fridges & Appliances | Yoola.ug')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold text-white">ADH Built for Uganda</h1>
        <p class="text-white">Reliable freezers and refrigerators designed for African homes. Free Kampala delivery.</p>
    </div>
</div>

<div class="container py-5">
    <h2 class="mb-4">ADH Chest Freezers</h2>
    <div class="row g-3 mb-5">
        @forelse($freezers->take(8) as $product)
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
        <div class="col-12"><p>No ADH freezers available</p></div>
        @endforelse
    </div>

    <h2 class="mb-4">ADH Refrigerators</h2>
    <div class="row g-3 mb-5">
        @forelse($fridges->take(8) as $product)
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
        <div class="col-12"><p>No ADH fridges available</p></div>
        @endforelse
    </div>
</div>

<div class="container py-4">
    <h3 class="mb-3">Frequently Asked Questions</h3>
    <div class="accordion" id="faqAccordion">
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                    What is ADH brand?
                </button>
            </h4>
            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    ADH is a trusted appliance brand specializing in chest freezers and refrigerators designed for African conditions. Known for durability, energy efficiency, and handling power fluctuations common in Uganda.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                    Are ADH freezers good for Uganda?
                </button>
            </h4>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Yes! ADH freezers are specifically designed for African markets. They handle power outages well, are energy efficient to reduce UMEME bills, and come in various sizes for homes and businesses.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                    Where can I buy ADH products in Kampala?
                </button>
            </h4>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Yoola.ug stocks genuine ADH freezers and fridges with full warranty. We offer free delivery in Kampala and the best prices in Uganda. Order via WhatsApp: 0704 229 768.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-primary text-white py-4 text-center">
    <h3 class="fw-bold text-white">Order ADH on WhatsApp: 0704 229 768</h3>
</div>
@endsection
