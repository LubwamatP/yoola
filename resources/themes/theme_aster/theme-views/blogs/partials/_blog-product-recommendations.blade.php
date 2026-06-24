{{--
    Blog Product Recommendations
    Converts informational blog traffic into product clicks.
    Matches blog content keywords to actual products in the catalog.
    Designed to capture CHiQ blog visitors (37% of all traffic) and redirect to Hisense.
--}}
@php
    $blogTitle = strtolower($blogData['title'] ?? '');
    $blogSlug = strtolower($blogData['slug'] ?? '');
    $blogText = $blogTitle . ' ' . $blogSlug;

    // Keyword → brand redirect mapping (for brands NOT sold but with high blog traffic)
    $brandRedirects = [
        'chiq' => ['hisense', 'skyworth', 'samsung'],
        'tcl' => ['hisense', 'skyworth', 'samsung'],
        'global star' => ['hisense', 'skyworth', 'mitech'],
        'blackark' => ['hisense', 'skyworth'],
        'syinix' => ['mitech', 'hisense'],
        'nexus' => ['mitech', 'hisense'],
        'lg' => ['samsung', 'hisense'],
        'sony' => ['samsung', 'hisense'],
    ];

    // Detect what the blog is about
    $detectedBrand = null;
    $redirectBrands = [];
    foreach ($brandRedirects as $keyword => $redirects) {
        if (str_contains($blogText, $keyword)) {
            $detectedBrand = $keyword;
            $redirectBrands = $redirects;
            break;
        }
    }

    $isTvBlog = str_contains($blogText, 'tv') || str_contains($blogText, 'television') || str_contains($blogText, 'smart tv');
    $isFridgeBlog = str_contains($blogText, 'fridge') || str_contains($blogText, 'refrigerator');
    $isWasherBlog = str_contains($blogText, 'washing') || str_contains($blogText, 'washer');

    // Build product query based on blog topic
    $recommendedProducts = collect();
    $sectionTitle = 'Shop Related Products';
    $sectionSubtitle = '';

    if ($detectedBrand && $redirectBrands) {
        // CHiQ/TCL/etc blog → show alternative brands you actually sell
        $sectionTitle = 'Looking for ' . ucfirst($detectedBrand) . '? Consider These Alternatives';
        $sectionSubtitle = 'We don\'t currently stock ' . ucfirst($detectedBrand) . ', but these trusted brands offer similar quality at competitive prices. All come with official warranty and free Kampala delivery.';

        $recommendedProducts = \App\Models\Product::where('status', 1)
            ->where(function($q) use ($redirectBrands) {
                foreach ($redirectBrands as $brand) {
                    $q->orWhere('name', 'LIKE', '%' . $brand . '%');
                }
            })
            ->where('current_stock', '>', 0)
            ->orderBy('unit_price', 'asc')
            ->take(4)
            ->get();

        // If no matching products, fall back to popular
        if ($recommendedProducts->isEmpty()) {
            $recommendedProducts = \App\Models\Product::where('status', 1)
                ->where('current_stock', '>', 0)
                ->orderBy('unit_price', 'asc')
                ->take(4)
                ->get();
            $sectionTitle = 'Shop Our Best Sellers';
        }
    } elseif ($isTvBlog) {
        $sectionTitle = 'Shop TVs With Best Prices in Kampala';
        $recommendedProducts = \App\Models\Product::where('status', 1)
            ->where(function($q) {
                $q->where('name', 'LIKE', '%TV%')
                  ->orWhere('name', 'LIKE', '%Smart TV%')
                  ->orWhere('name', 'LIKE', '%QLED%')
                  ->orWhere('name', 'LIKE', '%ULED%');
            })
            ->where('current_stock', '>', 0)
            ->orderBy('unit_price', 'asc')
            ->take(4)
            ->get();
    } elseif ($isFridgeBlog) {
        $sectionTitle = 'Shop Refrigerators at the Best Prices';
        $recommendedProducts = \App\Models\Product::where('status', 1)
            ->where(function($q) {
                $q->where('name', 'LIKE', '%fridge%')
                  ->orWhere('name', 'LIKE', '%refrigerator%');
            })
            ->where('current_stock', '>', 0)
            ->orderBy('unit_price', 'asc')
            ->take(4)
            ->get();
    } elseif ($isWasherBlog) {
        $sectionTitle = 'Shop Washing Machines';
        $recommendedProducts = \App\Models\Product::where('status', 1)
            ->where(function($q) {
                $q->where('name', 'LIKE', '%washing%')
                  ->orWhere('name', 'LIKE', '%washer%');
            })
            ->where('current_stock', '>', 0)
            ->orderBy('unit_price', 'asc')
            ->take(4)
            ->get();
    } else {
        // Default: show popular products
        $sectionTitle = 'Shop Our Best Sellers';
        $recommendedProducts = \App\Models\Product::where('status', 1)
            ->where('current_stock', '>', 0)
            ->orderBy('unit_price', 'asc')
            ->take(4)
            ->get();
    }

    // Determine the best price page to link to
    $pricePageLink = null;
    $pricePageText = null;
    if ($detectedBrand && in_array($detectedBrand, ['chiq', 'tcl'])) {
        $pricePageLink = url('/prices/hisense-tv-uganda');
        $pricePageText = 'See all Hisense TV prices →';
    } elseif ($detectedBrand && $detectedBrand === 'global star') {
        $pricePageLink = url('/prices/smart-tv-uganda');
        $pricePageText = 'Compare all smart TV prices →';
    } elseif ($isTvBlog) {
        $pricePageLink = url('/prices/smart-tv-uganda');
        $pricePageText = 'Compare all smart TV prices →';
    } elseif ($isFridgeBlog) {
        $pricePageLink = url('/prices/fridge-uganda');
        $pricePageText = 'See all fridge prices →';
    } elseif ($isWasherBlog) {
        $pricePageLink = url('/prices/washing-machine-uganda');
        $pricePageText = 'See all washing machine prices →';
    }
