@extends('theme-views.layouts.app')

@section('title', $pageTitle . ' | Yoola.ug')

@section('content')
<div class="bg-primary text-white py-4">
    <div class="container">
        <h1 class="fw-bold text-white">{{ $pageTitle }}</h1>
        <p class="text-white">{{ $metaDescription }}</p>
    </div>
</div>

<div class="container py-4">
    <h2 class="mb-4">{{ $categoryName }} ({{ $products->count() }} products)</h2>
    <div class="row g-3">
        @forelse($products as $product)
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
        <div class="col-12 text-center py-5">
            <p>No products found in this category</p>
            <a href="https://wa.me/256704229768" class="btn btn-primary">Contact Us</a>
        </div>
        @endforelse
    </div>
</div>

<div class="container py-4">
    <h3 class="mb-3">Frequently Asked Questions</h3>
    <div class="accordion" id="faqAccordion">
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                    How much does a {{ strtolower($categoryName) }} cost in Uganda?
                </button>
            </h4>
            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    {{ $categoryName }} prices in Uganda range from UGX {{ number_format($products->min('unit_price') ?? 0) }} to UGX {{ number_format($products->max('unit_price') ?? 0) }} at Yoola.ug. Prices depend on brand, size, and features.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                    Where can I buy genuine {{ strtolower($categoryName) }} in Kampala?
                </button>
            </h4>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Yoola.ug is Uganda's trusted online electronics store. We sell only 100% genuine products with manufacturer warranty and offer free delivery in Kampala.
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h4 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                    Do you deliver outside Kampala?
                </button>
            </h4>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Yes! We offer free delivery within Kampala and nationwide shipping to all major towns in Uganda. Contact us on WhatsApp for delivery quotes outside Kampala.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-primary text-white py-4 text-center">
    <h3 class="fw-bold text-white">Order on WhatsApp: 0704 229 768</h3>
</div>
@endsection
