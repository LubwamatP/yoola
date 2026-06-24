@php use App\Utils\Helpers; @endphp
@if(isset($accessories) && count($accessories) > 0)
<section class="complete-setup-section py-4 bg-light">
    <div class="container">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="fs-3">🎯</span>
            <h3 class="mb-0 text-capitalize">{{ translate("complete_your_setup") }}</h3>
        </div>
        <p class="text-muted mb-4">{{ translate("frequently_bought_together") }}</p>
        
        <div class="row g-3">
            @foreach($accessories as $accessory)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <a href="{{ route('product', $accessory->slug) }}" class="text-decoration-none">
                        <div class="position-relative">
                            <img src="{{ getStorageImages(path: $accessory->thumbnail_full_url, type: 'product') }}" 
                                 class="card-img-top p-2" 
                                 alt="{{ $accessory->name }}"
                                 style="height: 150px; object-fit: contain;">
                            @if($accessory->discount > 0)
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                -{{ $accessory->discount_type == 'percent' ? round($accessory->discount) . '%' : Helpers::currency_converter($accessory->discount) }}
                            </span>
                            @endif
                        </div>
                        <div class="card-body p-2">
                            <h6 class="card-title text-dark mb-1 text-truncate-2" style="font-size: 0.85rem; line-height: 1.3;">
                                {{ $accessory->name }}
                            </h6>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-primary fw-bold">
                                    {{ Helpers::currency_converter(
                                        $accessory->unit_price - (Helpers::getProductDiscount($accessory, $accessory->unit_price))
                                    ) }}
                                </span>
                                @if($accessory->discount > 0)
                                <small class="text-muted text-decoration-line-through">
                                    {{ Helpers::currency_converter($accessory->unit_price) }}
                                </small>
                                @endif
                            </div>
                        </div>
                    </a>
                    <div class="card-footer bg-transparent border-0 p-2 pt-0">
                        <a href="https://wa.me/256704229768?text={{ urlencode('Hi! I want to add this to my order: ' . $accessory->name . ' - ' . url('product/' . $accessory->slug)) }}" 
                           class="btn btn-success btn-sm w-100">
                            <i class="bi bi-whatsapp"></i> {{ translate("add_to_order") }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
