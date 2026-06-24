@extends('theme-views.layouts.app')

@section('title', 'Samsung Washing Machines Uganda | Yoola.ug')

@section('content')
<div class="bg-primary text-white py-4">
    <div class="container">
        <h1>Samsung Washing Machines</h1>
        <p>Genuine Samsung washers with 2-year warranty</p>
    </div>
</div>

<div class="container py-4">
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
            <p>No Samsung washing machines in stock</p>
            <a href="https://wa.me/256704229768" class="btn btn-primary">Contact Us</a>
        </div>
        @endforelse
    </div>
</div>
@endsection