@endphp

@if($recommendedProducts->isNotEmpty())
<div class="blog-product-recommendations mt-5 mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h3 class="fs-18 fw-bold mb-2">{{ $sectionTitle }}</h3>
            @if($sectionSubtitle)
                <p class="text-muted fs-14 mb-4">{{ $sectionSubtitle }}</p>
            @endif

            <div class="row g-3">
                @foreach($recommendedProducts as $product)
                <div class="col-6 col-md-3">
                    <a href="{{ route('product', $product->slug) }}" class="text-decoration-none">
                        <div class="card border h-100 product-recommendation-card">
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 140px; overflow: hidden;">
                                @if($product->thumbnail_full_url['path'] ?? null)
                                    <img src="{{ $product->thumbnail_full_url['path'] }}"
                                         alt="{{ $product->name }}"
                                         style="max-height: 120px; max-width: 100%; object-fit: contain;"
                                         loading="lazy">
                                @else
                                    <i class="bi bi-box-seam fs-1 text-muted"></i>
                                @endif
                            </div>
                            <div class="card-body p-2">
                                <p class="fw-semibold fs-13 line-clamp-2 mb-1 text-dark">
                                    {{ Str::limit($product->name, 50) }}
                                </p>
                                <p class="text-danger fw-bold fs-14 mb-1">
                                    {{ number_format($product->unit_price) }}/=
                                </p>
                                @if($product->current_stock > 0)
                                    <span class="badge bg-success fs-11">In Stock</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            {{-- Bottom CTA row --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 pt-3 border-top">
                @if($pricePageLink)
                    <a href="{{ $pricePageLink }}" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-tag"></i> {{ $pricePageText }}
                    </a>
                @endif
                <a href="https://wa.me/256704229768?text={{ urlencode('Hi! I read your article on ' . ($blogData['title'] ?? 'Yoola') . '. I\'m interested in buying. What\'s available?') }}"
                   class="btn btn-success btn-sm ms-auto">
                    <i class="bi bi-whatsapp"></i> WhatsApp: Get Best Price
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .product-recommendation-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .product-recommendation-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .blog-product-recommendations .card {
        border-radius: 12px;
    }
</style>
@endif
