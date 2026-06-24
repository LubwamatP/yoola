{{-- Amazon-Style Product Filters --}}
<style>
    .amazon-filter-section {
        border-bottom: 1px solid #e7e7e7;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
    .amazon-filter-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    .amazon-filter-title {
        font-size: 14px;
        font-weight: 700;
        color: #0F1111;
        margin-bottom: 0.75rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .amazon-filter-title .collapse-icon {
        transition: transform 0.2s;
    }
    .amazon-filter-title.collapsed .collapse-icon {
        transform: rotate(-90deg);
    }
    .amazon-filter-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .amazon-filter-list li {
        padding: 4px 0;
    }
    .amazon-filter-list label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 13px;
        color: #0F1111;
        transition: color 0.15s;
    }
    .amazon-filter-list label:hover {
        color: #C45500;
    }
    .amazon-filter-list .filter-count {
        color: #565959;
        font-size: 12px;
        margin-left: auto;
    }
    .amazon-filter-checkbox {
        width: 16px;
        height: 16px;
        border: 1px solid #888;
        border-radius: 2px;
        cursor: pointer;
    }
    .amazon-filter-checkbox:checked {
        background-color: #007185;
        border-color: #007185;
    }
    .amazon-price-btn {
        display: block;
        padding: 6px 12px;
        margin: 4px 0;
        background: #f7f7f7;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
        color: #0F1111;
        text-decoration: none;
        transition: all 0.15s;
        cursor: pointer;
    }
    .amazon-price-btn:hover {
        background: #ededed;
        border-color: #ccc;
        color: #0F1111;
    }
    .amazon-price-btn.active {
        background: #007185;
        border-color: #007185;
        color: #fff;
    }
    .amazon-star-rating {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .amazon-star-rating .star {
        color: #FFA41C;
        font-size: 14px;
    }
    .amazon-star-rating .star-empty {
        color: #ddd;
    }
    .amazon-star-rating .rating-text {
        font-size: 12px;
        color: #007185;
    }
    .amazon-clear-filter {
        font-size: 12px;
        color: #007185;
        text-decoration: none;
        cursor: pointer;
    }
    .amazon-clear-filter:hover {
        text-decoration: underline;
        color: #C45500;
    }
    .amazon-deal-badge {
        display: inline-block;
        background: #CC0C39;
        color: white;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 2px;
        margin-left: 4px;
    }
    .amazon-filter-search {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #888;
        border-radius: 4px;
        font-size: 13px;
        margin-bottom: 8px;
    }
    .amazon-filter-search:focus {
        outline: none;
        border-color: #007185;
        box-shadow: 0 0 0 3px rgba(0,113,133,0.1);
    }
    .amazon-see-more {
        font-size: 12px;
        color: #007185;
        cursor: pointer;
        margin-top: 8px;
        display: inline-block;
    }
    .amazon-see-more:hover {
        text-decoration: underline;
        color: #C45500;
    }
</style>

{{-- Availability Filter --}}
<div class="amazon-filter-section">
    <div class="amazon-filter-title" data-bs-toggle="collapse" data-bs-target="#filter-availability">
        {{ translate('Availability') }}
        <i class="bi bi-chevron-down collapse-icon"></i>
    </div>
    <div id="filter-availability" class="collapse show">
        <ul class="amazon-filter-list">
            <li>
                <label>
                    <input type="checkbox" name="in_stock" value="1" class="amazon-filter-checkbox real-time-action-update"
                        {{ request('in_stock') == 1 ? 'checked' : '' }}>
                    <span>{{ translate('In Stock') }}</span>
                </label>
            </li>
            <li>
                <label>
                    <input type="checkbox" name="include_out_of_stock" value="1" class="amazon-filter-checkbox"
                        {{ request('include_out_of_stock') == 1 ? 'checked' : '' }}>
                    <span>{{ translate('Include Out of Stock') }}</span>
                </label>
            </li>
        </ul>
    </div>
</div>

{{-- Deals & Discounts Filter --}}
<div class="amazon-filter-section">
    <div class="amazon-filter-title" data-bs-toggle="collapse" data-bs-target="#filter-deals">
        {{ translate('Deals & Discounts') }}
        <i class="bi bi-chevron-down collapse-icon"></i>
    </div>
    <div id="filter-deals" class="collapse show">
        <ul class="amazon-filter-list">
            <li>
                <label>
                    <input type="checkbox" name="has_discount" value="1" class="amazon-filter-checkbox real-time-action-update"
                        {{ request('has_discount') == 1 ? 'checked' : '' }}>
                    <span>{{ translate('All Discounts') }}</span>
                    <span class="amazon-deal-badge">{{ translate('SALE') }}</span>
                </label>
            </li>
            <li>
                <label>
                    <input type="checkbox" name="discount_10" value="1" class="amazon-filter-checkbox real-time-action-update"
                        {{ request('discount_10') == 1 ? 'checked' : '' }}>
                    <span>{{ translate('10% off or more') }}</span>
                </label>
            </li>
            <li>
                <label>
                    <input type="checkbox" name="discount_25" value="1" class="amazon-filter-checkbox real-time-action-update"
                        {{ request('discount_25') == 1 ? 'checked' : '' }}>
                    <span>{{ translate('25% off or more') }}</span>
                </label>
            </li>
            <li>
                <label>
                    <input type="checkbox" name="discount_50" value="1" class="amazon-filter-checkbox real-time-action-update"
                        {{ request('discount_50') == 1 ? 'checked' : '' }}>
                    <span>{{ translate('50% off or more') }}</span>
                </label>
            </li>
        </ul>
    </div>
</div>

{{-- Price Filter --}}
<div class="amazon-filter-section">
    <div class="amazon-filter-title" data-bs-toggle="collapse" data-bs-target="#filter-price">
        {{ translate('Price') }}
        <i class="bi bi-chevron-down collapse-icon"></i>
    </div>
    <div id="filter-price" class="collapse show">
        {{-- Quick Price Range Buttons --}}
        <div class="mb-3">
            @php
                $currency = session('currency_symbol', 'UGX');
                $priceRanges = [
                    ['min' => 0, 'max' => 100000, 'label' => translate('Under') . ' 100K'],
                    ['min' => 100000, 'max' => 500000, 'label' => '100K - 500K'],
                    ['min' => 500000, 'max' => 1000000, 'label' => '500K - 1M'],
                    ['min' => 1000000, 'max' => 3000000, 'label' => '1M - 3M'],
                    ['min' => 3000000, 'max' => 5000000, 'label' => '3M - 5M'],
                    ['min' => 5000000, 'max' => null, 'label' => translate('Above') . ' 5M'],
                ];
            @endphp
            @foreach($priceRanges as $range)
                @php
                    $isActive = request('min_price') == $range['min'] && request('max_price') == $range['max'];
                @endphp
                <a href="javascript:void(0)"
                   class="amazon-price-btn {{ $isActive ? 'active' : '' }}"
                   data-min="{{ $range['min'] }}"
                   data-max="{{ $range['max'] ?? '' }}"
                   onclick="setPriceRange({{ $range['min'] }}, {{ $range['max'] ?? 'null' }})">
                    {{ $range['label'] }}
                </a>
            @endforeach
        </div>

        {{-- Custom Price Range --}}
        <div class="d-flex align-items-center gap-2">
            <input type="number" id="min_price" name="min_price" class="amazon-filter-search"
                   placeholder="{{ translate('Min') }}" value="{{ request('min_price') }}" style="width: 45%;">
            <span>-</span>
            <input type="number" id="max_price" name="max_price" class="amazon-filter-search"
                   placeholder="{{ translate('Max') }}" value="{{ request('max_price') }}" style="width: 45%;">
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2 w-100 action-search-products-by-price">
            {{ translate('Go') }}
        </button>
    </div>
</div>

{{-- Customer Reviews Filter --}}
<div class="amazon-filter-section">
    <div class="amazon-filter-title" data-bs-toggle="collapse" data-bs-target="#filter-rating">
        {{ translate('Customer Reviews') }}
        <i class="bi bi-chevron-down collapse-icon"></i>
    </div>
    <div id="filter-rating" class="collapse show">
        <ul class="amazon-filter-list">
            @foreach([4, 3, 2, 1] as $rating)
                <li>
                    <label class="amazon-star-rating">
                        <input type="checkbox" name="rating[]" value="{{ $rating }}"
                               class="amazon-filter-checkbox real-time-action-update review_class_for_tag_{{ $rating }}"
                               {{ in_array($rating, (array)request('rating', [])) ? 'checked' : '' }}>
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $rating ? 'bi-star-fill star' : 'bi-star star-empty' }}"></i>
                        @endfor
                        <span class="rating-text">{{ translate('& Up') }}</span>
                        @if(isset($productRatings['rating_'.$rating]))
                            <span class="filter-count">({{ $productRatings['rating_'.$rating] }})</span>
                        @endif
                    </label>
                </li>
            @endforeach
        </ul>
    </div>
