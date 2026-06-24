@extends('layouts.front-end.app')

@section('title', 'Electronics Prices in Uganda 2026 | Yoola')

@push('css_or_js')
    <meta name="description" content="Compare electronics prices in Uganda. TVs, fridges, cookers, washing machines and more. Best prices with free Kampala delivery. Updated February 2026.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ route('price-pages.index') }}">
    
    <style>
        .price-hub-hero {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }
        .price-hub-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .category-section {
            padding: 2rem 0;
        }
        .category-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #dc3545;
            display: inline-block;
        }
        .price-page-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }
        .price-page-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .price-page-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            color: inherit;
        }
        .price-page-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .price-page-card .price-preview {
            color: #dc3545;
            font-weight: 600;
        }
        .price-page-card .arrow {
            float: right;
            color: #dc3545;
        }
    </style>
@endpush

@section('content')
<main class="main-content">
    {{-- Hero --}}
    <section class="price-hub-hero">
        <div class="container">
            <h1>Electronics Prices in Uganda</h1>
            <p class="lead opacity-75">Compare prices and find the best deals. Updated {{ now()->format('F Y') }}.</p>
        </div>
    </section>

    <div class="container py-5">
        @foreach($pricePages as $type => $pages)
            <section class="category-section">
                <h2 class="category-title">
                    @switch($type)
                        @case('tv')
                            📺 TV Prices
                            @break
                        @case('fridge')
                            🧊 Fridge & Freezer Prices
                            @break
                        @case('cooker')
                            🔥 Cooker Prices
                            @break
                        @case('washing')
                            🌀 Washing Machine Prices
                            @break
                        @case('appliance')
                            🔌 Small Appliance Prices
                            @break
                        @default
                            📦 {{ ucfirst($type ?? 'Other') }} Prices
                    @endswitch
                </h2>

                <div class="price-page-grid">
                    @foreach($pages as $page)
                        <a href="{{ $page->url }}" class="price-page-card">
                            <h3>{{ $page->h1 }} <span class="arrow">→</span></h3>
                            @php $range = $page->price_range; @endphp
                            @if($range['min'] > 0)
                                <span class="price-preview">From {{ number_format($range['min']) }}/=</span>
                            @else
                                <span class="text-muted">View prices →</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach

        @if($pricePages->isEmpty())
            <div class="text-center py-5">
                <h2>Price pages coming soon!</h2>
                <p class="lead">In the meantime, browse our <a href="{{ route('products') }}">full catalog</a>.</p>
            </div>
        @endif
    </div>

    {{-- Bottom CTA --}}
    <div class="container pb-5">
        <div class="text-center p-5 bg-danger text-white rounded">
            <h2>Can't Find What You're Looking For?</h2>
            <p class="lead mb-4">WhatsApp us and we'll give you the best price instantly</p>
            <a href="https://wa.me/256780221421" class="btn btn-light btn-lg">
                <i class="bi bi-whatsapp text-success"></i> +256780221421
            </a>
        </div>
    </div>
</main>
@endsection
