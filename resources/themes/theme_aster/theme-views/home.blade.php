@extends('theme-views.layouts.app')

{{-- SEO: Optimized title targeting "Electronics Store Uganda" --}}
@section('title', 'Electronics Store Uganda | TVs, Fridges, Appliances | Free Delivery - Yoola')

@section('meta_description', 'Shop electronics in Uganda at Yoola. TVs from 490,000/=, fridges, washing machines & more. ✓ Free Kampala delivery ✓ Genuine warranty ✓ Cash on delivery. Burton St, Aponye Mall.')

@push('css_or_js')
    @include('theme-views.partials._organization-schema')
    
    {{-- Homepage FAQ Schema for Rich Results --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "FAQPage",
        "mainEntity": [
            {
                "@@type": "Question",
                "name": "Does Yoola offer free delivery in Kampala?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Yes! We offer free delivery within Kampala for orders above 100,000 UGX. Same-day delivery is available for orders placed before 2 PM."
                }
            },
            {
                "@@type": "Question",
                "name": "Do products come with warranty?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "All electronics at Yoola come with genuine manufacturer warranty. We only sell authentic products, not grey market imports."
                }
            },
            {
                "@@type": "Question",
                "name": "Can I pay on delivery?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Yes! We accept cash on delivery, mobile money (MTN MoMo, Airtel Money), and bank transfers. Pay when you receive your order."
                }
            },
            {
                "@@type": "Question",
                "name": "Where is Yoola located?",
                "acceptedAnswer": {
                    "@@type": "Answer",
                    "text": "Visit us at 6a Burton Street, Aponye City Mall, Kampala. You can also shop online at yoola.ug or WhatsApp us at +256704229768."
                }
            }
        ]
    }
    </script>
@endpush

@section('content')
    <main class="main-content d-flex flex-column gap-3 py-3">
        {{-- SEO: H1 optimized for "Electronics Store Uganda" keyword --}}
        <h1 class="visually-hidden">Electronics Store Uganda — TVs, Fridges & Home Appliances | Free Kampala Delivery</h1>
        <?php
        $orderSuccessIds = session('order_success_ids');
        $isNewCustomerInSession = session('isNewCustomerInSession');
        session()->forget('order_success_ids');
        session()->forget('isNewCustomerInSession');
        ?>
        @include("theme-views.partials._order-success-modal",['orderSuccessIds' => $orderSuccessIds, 'isNewCustomerInSession' => $isNewCustomerInSession])

        @include('theme-views.partials._main-banner')

        {{-- Valentine's Day Promotional Banner --}}
        @include('theme-views.partials._valentines-banner')

        {{-- Trust Strip - Builds buyer confidence --}}
        @include('theme-views.partials._trust-strip')

        {{-- Flash Deals - Show if available --}}
        @if (isset($flashDeal) && isset($flashDeal['flashDeal']) && isset($flashDeal['flashDealProducts']) && count($flashDeal['flashDealProducts']) > 0)
            @include('theme-views.partials._flash-deals')
        @endif

        {{-- Trending Now (AI-Powered) --}}
        @if(isset($trendingProducts) && $trendingProducts->count() > 0)
            @include('theme-views.partials._trending-products', ['trendingProducts' => $trendingProducts])
        @endif

        @include('theme-views.partials._clearance-sale', ['clearanceSaleProducts' => $clearanceSaleProducts])

        @if ($web_config['business_mode'] == 'multi' && count($topVendorsList) > 0 && $topVendorsListSectionShowingStatus)
            @include('theme-views.partials._top-stores')
        @endif

        @if (getFeaturedDealsProductList()->count() > 0)
            @include('theme-views.partials._featured-deals')
        @endif

        @include('theme-views.partials._recommended-product')
        @if($web_config['business_mode'] == 'multi')
            @include('theme-views.partials._more-stores')
        @endif

        @include('theme-views.partials._top-rated-products')

        {{-- AI-Powered: Hot Right Now --}}
        @if(isset($hotNowProducts) && $hotNowProducts->count() > 0)
            @include('theme-views.partials._hot-now', ['hotNowProducts' => $hotNowProducts])
        @endif

        @include('theme-views.partials._best-deal-just-for-you')

        @include('theme-views.partials._home-categories')
        
        {{-- Recently Viewed (Personalized) --}}
        @if(isset($recentlyViewedProducts) && $recentlyViewedProducts->count() > 0)
            @include('theme-views.partials._recently-viewed', ['recentlyViewedProducts' => $recentlyViewedProducts])
        @endif
        @if (!empty($bannerTypeMainSectionBanner))
        <section class="">
            <div class="container">
                <div class="py-5 rounded position-relative">
                    <img src="{{ getStorageImages(path: $bannerTypeMainSectionBanner->photo_full_url??null, type:'banner') }}"
                         alt="{{ $product['name'] ?? 'Product' }}" class="rounded position-absolute dark-support img-fit start-0 top-0 index-n1 flipX-in-rtl">
                    <div class="row justify-content-center">
                        <div class="col-10 py-4">
                            <h6 class="text-primary mb-2 text-capitalize">{{ translate('do_not_miss_today`s_deal') }}!</h6>
                            <h2 class="fs-2 mb-4 absolute-dark text-capitalize">{{ translate('let_us_shopping_today') }}</h2>
                            <div class="d-flex">
                                <a href="{{ $bannerTypeMainSectionBanner ? $bannerTypeMainSectionBanner->url : '' }}"
                                   class="btn btn-primary fs-16 text-capitalize">
                                    {{ translate('shop_now') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif
    </main>
@endsection

@push('script')
    @if($orderSuccessIds)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modalEl = document.getElementById('order_successfully');
                const orderModal = new bootstrap.Modal(modalEl, {
                    backdrop: 'static',
                    keyboard: false
                });
                orderModal.show();

                document.querySelectorAll('.copy-order-id').forEach(function(copyBtn) {
                    copyBtn.addEventListener('click', function() {
                        let orderTextEl = null;
                        orderTextEl = this.closest('tr')?.querySelector('.order-id-text');
                        if (!orderTextEl) {
                            orderTextEl = this.parentElement.querySelector('.order-id-text');
                        }
                        const orderText = orderTextEl?.textContent.trim();
                        if (orderText) {
                            navigator.clipboard.writeText(orderText).then(() => {
                                toastr.success('Order ID copied successfully!');
                            }).catch(err => {
                                console.warn('Clipboard error:', err);
                                toastr.warning('Unable to copy. Clipboard requires HTTPS or localhost.');
                            });
                        }
                    });
                });
                const closeBtn = document.getElementById('modal-close-btn');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        setTimeout(() => { orderModal.hide(); }, 600);
                    });
                }
            });
        </script>
    @endif

    @if(Request::is('/') && Cookie::has('popup_banner') === false && empty($orderSuccessIds))
        <script>
            $(document).ready(function () {
                $('#initialModal').modal('show');
            });
        </script>
        @php(Cookie::queue('popup_banner', 'off', 1))
    @endif
@endpush