</div>

{{-- Categories Filter --}}
@if(isset($productCategories) && $productCategories->count() > 0)
<div class="amazon-filter-section">
    <div class="amazon-filter-title" data-bs-toggle="collapse" data-bs-target="#filter-category">
        {{ translate('Department') }}
        <i class="bi bi-chevron-down collapse-icon"></i>
    </div>
    <div id="filter-category" class="collapse show">
        <input type="text" class="amazon-filter-search" id="category-search"
               placeholder="{{ translate('Search categories...') }}"
               onkeyup="filterList('category-search', 'category-list')">
        <ul class="amazon-filter-list" id="category-list" style="max-height: 250px; overflow-y: auto;">
            @foreach($productCategories->take(10) as $category)
                <li class="filter-item">
                    <label>
                        <input type="checkbox" name="category_ids[]" value="{{ $category['id'] }}"
                               class="amazon-filter-checkbox real-time-action-update category_class_for_tag_{{ $category['id'] }}"
                               {{ in_array($category['id'], request('category_ids') ?? []) || $category['id'] == request('category_id') ? 'checked' : '' }}>
                        <span>{{ $category['name'] }}</span>
                        <span class="filter-count">({{ $category->product_count ?? 0 }})</span>
                    </label>
                    @if($category->childes && $category->childes->count() > 0)
                        <ul class="amazon-filter-list ps-3 mt-1">
                            @foreach($category->childes->take(5) as $child)
                                <li class="filter-item">
                                    <label>
                                        <input type="checkbox" name="sub_category_ids[]" value="{{ $child['id'] }}"
                                               class="amazon-filter-checkbox real-time-action-update"
                                               {{ in_array($child['id'], request('sub_category_ids') ?? []) ? 'checked' : '' }}>
                                        <span>{{ $child['name'] }}</span>
                                        <span class="filter-count">({{ $child->sub_category_product_count ?? 0 }})</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
        @if($productCategories->count() > 10)
            <span class="amazon-see-more" onclick="toggleSeeMore('category-list', this)">
                {{ translate('See more') }} <i class="bi bi-chevron-down"></i>
            </span>
        @endif
    </div>
