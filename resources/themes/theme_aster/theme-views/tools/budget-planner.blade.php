@extends('theme-views.layouts.app')

@section('title', 'Budget Planner - Plan Your Electronics Purchase | Yoola Uganda')

@section('meta')
    <meta name="description" content="Free budget planner tool. Enter your budget and get personalized electronics recommendations. Find TVs, fridges, and appliances that fit your budget in Uganda.">
    <meta name="keywords" content="budget planner uganda, electronics budget, affordable appliances uganda, cheap electronics kampala">
    <meta property="og:title" content="Budget Planner - Plan Your Electronics Purchase | Yoola Uganda">
    <meta property="og:description" content="Enter your budget and get instant recommendations for TVs, fridges, and home appliances in Uganda.">
    <meta property="og:type" content="website">
    <link rel="canonical" href="{{ route('budget-planner') }}">
@endsection

@push('css')
<style>
    .budget-hero {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1a365d 100%);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2rem;
    }
    .budget-input-wrapper {
        position: relative;
        max-width: 400px;
        margin: 0 auto;
    }
    .budget-input {
        font-size: 1.5rem;
        padding: 1rem 1.5rem;
        padding-left: 3rem;
        border-radius: 50px;
        border: 3px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.1);
        color: white;
        width: 100%;
        text-align: center;
    }
    .budget-input::placeholder {
        color: rgba(255,255,255,0.6);
    }
    .budget-input:focus {
        outline: none;
        border-color: white;
        background: rgba(255,255,255,0.2);
    }
    .currency-symbol {
        position: absolute;
        left: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.2rem;
        opacity: 0.8;
    }
    .quick-budget-btn {
        background: rgba(255,255,255,0.15);
        border: 2px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 0.9rem;
    }
    .quick-budget-btn:hover, .quick-budget-btn.active {
        background: white;
        color: var(--primary-color);
    }
    .category-chip {
        display: inline-block;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        background: #f0f0f0;
        margin: 0.25rem;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.85rem;
    }
    .category-chip:hover {
        background: #e0e0e0;
    }
    .category-chip.selected {
        background: var(--primary-color);
        color: white;
    }
    .plan-btn {
        background: #10b981;
        color: white;
        border: none;
        padding: 1rem 3rem;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    .plan-btn:hover {
        background: #059669;
        transform: translateY(-2px);
    }
    .plan-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    .combination-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 1.5rem;
        border: 2px solid transparent;
        transition: all 0.3s;
    }
    .combination-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    }
    .combination-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .combination-icon {
        font-size: 2rem;
    }
    .combination-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }
    .combination-desc {
        color: #666;
        font-size: 0.9rem;
        margin: 0;
    }
    .combination-stats {
        display: flex;
        gap: 1.5rem;
        margin: 1rem 0;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
    }
    .stat-item {
        text-align: center;
    }
    .stat-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary-color);
    }
    .stat-label {
        font-size: 0.75rem;
        color: #666;
    }
    .product-mini-card {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
    }
    .product-mini-card:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }
    .product-mini-img {
        width: 50px;
        height: 50px;
        object-fit: contain;
        border-radius: 6px;
        background: white;
    }
    .product-mini-info {
        flex: 1;
        min-width: 0;
    }
    .product-mini-name {
        font-size: 0.85rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0;
    }
    .product-mini-category {
        font-size: 0.75rem;
        color: #666;
    }
    .product-mini-price {
        font-weight: 700;
        color: var(--primary-color);
        white-space: nowrap;
    }
    .remaining-badge {
        display: inline-block;
        background: #dcfce7;
        color: #166534;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .results-section {
        display: none;
    }
    .results-section.show {
        display: block;
    }
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 3rem;
    }
    .loading-spinner.show {
        display: block;
    }
    .no-results {
        text-align: center;
        padding: 3rem;
        color: #666;
    }
    .whatsapp-cta {
        background: #25d366;
        color: white;
        padding: 1rem;
        border-radius: 10px;
        text-align: center;
        margin-top: 2rem;
    }
    .whatsapp-cta a {
        color: white;
        text-decoration: none;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
    <!-- Hero Section -->
    <div class="budget-hero">
        <div class="container">
            <div class="text-center">
                <h1 class="mb-3">🎯 Budget Planner</h1>
                <p class="mb-4 opacity-75">Tell us your budget, we'll find the perfect electronics for you</p>
                
                <div class="budget-input-wrapper mb-4">
                    <span class="currency-symbol">UGX</span>
                    <input type="text" 
                           id="budgetInput" 
                           class="budget-input" 
                           placeholder="Enter your budget..."
                           inputmode="numeric">
                </div>

                <!-- Quick Budget Buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                    @foreach($popularBudgets as $budget)
                        <button class="quick-budget-btn" data-amount="{{ $budget['amount'] }}">
                            {{ $budget['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Category Filter -->
        <div class="text-center mb-4">
            <p class="text-muted mb-2">Filter by category (optional):</p>
            <div class="category-chips">
                @foreach($categories as $category)
                    <span class="category-chip" data-id="{{ $category->id }}">
                        {{ $category->name }} ({{ $category->product_count }})
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Plan Button -->
        <div class="text-center mb-5">
            <button id="planBtn" 
                    class="plan-btn" 
                    disabled
                    data-url="{{ route('budget-planner.recommendations') }}"
                    data-csrf="{{ csrf_token() }}">
                <i class="bi bi-calculator me-2"></i> Plan My Purchase
            </button>
        </div>

        <!-- Loading -->
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Finding the best options for your budget...</p>
        </div>

        <!-- Results Section -->
        <div class="results-section" id="resultsSection">
            <h2 class="text-center mb-4">
                <span id="resultsBudget"></span> Budget Plan
            </h2>
            <p class="text-center text-muted mb-4">
                <span id="totalProductsCount"></span> products fit your budget
            </p>

            <!-- Combinations -->
            <div class="row" id="combinationsContainer">
                <!-- Filled by JS -->
            </div>

            <!-- All Affordable Products -->
            <div class="mt-5">
                <h3 class="mb-4">💡 All Products In Your Budget</h3>
                <div class="row" id="allProductsContainer">
                    <!-- Filled by JS -->
                </div>
            </div>

            <!-- WhatsApp CTA -->
            <div class="whatsapp-cta">
                <p class="mb-2">Need help deciding? Chat with us!</p>
                <a href="https://wa.me/256704229768?text=Hi%20Yoola!%20I'm%20planning%20to%20buy%20electronics" target="_blank" id="whatsappLink">
                    <i class="bi bi-whatsapp me-2"></i> WhatsApp Us Now
                </a>
            </div>
        </div>
    </div>

    <!-- Schema Markup -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebApplication",
        "name": "Yoola Budget Planner",
        "description": "Free tool to plan your electronics purchase within your budget in Uganda",
        "url": "{{ route('budget-planner') }}",
        "applicationCategory": "ShoppingApplication",
        "operatingSystem": "Web Browser",
        "offers": {
            "@@type": "Offer",
            "price": "0",
            "priceCurrency": "UGX"
        },
        "provider": {
            "@@type": "Organization",
            "name": "Yoola Uganda",
            "url": "https://yoola.ug"
        }
    }
    </script>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const budgetInput = document.getElementById('budgetInput');
    const planBtn = document.getElementById('planBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const resultsSection = document.getElementById('resultsSection');
    const quickBtns = document.querySelectorAll('.quick-budget-btn');
    const categoryChips = document.querySelectorAll('.category-chip');
    
    let selectedCategories = [];

    // Get URL and CSRF from button data attributes
    const apiUrl = planBtn.dataset.url;
    const csrfToken = planBtn.dataset.csrf;

    // Format number with commas
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Parse formatted number
    function parseFormattedNumber(str) {
        return parseInt(str.replace(/,/g, '')) || 0;
    }

    // Budget input formatting
    budgetInput.addEventListener('input', function(e) {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value) {
            this.value = formatNumber(parseInt(value));
            planBtn.disabled = false;
        } else {
            this.value = '';
            planBtn.disabled = true;
        }
    });

    // Quick budget buttons
    quickBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            quickBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            budgetInput.value = formatNumber(this.dataset.amount);
            planBtn.disabled = false;
        });
    });

    // Category chips
    categoryChips.forEach(chip => {
        chip.addEventListener('click', function() {
            this.classList.toggle('selected');
            const id = parseInt(this.dataset.id);
            if (this.classList.contains('selected')) {
                selectedCategories.push(id);
            } else {
                selectedCategories = selectedCategories.filter(c => c !== id);
            }
        });
    });

    // Plan button
    planBtn.addEventListener('click', async function() {
        const budget = parseFormattedNumber(budgetInput.value);
        if (budget <= 0) return;

        // Show loading
        resultsSection.classList.remove('show');
        loadingSpinner.classList.add('show');
        planBtn.disabled = true;

        try {
            console.log('Fetching recommendations...', apiUrl);
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    budget: budget,
                    categories: selectedCategories
                })
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Data:', data);
            
            if (data.error) {
                alert(data.error);
                return;
            }

            displayResults(data);
        } catch (error) {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
        } finally {
            loadingSpinner.classList.remove('show');
            planBtn.disabled = false;
        }
    });

    function displayResults(data) {
        document.getElementById('resultsBudget').textContent = data.formatted_budget;
        document.getElementById('totalProductsCount').textContent = data.total_products_in_budget;

        // Render combinations
        const combinationsHtml = data.combinations.map(combo => `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="combination-card">
                    <div class="combination-header">
                        <span class="combination-icon">${combo.icon}</span>
                        <div>
                            <h3 class="combination-title">${combo.name}</h3>
                            <p class="combination-desc">${combo.description}</p>
                        </div>
                    </div>
                    <div class="combination-stats">
                        <div class="stat-item">
                            <div class="stat-value">${combo.item_count}</div>
                            <div class="stat-label">Items</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${formatNumber(Math.round(combo.total))}/=</div>
                            <div class="stat-label">Total</div>
                        </div>
                        <div class="stat-item">
                            <span class="remaining-badge">Save ${formatNumber(Math.round(combo.remaining))}/=</span>
                        </div>
                    </div>
                    <div class="combination-products">
                        ${combo.items.map(item => `
                            <a href="${item.url}" class="product-mini-card">
                                <img src="${item.image}" alt="${item.name}" class="product-mini-img">
                                <div class="product-mini-info">
                                    <p class="product-mini-name">${item.name}</p>
                                    <span class="product-mini-category">${item.category}</span>
                                </div>
                                <span class="product-mini-price">${item.formatted_price}</span>
                            </a>
                        `).join('')}
                    </div>
                </div>
            </div>
        `).join('');

        document.getElementById('combinationsContainer').innerHTML = combinationsHtml;

        // Render all products
        const productsHtml = data.all_affordable.map(item => `
            <div class="col-lg-3 col-md-4 col-6 mb-3">
                <a href="${item.url}" class="product-mini-card flex-column text-center p-3">
                    <img src="${item.image}" alt="${item.name}" class="product-mini-img mb-2" style="width:80px;height:80px;">
                    <p class="product-mini-name mb-1">${item.name}</p>
                    <span class="product-mini-price">${item.formatted_price}</span>
                </a>
            </div>
        `).join('');

        document.getElementById('allProductsContainer').innerHTML = productsHtml;

        // Update WhatsApp link with actual budget
        const whatsappLink = document.getElementById('whatsappLink');
        whatsappLink.href = `https://wa.me/256704229768?text=Hi%20Yoola!%20I'm%20planning%20to%20buy%20electronics%20with%20a%20budget%20of%20${encodeURIComponent(data.formatted_budget)}`;

        resultsSection.classList.add('show');
        resultsSection.scrollIntoView({ behavior: 'smooth' });
    }
});
</script>
@endpush
