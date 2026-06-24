@extends('theme-views.layouts.app')

{{-- SEO OPTIMIZATION --}}
@section('title', 'Free Power Calculator | Save Money on Electricity | Yoola Uganda')
@section('meta')
    <meta name="description" content="Calculate your exact UEDCL bill & discover how to cut it by 40%. Free instant results. Used by 2,847+ Ugandan homes.">
    <meta name="keywords" content="power calculator Uganda, UEDCL bill calculator, electricity savings, solar calculator Uganda, inverter calculator">
    <meta property="og:title" content="Free Power Calculator - See Exactly Where Your Money Goes">
    <meta property="og:description" content="Discover your real electricity costs in 60 seconds. Plus get personalized recommendations to slash your bill.">
@endsection

@push('css_or_js')
<style>
    :root {
        --yoola-primary: #9e0000; 
        --yoola-primary-light: #fff0f0; 
        --yoola-accent: #FFD700;
        --yoola-text-dark: #2D2D2D;
        --yoola-green: #10b981;
    }

    .power-calc-page {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
        padding-bottom: 60px;
    }

    /* Hero Section */
    .calc-hero {
        background: linear-gradient(135deg, var(--yoola-primary) 0%, #7a0000 100%);
        color: white;
        padding: 3rem 1.5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .calc-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    }

    .hero-badge {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 50px;
        padding: 8px 20px;
        font-size: 0.85rem;
        display: inline-block;
        margin-bottom: 15px;
        border: 1px solid rgba(255,255,255,0.3);
    }

    .hero-badge .pulse-dot {
        width: 8px;
        height: 8px;
        background: #4ade80;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }

    .calc-hero h1 {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 10px;
        position: relative;
        color: white !important;
    }

    .calc-hero .subheadline {
        font-size: 1.1rem;
        opacity: 0.95;
        max-width: 500px;
        margin: 0 auto;
        color: white !important;
    }

    .trust-strip {
        background: rgba(0,0,0,0.2);
        padding: 12px;
        margin-top: 20px;
        display: flex;
        justify-content: center;
        gap: 25px;
        flex-wrap: wrap;
        font-size: 0.85rem;
    }

    .trust-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Main Card */
    .yoola-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
        background: white;
        overflow: hidden;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .section-number {
        width: 32px;
        height: 32px;
        background: var(--yoola-primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .section-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--yoola-text-dark);
        margin: 0;
    }

    /* Form Styling */
    .yoola-input {
        border: 2px solid #e5e7eb;
        background-color: #f9fafb;
        border-radius: 12px;
        padding: 14px 16px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .yoola-input:focus {
        background-color: white;
        border-color: var(--yoola-primary);
        box-shadow: 0 0 0 4px rgba(158, 0, 0, 0.1);
    }

    .appliance-row {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.3s;
    }

    .appliance-row:hover {
        border-color: var(--yoola-primary);
        box-shadow: 0 4px 15px rgba(158, 0, 0, 0.08);
    }

    /* Quick Add Presets */
    .preset-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 10px;
        margin-bottom: 20px;
    }

    .preset-btn {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 10px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }

    .preset-btn:hover {
        border-color: var(--yoola-primary);
        background: var(--yoola-primary-light);
    }

    .preset-btn .emoji {
        font-size: 1.5rem;
    }

    /* CTA Buttons */
    .btn-calculate {
        background: linear-gradient(135deg, var(--yoola-primary) 0%, #7a0000 100%);
        color: white;
        border: none;
        border-radius: 14px;
        padding: 18px 30px;
        font-size: 1.1rem;
        font-weight: 700;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .btn-calculate:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(158, 0, 0, 0.4);
    }

    .btn-whatsapp {
        background: #25D366;
        color: white;
        border: none;
        border-radius: 14px;
        padding: 16px 25px;
        font-size: 1rem;
        font-weight: 700;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-whatsapp:hover {
        background: #128C7E;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 211, 102, 0.4);
    }

    /* Results Section */
    .results-section {
        display: none;
    }

    .results-section.show {
        display: block;
        animation: slideUp 0.6s ease;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .result-hero {
        background: linear-gradient(135deg, var(--yoola-primary-light) 0%, #ffe5e5 100%);
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        margin-bottom: 25px;
        border: 2px solid var(--yoola-primary);
    }

    .result-hero .big-number {
        font-size: 3rem;
        font-weight: 800;
        color: var(--yoola-primary);
        line-height: 1;
    }

    .result-hero .label {
        color: #666;
        font-size: 0.9rem;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 1px;
    }

    /* Money Wasted Section - Pain Point */
    .pain-card {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 20px;
        border-left: 5px solid #f59e0b;
    }

    .pain-card h4 {
        color: #92400e;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .pain-card .money-lost {
        font-size: 2rem;
        font-weight: 800;
        color: #dc2626;
    }

    /* Solution Cards */
    .solution-section {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border-radius: 20px;
        padding: 25px;
        margin-top: 25px;
    }

    .solution-section h3 {
        color: #065f46;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .solution-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 15px;
        border: 2px solid #10b981;
        cursor: pointer;
        transition: all 0.3s;
    }

    .solution-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.2);
    }

    .solution-card .savings {
        background: #10b981;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    /* Lead Capture Modal */
    .lead-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }

    .lead-modal.show {
        display: flex;
    }

    .lead-modal-content {
        background: white;
        border-radius: 24px;
        padding: 35px;
        max-width: 450px;
        width: 100%;
        text-align: center;
        animation: modalPop 0.4s ease;
    }

    @keyframes modalPop {
        from { opacity: 0; transform: scale(0.8); }
        to { opacity: 1; transform: scale(1); }
    }

    .lead-modal h3 {
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 10px;
        color: var(--yoola-text-dark);
    }

    .lead-modal p {
        color: #666;
        margin-bottom: 25px;
    }

    .lead-form-input {
        width: 100%;
        padding: 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 1rem;
        margin-bottom: 12px;
        transition: all 0.3s;
    }

    .lead-form-input:focus {
        border-color: var(--yoola-primary);
        outline: none;
    }

    /* Urgency Banner */
    .urgency-banner {
        background: linear-gradient(90deg, #dc2626 0%, #b91c1c 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }

    .urgency-banner .icon {
        font-size: 1.5rem;
        animation: shake 0.5s infinite;
    }

    @keyframes shake {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-10deg); }
        75% { transform: rotate(10deg); }
    }

    /* Product Recommendations */
    .product-rec-section {
        margin-top: 30px;
    }

    .product-rec-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 15px;
        border: 2px solid #e5e7eb;
        display: flex;
        gap: 15px;
        align-items: center;
        transition: all 0.3s;
    }

    .product-rec-card:hover {
        border-color: var(--yoola-primary);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .product-rec-card img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 12px;
    }

    .product-rec-card .match-badge {
        background: var(--yoola-green);
        color: white;
        font-size: 0.75rem;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 700;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .calc-hero h1 { font-size: 1.7rem; }
        .result-hero .big-number { font-size: 2.2rem; }
        .preset-grid { grid-template-columns: repeat(3, 1fr); }
        .trust-strip { flex-direction: column; gap: 10px; }
    }
</style>
@endpush

@section('content')
<div class="power-calc-page">

    {{-- HERO SECTION --}}
    <div class="calc-hero">
        <div class="hero-badge">
            <span class="pulse-dot"></span>
            <span id="liveCounter">2,847</span> Ugandans used this today
        </div>
        <h1>💡 See Exactly Where Your Money Goes</h1>
        <p class="subheadline">Calculate your real electricity cost in 60 seconds. Plus discover how to cut your bill by up to 40%.</p>
        
        <div class="trust-strip">
            <div class="trust-item">✅ 100% Free</div>
            <div class="trust-item">⚡ Instant Results</div>
            <div class="trust-item">🇺🇬 UEDCL Rates 2026</div>
            <div class="trust-item">🔒 No Spam</div>
        </div>
    </div>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- CALCULATOR CARD --}}
                <div class="yoola-card p-4">
                    
                    {{-- STEP 1: Quick Add Presets --}}
                    <div class="section-header">
                        <div class="section-number">1</div>
                        <h2 class="section-title">Quick Add Common Appliances</h2>
                    </div>
                    
                    <div class="preset-grid">
                        <button type="button" class="preset-btn" onclick="addPreset('Fridge', 150, 24)">
                            <span class="emoji">🧊</span>
                            <span>Fridge</span>
                        </button>
                        <button type="button" class="preset-btn" onclick="addPreset('TV 43 inch', 80, 6)">
                            <span class="emoji">📺</span>
                            <span>TV</span>
                        </button>
                        <button type="button" class="preset-btn" onclick="addPreset('Ceiling Fan', 75, 8)">
                            <span class="emoji">🌀</span>
                            <span>Fan</span>
                        </button>
                        <button type="button" class="preset-btn" onclick="addPreset('Iron Box', 1200, 0.5)">
                            <span class="emoji">👔</span>
                            <span>Iron</span>
                        </button>
                        <button type="button" class="preset-btn" onclick="addPreset('Kettle', 1500, 0.3)">
                            <span class="emoji">☕</span>
                            <span>Kettle</span>
                        </button>
                        <button type="button" class="preset-btn" onclick="addPreset('Laptop', 60, 8)">
                            <span class="emoji">💻</span>
                            <span>Laptop</span>
                        </button>
                        <button type="button" class="preset-btn" onclick="addPreset('AC (1.5HP)', 1500, 8)">
                            <span class="emoji">❄️</span>
                            <span>AC</span>
                        </button>
                        <button type="button" class="preset-btn" onclick="addPreset('LED Bulbs (x5)', 45, 6)">
                            <span class="emoji">💡</span>
                            <span>Bulbs</span>
                        </button>
                    </div>

                    {{-- STEP 2: Your Appliances --}}
                    <div class="section-header">
                        <div class="section-number">2</div>
                        <h2 class="section-title">Your Appliances</h2>
                    </div>

                    <div id="applianceList">
                        {{-- Appliances will be added here --}}
                    </div>

                    <button type="button" class="btn btn-outline-secondary w-100 mb-4 rounded-pill" onclick="addEmptyRow()">
                        + Add Custom Appliance
                    </button>

                    {{-- CALCULATE BUTTON --}}
                    <button type="button" class="btn-calculate" onclick="calculateAndCapture()">
                        <span style="font-size: 1.3rem;">⚡</span> Calculate My Bill & Savings
                    </button>

                    <p class="text-center text-muted mt-3 mb-0" style="font-size: 0.85rem;">
                        🔒 Your data stays private. No spam, ever.
                    </p>
                </div>

                {{-- RESULTS SECTION (Hidden initially) --}}
                <div id="resultsSection" class="results-section mt-4">
                    
                    {{-- Main Result --}}
                    <div class="result-hero">
                        <div class="label">Your Monthly Electricity Bill</div>
                        <div class="big-number">UGX <span id="monthlyBill">0</span></div>
                        <div class="mt-2">
                            <span id="kwhDisplay" class="text-muted">0 kWh/month</span>
                            <span id="lifelineBadge" class="badge bg-success ms-2" style="display:none;">✅ Lifeline Rate</span>
                        </div>
                    </div>

                    {{-- Pain Point: Money Wasted --}}
                    <div class="pain-card">
                        <h4>⚠️ Here's What This Means...</h4>
                        <p class="mb-2">If electricity prices go up just 10%, you'll pay an extra:</p>
                        <div class="money-lost">UGX <span id="extraCost">0</span>/year</div>
                        <p class="mt-2 mb-0 text-muted" style="font-size: 0.9rem;">
                            Plus, every power cut costs you productivity and spoiled food.
                        </p>
                    </div>

                    {{-- Solutions --}}
                    <div class="solution-section">
                        <h3>✅ Here's How To Cut Your Bill</h3>

                        <div class="solution-card" onclick="openWhatsApp('energy-efficient')">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>🔄 Switch to Energy-Efficient Appliances</strong>
                                </div>
                                <span class="savings">Save 20-40%</span>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                Our inverter fridges & AC units use 40% less power. Pay less every month, forever.
                            </p>
                        </div>

                        <div class="solution-card" onclick="openWhatsApp('backup-power')">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>🔋 Get Backup Power (Never Lose Power Again)</strong>
                                </div>
                                <span class="savings">From 450K</span>
                            </div>
                            <p class="text-muted mb-2" style="font-size: 0.9rem;">
                                Your home needs a <strong><span id="batterySize">100</span>Ah battery</strong> for 4-hour backup.
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-light text-dark">Inverter: <span id="inverterSize">1000</span>VA</span>
                                <span class="badge bg-light text-dark">Runs: <span id="backupDevices">Fridge + TV + Lights</span></span>
                            </div>
                        </div>

                        <div class="solution-card" onclick="openWhatsApp('solar')">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>☀️ Go Solar (Stop Paying UEDCL)</strong>
                                </div>
                                <span class="savings">Save 100%</span>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                                You need <strong><span id="solarPanels">2</span> panels (450W each)</strong> to power your home. Pay once, save for 25 years.
                            </p>
                        </div>
                    </div>

                    {{-- WhatsApp CTA --}}
                    <div class="mt-4">
                        <button type="button" class="btn-whatsapp" onclick="openWhatsApp('consultation')">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Chat With Us on WhatsApp
                        </button>
                        <p class="text-center text-muted mt-2" style="font-size: 0.85rem;">
                            💬 Get expert advice in 2 minutes • Free delivery in Kampala
                        </p>
                    </div>

                    {{-- Recalculate --}}
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-link text-muted" onclick="resetCalculator()">
                            ↻ Calculate Again
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- LEAD CAPTURE MODAL --}}
<div id="leadModal" class="lead-modal">
    <div class="lead-modal-content">
        <h3>🎉 Your Results Are Ready!</h3>
        <p>Enter your WhatsApp number to receive your personalized savings report & product recommendations.</p>
        
        <form id="leadForm" onsubmit="submitLead(event)">
            <input type="text" id="leadName" class="lead-form-input" placeholder="Your Name" required>
            <input type="tel" id="leadPhone" class="lead-form-input" placeholder="WhatsApp Number (e.g. 0780123456)" required>
            
            <button type="submit" class="btn-calculate mt-2">
                📊 Show My Results
            </button>
        </form>

        <p class="mt-3 mb-0" style="font-size: 0.8rem; color: #999;">
            🔒 We'll only send helpful tips. Unsubscribe anytime.
        </p>
        
        <button type="button" class="btn btn-link text-muted mt-2" onclick="skipLead()" style="font-size: 0.85rem;">
            Skip for now →
        </button>
    </div>
