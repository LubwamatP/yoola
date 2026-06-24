@extends('theme-views.layouts.app')

@section('title', 'Hisense Products in Uganda | TVs, Fridges, Appliances | Yoola.ug')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="fw-bold text-white">Hisense Quality You Can Trust</h1>
        <p class="text-white">Uganda's widest selection of genuine Hisense electronics. Free Kampala delivery.</p>
    </div>
</div>

<div class="container py-5">
    <h2 class="mb-4">Hisense TVs</h2>
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
        <div class="col-12"><p>No Hisense TVs available</p></div>
        @endforelse
    </div>

    <h2 class="mb-4">Hisense Refrigerators</h2>
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
        <div class="col-12"><p>No Hisense fridges available</p></div>
        @endforelse
    </div>

    <h2 class="mb-4">Hisense Washing Machines</h2>
    <div class="row g-3 mb-5">
        @forelse($washers->take(4) as $product)
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
        <div class="col-12"><p>No Hisense washers available</p></div>
        @endforelse
    </div>
</div>

<div class="container py-4">
    <h3 class="mb-3">Frequently Asked Questions</h3>
    <div class="accordion" id="faqAccordion">
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                    Is Hisense a good brand in Uganda?
                </button>
            </h4>
            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Yes! Hisense is one of the most trusted electronics brands in Uganda. Known for excellent value, energy efficiency, and durability. Hisense TVs and fridges are popular choices for Ugandan homes.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                    Where can I buy genuine Hisense products in Uganda?
                </button>
            </h4>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Yoola.ug is an authorized Hisense dealer in Uganda. All our Hisense products are 100% genuine with manufacturer warranty. We offer free delivery in Kampala.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                    What warranty do Hisense products have?
                </button>
            </h4>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    All Hisense products from Yoola.ug come with official manufacturer warranty. TVs and fridges typically have 2-year warranty. We provide warranty cards with every purchase.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-primary text-white py-4 text-center">
    <h3 class="fw-bold text-white">Order Hisense on WhatsApp: 0704 229 768</h3>
</div>
@endsection
