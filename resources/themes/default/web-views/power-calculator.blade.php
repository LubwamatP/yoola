@extends('layouts.front-end.app')

@section('title', 'Power Cost Calculator | '.$web_config['name']->value)

@push('css_or_js')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    .power-calculator-page {
        font-family: 'Poppins', sans-serif;
        background: #f5f5f5;
        padding: 40px 0;
    }

    .power-calc-container {
        max-width: 950px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .power-calc-header {
        background: linear-gradient(135deg, var(--web-primary) 0%, var(--web-secondary) 100%);
        color: white !important;
        padding: 45px 35px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .power-calc-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        animation: pulse 3s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.3; }
        50% { transform: scale(1.1); opacity: 0.5; }
    }

    .header-icon {
        font-size: 48px;
        margin-bottom: 10px;
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .power-calc-header h1 {
        font-size: 34px;
        font-weight: 700;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        color: white !important;
    }

    .power-calc-header p {
        font-size: 16px;
        font-weight: 300;
        opacity: 0.95;
        position: relative;
        z-index: 1;
        color: white !important;
    }

    .power-calc-content {
        padding: 45px 35px;
    }

    .calc-section {
        margin-bottom: 40px;
    }

    .section-title {
        font-size: 24px;
        font-weight: 600;
        color: var(--web-primary);
        margin-bottom: 25px;
        padding-bottom: 12px;
        border-bottom: 3px solid var(--web-primary);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-icon {
        font-size: 28px;
    }

    .appliance-list {
        margin-bottom: 25px;
    }

    .appliance-row {
        display: grid;
        grid-template-columns: 2.5fr 1.2fr 1.2fr auto;
        gap: 15px;
        margin-bottom: 15px;
        align-items: end;
        padding: 18px;
        background: linear-gradient(135deg, #f9f9f9 0%, #f5f5f5 100%);
        border-radius: 12px;
        border: 2px solid #e5e5e5;
        transition: all 0.3s;
    }

    .appliance-row:hover {
        border-color: var(--web-primary);
        box-shadow: 0 6px 20px var(--web-primary-20);
        transform: translateY(-2px);
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #555;
        margin-bottom: 7px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group input {
        padding: 13px 16px;
        border: 2px solid #e5e5e5;
        border-radius: 10px;
        font-size: 15px;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s;
        background: white;
    }

    .form-group input:focus {
        outline: none;
        border-color: var(--web-primary);
        box-shadow: 0 0 0 4px var(--web-primary-10);
    }

    .form-group input::placeholder {
        color: #bbb;
    }

    .calc-btn {
        padding: 13px 28px;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        font-family: 'Poppins', sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-primary-calc {
        background: var(--web-primary);
        color: white;
        box-shadow: 0 4px 15px var(--web-primary-20);
    }

    .btn-primary-calc:hover {
        background: var(--web-secondary);
        transform: translateY(-3px);
        box-shadow: 0 6px 20px var(--web-primary-40);
    }

    .btn-calculate {
        background: linear-gradient(135deg, var(--web-primary) 0%, var(--web-secondary) 100%);
        color: white;
        width: 100%;
        padding: 20px;
        font-size: 19px;
        box-shadow: 0 6px 25px var(--web-primary-40);
        position: relative;
        overflow: hidden;
    }

    .btn-calculate::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-calculate:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-calculate:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px var(--web-primary-40);
    }

    .btn-danger-calc {
        background: #666;
        color: white;
        padding: 11px 16px;
        font-size: 13px;
    }

    .btn-danger-calc:hover {
        background: var(--web-primary);
        transform: scale(1.05);
    }

    .results {
        display: none;
        background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
        border-radius: 16px;
        padding: 35px;
        margin-top: 40px;
        border: 3px solid var(--web-primary);
        box-shadow: 0 8px 25px var(--web-primary-20);
    }

    .results.show {
        display: block;
        animation: slideIn 0.6s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .results-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .results-header h3 {
        font-size: 26px;
        color: var(--web-primary);
        margin-bottom: 8px;
    }

    .results-header p {
        color: #666;
        font-size: 14px;
    }

    .result-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }

    .result-card {
        background: white;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        border-left: 6px solid var(--web-primary);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .result-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, var(--web-primary-10) 0%, transparent 70%);
    }

    .result-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 35px var(--web-primary-20);
    }

    .result-card .icon {
        font-size: 40px;
        margin-bottom: 15px;
    }

    .result-card .label {
        font-size: 13px;
        color: #666;
        margin-bottom: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .result-card .value {
        font-size: 42px;
        font-weight: 700;
        color: var(--web-primary);
        line-height: 1.1;
        margin-bottom: 8px;
    }

    .result-card .unit {
        font-size: 17px;
        color: #999;
        font-weight: 500;
    }

    .alert-calc {
        background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
        border-left: 5px solid #ffc107;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 30px;
        font-size: 14px;
        color: #856404;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
    }

    .alert-calc strong {
        font-weight: 700;
        color: var(--web-primary);
    }

    @media (max-width: 768px) {
        .appliance-row {
            grid-template-columns: 1fr;
        }

        .power-calc-header h1 {
            font-size: 26px;
        }

        .power-calc-header p {
            font-size: 14px;
        }

        .power-calc-content {
            padding: 30px 20px;
        }

        .result-grid {
            grid-template-columns: 1fr;
        }

        .result-card .value {
            font-size: 34px;
        }

        .section-title {
            font-size: 20px;
        }

        .power-calculator-page {
            padding: 20px 0;
        }
    }
</style>
@endpush

@section('content')
<div class="power-calculator-page">
    <div class="container">
        <div class="power-calc-container">
            <div class="power-calc-header">
                <div class="header-icon">🏠</div>
                <h1>Uganda Home Appliance Power Cost Calculator</h1>
                <p>Discover how much your home appliances cost you every month</p>
            </div>

            <div class="power-calc-content">
                <div class="alert-calc">
                    <strong>💡 UEDCL Tariff Information (Q1 2026):</strong> Domestic tariffs - Lifeline: UGX 250/kWh (first 15 units) | 16-80 units: UGX 756.2/kWh | 81-150 units: UGX 412/kWh | Above 150 units: UGX 756.2/kWh
                </div>

                <div class="calc-section">
                    <h2 class="section-title">
                        <span class="section-icon">⚡</span>
                        <span>Your Home Appliances</span>
                    </h2>
                    <div id="applianceList" class="appliance-list">
                        <div class="appliance-row">
                            <div class="form-group">
                                <label>Appliance Name</label>
                                <input type="text" class="appliance-name" placeholder="e.g., Fridge, TV, Iron Box" value="Refrigerator">
                            </div>
                            <div class="form-group">
                                <label>Wattage (W)</label>
                                <input type="number" class="appliance-wattage" placeholder="150" value="150" min="0">
                            </div>
                            <div class="form-group">
                                <label>Daily Hours</label>
                                <input type="number" class="appliance-hours" placeholder="8.0" value="8" min="0" step="0.5">
                            </div>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button class="calc-btn btn-danger-calc remove-appliance" onclick="removeAppliance(this)" style="display: none;">✕ Remove</button>
                            </div>
                        </div>
                    </div>
                    <button class="calc-btn btn-primary-calc" onclick="addAppliance()">+ Add Another Appliance</button>
                </div>

                <div class="calc-section">
                    <button class="calc-btn btn-calculate" onclick="calculateCost()">
                        <span style="position: relative; z-index: 1;">💰 Calculate Monthly Power Cost</span>
                    </button>
                </div>

                <div id="results" class="results">
                    <div class="results-header">
                        <h3>📊 Your Monthly Power Cost</h3>
                        <p>Based on UEDCL approved tariffs (Q1 2026)</p>
                    </div>
                    <div class="result-grid">
                        <div class="result-card">
                            <div class="icon">📈</div>
                            <div class="label">Total Monthly Consumption</div>
                            <div class="value" id="monthlyKwh">0</div>
                            <div class="unit">kWh</div>
                        </div>
                        <div class="result-card">
                            <div class="icon">💵</div>
                            <div class="label">Estimated Monthly Bill</div>
                            <div class="value" id="monthlyCost">0</div>
                            <div class="unit">Uganda Shillings</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    "use strict";
    
    // ERA Approved Tariffs (Oct-Dec 2026) - Domestic Code 10.1
    const LIFELINE_RATE = 250.0;      // UGX per kWh (first 15 units)
    const TIER2_RATE = 756.2;         // UGX per kWh (16-80 units)
    const TIER3_RATE = 412.0;         // UGX per kWh (81-150 units)
    const TIER4_RATE = 756.2;         // UGX per kWh (above 150 units)
    
    const LIFELINE_LIMIT = 15;        // First 15 kWh
    const TIER2_LIMIT = 80;           // Up to 80 kWh
    const TIER3_LIMIT = 150;          // Up to 150 kWh
    
    const DAYS_PER_MONTH = 30;

    function addAppliance() {
        const applianceList = document.getElementById('applianceList');
        const newRow = document.createElement('div');
        newRow.className = 'appliance-row';
        newRow.innerHTML = `
            <div class="form-group">
                <label>Appliance Name</label>
                <input type="text" class="appliance-name" placeholder="e.g., TV, Washing Machine, Fan">
            </div>
            <div class="form-group">
                <label>Wattage (W)</label>
                <input type="number" class="appliance-wattage" placeholder="100" min="0">
            </div>
            <div class="form-group">
                <label>Daily Hours</label>
                <input type="number" class="appliance-hours" placeholder="4.0" min="0" step="0.5">
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <button class="calc-btn btn-danger-calc remove-appliance" onclick="removeAppliance(this)">✕ Remove</button>
            </div>
        `;
        applianceList.appendChild(newRow);
        updateRemoveButtons();
    }

    function removeAppliance(button) {
        const row = button.closest('.appliance-row');
        row.style.opacity = '0';
        row.style.transform = 'scale(0.95)';
        setTimeout(() => {
            row.remove();
            updateRemoveButtons();
        }, 300);
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.appliance-row');
        rows.forEach((row) => {
            const removeBtn = row.querySelector('.remove-appliance');
            if (rows.length === 1) {
                removeBtn.style.display = 'none';
            } else {
                removeBtn.style.display = 'block';
            }
        });
    }

    function calculateCost() {
        const applianceRows = document.querySelectorAll('.appliance-row');
        let totalDailyWh = 0;
        let validAppliances = 0;

        applianceRows.forEach(row => {
            const name = row.querySelector('.appliance-name').value.trim();
            const wattage = parseFloat(row.querySelector('.appliance-wattage').value) || 0;
            const hours = parseFloat(row.querySelector('.appliance-hours').value) || 0;

            if (name && wattage > 0 && hours > 0) {
                const dailyWh = wattage * hours;
                totalDailyWh += dailyWh;
                validAppliances++;
            }
        });

        if (validAppliances === 0) {
            toastr.error('Please add at least one valid appliance with name, wattage, and daily usage hours', 'Error', {
                CloseButton: true,
                ProgressBar: true
            });
            return;
        }

        // Calculate monthly kWh
        const monthlyKwh = (totalDailyWh / 1000) * DAYS_PER_MONTH;
        
        // Calculate cost based on ERA tiered pricing
        let monthlyCost = 0;
        
        if (monthlyKwh <= LIFELINE_LIMIT) {
            // All consumption in lifeline tier
            monthlyCost = monthlyKwh * LIFELINE_RATE;
        } else if (monthlyKwh <= TIER2_LIMIT) {
            // Lifeline + Tier 2
            monthlyCost = (LIFELINE_LIMIT * LIFELINE_RATE) + 
                         ((monthlyKwh - LIFELINE_LIMIT) * TIER2_RATE);
        } else if (monthlyKwh <= TIER3_LIMIT) {
            // Lifeline + Tier 2 + Tier 3
            monthlyCost = (LIFELINE_LIMIT * LIFELINE_RATE) + 
                         ((TIER2_LIMIT - LIFELINE_LIMIT) * TIER2_RATE) +
                         ((monthlyKwh - TIER2_LIMIT) * TIER3_RATE);
        } else {
            // Lifeline + Tier 2 + Tier 3 + Tier 4
            monthlyCost = (LIFELINE_LIMIT * LIFELINE_RATE) + 
                         ((TIER2_LIMIT - LIFELINE_LIMIT) * TIER2_RATE) +
                         ((TIER3_LIMIT - TIER2_LIMIT) * TIER3_RATE) +
                         ((monthlyKwh - TIER3_LIMIT) * TIER4_RATE);
        }

        // Display results
        document.getElementById('monthlyKwh').textContent = monthlyKwh.toFixed(2);
        document.getElementById('monthlyCost').textContent = 'UGX ' + monthlyCost.toLocaleString('en-UG', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });

        const resultsDiv = document.getElementById('results');
        resultsDiv.classList.add('show');
        resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        toastr.success('Calculation completed successfully!', 'Success', {
            CloseButton: true,
            ProgressBar: true
        });
    }

    // Initialize
    updateRemoveButtons();
</script>
@endpush