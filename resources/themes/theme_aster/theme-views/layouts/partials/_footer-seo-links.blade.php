{{-- SEO Internal Links Section --}}
<div class="footer-seo-links py-3 border-top">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h6 class="text-white mb-2">Popular Categories</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('products', ['category_id' => 5]) }}" class="text-white-50">TVs & Entertainment</a></li>
                    <li><a href="{{ route('products', ['category_id' => 1]) }}" class="text-white-50">Kitchen Appliances</a></li>
                    <li><a href="{{ route('products', ['category_id' => 4]) }}" class="text-white-50">Air Conditioners</a></li>
                    <li><a href="{{ route('products', ['category_id' => 3]) }}" class="text-white-50">Washing Machines</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="text-white mb-2">Top Brands</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ url('/brand/samsung') }}" class="text-white-50">Samsung Uganda</a></li>
                    <li><a href="{{ url('/brand/hisense') }}" class="text-white-50">Hisense Uganda</a></li>
                    <li><a href="{{ url('/brand/lg') }}" class="text-white-50">LG Uganda</a></li>
                    <li><a href="{{ url('/brand/jbl') }}" class="text-white-50">JBL Speakers</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="text-white mb-2">Buying Guides</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ url('/blog/best-smart-tvs-uganda-2026') }}" class="text-white-50">Best TVs 2026</a></li>
                    <li><a href="{{ url('/blog/buy-tvs-kampala-uganda') }}" class="text-white-50">Buy TVs Kampala</a></li>
                    <li><a href="{{ url('/blog/samsung-tv-prices-uganda') }}" class="text-white-50">Samsung Prices</a></li>
                    <li><a href="{{ url('/blog/hisense-tv-prices-uganda') }}" class="text-white-50">Hisense Prices</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