</div>
@endif

{{-- Brands Filter --}}
@if($web_config['brand_setting'] && isset($productBrands) && $productBrands->count() > 0)
<div class="amazon-filter-section product-type-physical-section">
    <div class="amazon-filter-title" data-bs-toggle="collapse" data-bs-target="#filter-brand">
        {{ translate('Brands') }}
        <i class="bi bi-chevron-down collapse-icon"></i>
    </div>
    <div id="filter-brand" class="collapse show">
        <input type="text" class="amazon-filter-search" id="brand-search"
               placeholder="{{ translate('Search brands...') }}"
               onkeyup="filterList('brand-search', 'brand-list')">
        <ul class="amazon-filter-list" id="brand-list" style="max-height: 200px; overflow-y: auto;">
            @foreach($productBrands->take(10) as $brand)
                <li class="filter-item">
                    <label>
                        <input type="checkbox" name="brand_ids[]" value="{{ $brand['id'] }}"
                               class="amazon-filter-checkbox real-time-action-update brand_class_for_tag_{{ $brand['id'] }}"
                               {{ in_array($brand['id'], request('brand_ids') ?? []) || $brand['id'] == request('brand_id') ? 'checked' : '' }}>
                        <span>{{ $brand['name'] }}</span>
                        <span class="filter-count">({{ $brand['brand_products_count'] }})</span>
                    </label>
                </li>
            @endforeach
        </ul>
        @if($productBrands->count() > 10)
            <span class="amazon-see-more" onclick="toggleSeeMore('brand-list', this)">
                {{ translate('See more') }} <i class="bi bi-chevron-down"></i>
            </span>
        @endif
    </div>