</div>

<script>
    "use strict";
    
    // UEDCL Q4 2026 Tariffs
    const RATE_LIFELINE = 250.0;
    const RATE_TIER_1 = 756.2;
    const RATE_TIER_2 = 412.0;
    const RATE_TIER_3 = 756.2;
    const LIMIT_LIFELINE = 15;
    const LIMIT_TIER_1 = 80;
    const LIMIT_TIER_2 = 150;
    const LIMIT_ELIGIBILITY = 100;
    const DAYS_PER_MONTH = 30;

    // Store calculation data for WhatsApp message
    let calculationData = {};

    // Live counter animation
    function animateCounter() {
        const counter = document.getElementById('liveCounter');
        let base = 2847;
        setInterval(() => {
            base += Math.floor(Math.random() * 3);
            counter.textContent = base.toLocaleString();
        }, 30000);
    }
    animateCounter();

    // Add preset appliance
    function addPreset(name, wattage, hours) {
        const list = document.getElementById('applianceList');
        const row = createApplianceRow(name, wattage, hours);
        list.appendChild(row);
        row.style.animation = 'slideUp 0.3s ease';
    }

    // Add empty row
    function addEmptyRow() {
        const list = document.getElementById('applianceList');
        const row = createApplianceRow('', '', '');
        list.appendChild(row);
        row.querySelector('.appliance-name').focus();
    }

    // Create appliance row
    function createApplianceRow(name, wattage, hours) {
        const row = document.createElement('div');
        row.className = 'appliance-row';
        row.innerHTML = `
            <div class="row g-2 align-items-end">
                <div class="col-5 col-md-5">
                    <label class="form-label small text-muted">Appliance</label>
                    <input type="text" class="form-control yoola-input appliance-name" placeholder="e.g. Water Heater" value="${name}">
                </div>
                <div class="col-3 col-md-3">
                    <label class="form-label small text-muted">Watts</label>
                    <input type="number" class="form-control yoola-input appliance-wattage" placeholder="100" value="${wattage}" min="0">
                </div>
                <div class="col-2 col-md-2">
                    <label class="form-label small text-muted">Hrs/Day</label>
                    <input type="number" class="form-control yoola-input appliance-hours" placeholder="4" value="${hours}" min="0" step="0.5">
                </div>
                <div class="col-2 col-md-2">
                    <button type="button" class="btn btn-outline-danger w-100 rounded-pill" onclick="removeRow(this)">✕</button>
                </div>
            </div>
        `;
        return row;
    }

    // Remove row
    function removeRow(btn) {
        const row = btn.closest('.appliance-row');
        row.style.opacity = '0';
        row.style.transform = 'translateX(20px)';
        setTimeout(() => row.remove(), 200);
    }

    // Calculate and show lead modal
    function calculateAndCapture() {
        // Validate
        const rows = document.querySelectorAll('.appliance-row');
        if (rows.length === 0) {
            alert('Please add at least one appliance');
            return;
        }

        // Calculate first
        doCalculation();

        // Show lead capture modal (can be skipped)
        document.getElementById('leadModal').classList.add('show');
    }

    // Do the actual calculation
    function doCalculation() {
        const rows = document.querySelectorAll('.appliance-row');
        let totalDailyWh = 0;
        let totalWattage = 0;
        let appliances = [];

        rows.forEach(row => {
            const name = row.querySelector('.appliance-name').value.trim();
            const wattage = parseFloat(row.querySelector('.appliance-wattage').value) || 0;
            const hours = parseFloat(row.querySelector('.appliance-hours').value) || 0;

            if (wattage > 0 && hours > 0) {
                totalDailyWh += wattage * hours;
                totalWattage += wattage;
                appliances.push({ name: name || 'Appliance', wattage, hours });
            }
        });

        const monthlyKwh = (totalDailyWh / 1000) * DAYS_PER_MONTH;
        let monthlyCost = 0;
        let lifelineEligible = false;

        // Calculate bill
        if (monthlyKwh <= LIMIT_ELIGIBILITY) {
            lifelineEligible = true;
            if (monthlyKwh <= LIMIT_LIFELINE) {
                monthlyCost = monthlyKwh * RATE_LIFELINE;
            } else if (monthlyKwh <= LIMIT_TIER_1) {
                monthlyCost = (LIMIT_LIFELINE * RATE_LIFELINE) + ((monthlyKwh - LIMIT_LIFELINE) * RATE_TIER_1);
            } else {
                monthlyCost = (LIMIT_LIFELINE * RATE_LIFELINE) + ((LIMIT_TIER_1 - LIMIT_LIFELINE) * RATE_TIER_1) + ((monthlyKwh - LIMIT_TIER_1) * RATE_TIER_2);
            }
        } else {
            if (monthlyKwh <= LIMIT_TIER_2) {
                monthlyCost = (LIMIT_TIER_1 * RATE_TIER_1) + ((monthlyKwh - LIMIT_TIER_1) * RATE_TIER_2);
            } else {
                monthlyCost = (LIMIT_TIER_1 * RATE_TIER_1) + ((LIMIT_TIER_2 - LIMIT_TIER_1) * RATE_TIER_2) + ((monthlyKwh - LIMIT_TIER_2) * RATE_TIER_3);
            }
        }

        // Backup calculations
        const batteryAh = Math.ceil((totalWattage * 4) / (0.8 * 12) / 10) * 10; // 4hr backup, 80% DoD, 12V
        const inverterVa = Math.ceil((totalWattage * 1.2) / 500) * 500;
        const solarPanels = Math.ceil((totalDailyWh / 5) * 1.3 / 450);

        // Store for WhatsApp
        calculationData = {
            monthlyKwh: monthlyKwh.toFixed(1),
            monthlyCost: Math.round(monthlyCost),
            batteryAh,
            inverterVa,
            solarPanels,
            totalWattage,
            appliances,
            lifelineEligible
        };

        // Update UI
        document.getElementById('monthlyBill').textContent = monthlyCost.toLocaleString('en-UG', {maximumFractionDigits: 0});
        document.getElementById('kwhDisplay').textContent = monthlyKwh.toFixed(1) + ' kWh/month';
        document.getElementById('extraCost').textContent = (monthlyCost * 0.1 * 12).toLocaleString('en-UG', {maximumFractionDigits: 0});
        document.getElementById('batterySize').textContent = batteryAh;
        document.getElementById('inverterSize').textContent = inverterVa;
        document.getElementById('solarPanels').textContent = solarPanels;
        
        if (lifelineEligible) {
            document.getElementById('lifelineBadge').style.display = 'inline-block';
        }

        // List backup devices
        const topDevices = appliances.slice(0, 3).map(a => a.name).join(' + ');
        document.getElementById('backupDevices').textContent = topDevices || 'Your appliances';
    }

    // Submit lead
    function submitLead(e) {
        e.preventDefault();
        const name = document.getElementById('leadName').value;
        const phone = document.getElementById('leadPhone').value;

        // Store lead (you can send to backend)
        console.log('Lead captured:', { name, phone, ...calculationData });
        
        // Send to backend
        fetch('/power-calculator/capture-lead', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="_token"]').content
            },
            body: JSON.stringify({ name, phone, calculation: calculationData })
        }).catch(err => console.log('Lead save error:', err));

        closeModalAndShowResults();
    }

    // Skip lead capture
    function skipLead() {
        closeModalAndShowResults();
    }

    // Close modal and show results
    function closeModalAndShowResults() {
        document.getElementById('leadModal').classList.remove('show');
        document.getElementById('resultsSection').classList.add('show');
        document.getElementById('resultsSection').scrollIntoView({ behavior: 'smooth' });
    }

    // Open WhatsApp with context
    function openWhatsApp(type) {
        const phone = '+256704229768';
        let message = '';

        switch(type) {
            case 'energy-efficient':
                message = `Hi Yoola! 👋\n\nI just used your Power Calculator and my monthly bill is UGX ${calculationData.monthlyCost?.toLocaleString()}.\n\nI'm interested in energy-efficient appliances to reduce my bill. What do you recommend?`;
                break;
            case 'backup-power':
                message = `Hi Yoola! 👋\n\nI need backup power for my home.\n\nMy requirements:\n• Total load: ${calculationData.totalWattage}W\n• Battery needed: ${calculationData.batteryAh}Ah\n• Inverter needed: ${calculationData.inverterVa}VA\n\nWhat's the best package you have?`;
                break;
            case 'solar':
                message = `Hi Yoola! ☀️\n\nI want to stop paying UEDCL bills!\n\nMy usage: ${calculationData.monthlyKwh} kWh/month\nRecommended: ${calculationData.solarPanels} solar panels (450W each)\n\nCan you quote me for a full solar system?`;
                break;
            default:
                message = `Hi Yoola! 👋\n\nI used your Power Calculator.\n\nMy bill: UGX ${calculationData.monthlyCost?.toLocaleString()}/month\nUsage: ${calculationData.monthlyKwh} kWh\n\nI'd like to know how to save on electricity.`;
        }

        const url = `https://wa.me/${phone.replace('+', '')}?text=${encodeURIComponent(message)}`;
        window.open(url, '_blank');
    }

    // Reset calculator
    function resetCalculator() {
        document.getElementById('resultsSection').classList.remove('show');
        document.getElementById('applianceList').innerHTML = '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Add default appliance
        addPreset('Refrigerator', 150, 24);
    });
</script>
@endsection