</div>
@endif

{{-- Product Type Filter --}}
@if($web_config['digital_product_setting'])
<div class="amazon-filter-section">
    <div class="amazon-filter-title" data-bs-toggle="collapse" data-bs-target="#filter-product-type">
        {{ translate('Product Type') }}
        <i class="bi bi-chevron-down collapse-icon"></i>
    </div>
    <div id="filter-product-type" class="collapse show">
        <ul class="amazon-filter-list">
            <li>
                <label>
                    <input type="radio" name="product_type" value="" class="amazon-filter-checkbox real-time-action-update"
                        {{ !request('product_type') ? 'checked' : '' }}>
                    <span>{{ translate('All') }}</span>
                </label>
            </li>
            <li>
                <label>
                    <input type="radio" name="product_type" value="physical" class="amazon-filter-checkbox real-time-action-update"
                        {{ request('product_type') == 'physical' ? 'checked' : '' }}>
                    <span>{{ translate('Physical Products') }}</span>
                </label>
            </li>
            <li>
                <label>
                    <input type="radio" name="product_type" value="digital" class="amazon-filter-checkbox real-time-action-update"
                        {{ request('product_type') == 'digital' ? 'checked' : '' }}>
                    <span>{{ translate('Digital Products') }}</span>
                </label>
            </li>
        </ul>
    </div>
</div>
@endif

{{-- Clear All Filters --}}
@if(request()->hasAny(['category_ids', 'brand_ids', 'min_price', 'max_price', 'rating', 'product_type', 'in_stock', 'has_discount']))
<div class="amazon-filter-section pt-3">
    <a href="{{ url()->current() }}" class="amazon-clear-filter">
        <i class="bi bi-x-circle me-1"></i>{{ translate('Clear all filters') }}
    </a>
</div>
@endif

@push('script')
<script>
    // Price range quick select
    function setPriceRange(min, max) {
        document.getElementById('min_price').value = min;
        document.getElementById('max_price').value = max !== null ? max : '';

        // Update active state on buttons
        document.querySelectorAll('.amazon-price-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');

        // Trigger filter update
        document.querySelector('.action-search-products-by-price').click();
    }

    // Filter list search
    function filterList(searchId, listId) {
        const searchInput = document.getElementById(searchId);
        const list = document.getElementById(listId);
        const filter = searchInput.value.toLowerCase();
        const items = list.querySelectorAll('.filter-item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(filter) ? '' : 'none';
        });
    }

    // See more toggle
    function toggleSeeMore(listId, btn) {
        const list = document.getElementById(listId);
        if (list.style.maxHeight === 'none') {
            list.style.maxHeight = '200px';
            btn.innerHTML = '{{ translate("See more") }} <i class="bi bi-chevron-down"></i>';
        } else {
            list.style.maxHeight = 'none';
            btn.innerHTML = '{{ translate("See less") }} <i class="bi bi-chevron-up"></i>';
        }
    }

    // Collapse toggle
    document.querySelectorAll('.amazon-filter-title[data-bs-toggle="collapse"]').forEach(title => {
        title.addEventListener('click', function() {
            this.classList.toggle('collapsed');
        });
    });
</script>
@endpush
