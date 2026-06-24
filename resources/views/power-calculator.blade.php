<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Power Cost Calculator | Yoola.ug - Electronics On A Budget</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        /* Site Header */
        .site-header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .site-header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #FF0000;
            font-size: 28px;
            font-weight: 700;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
            font-size: 15px;
        }

        .nav-links a:hover {
            color: #FF0000;
        }

        .nav-links .btn-shop {
            background: #FF0000;
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .nav-links .btn-shop:hover {
            background: #CC0000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
        }

        /* Main Content */
        .main-content {
            padding: 40px 20px;
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
            background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
            color: white;
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
        }

        .power-calc-header p {
            font-size: 16px;
            font-weight: 300;
            opacity: 0.95;
            position: relative;
            z-index: 1;
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
            color: #FF0000;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 3px solid #FF0000;
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
            border-color: #FF0000;
            box-shadow: 0 6px 20px rgba(255, 0, 0, 0.15);
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
            border-color: #FF0000;
            box-shadow: 0 0 0 4px rgba(255, 0, 0, 0.12);
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

        .btn-primary {
            background: #FF0000;
            color: white;
            box-shadow: 0 4px 15px rgba(255, 0, 0, 0.35);
        }

        .btn-primary:hover {
            background: #CC0000;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 0, 0, 0.45);
        }

        .btn-calculate {
            background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
            color: white;
            width: 100%;
            padding: 20px;
            font-size: 19px;
            box-shadow: 0 6px 25px rgba(255, 0, 0, 0.4);
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
            box-shadow: 0 8px 30px rgba(255, 0, 0, 0.5);
        }

        .btn-danger {
            background: #666;
            color: white;
            padding: 11px 16px;
            font-size: 13px;
        }

        .btn-danger:hover {
            background: #FF0000;
            transform: scale(1.05);
        }

        .results {
            display: none;
            background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
            border-radius: 16px;
            padding: 35px;
            margin-top: 40px;
            border: 3px solid #FF0000;
            box-shadow: 0 8px 25px rgba(255, 0, 0, 0.15);
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
            color: #FF0000;
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
            border-left: 6px solid #FF0000;
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
            background: radial-gradient(circle, rgba(255,0,0,0.05) 0%, transparent 70%);
        }

        .result-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(255, 0, 0, 0.25);
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
            color: #FF0000;
            line-height: 1.1;
            margin-bottom: 8px;
        }

        .result-card .unit {
            font-size: 17px;
            color: #999;
            font-weight: 500;
        }

        .alert {
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

        .alert strong {
            font-weight: 700;
            color: #FF0000;
        }

        /* Footer */
        .site-footer {
            background: #2c2c2c;
            color: white;
            padding: 40px 20px;
            margin-top: 60px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .footer-content p {
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .footer-links a:hover {
            opacity: 1;
            color: #FF0000;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #FF0000;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
                position: absolute;
                top: 75px;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                padding: 20px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            }

            .nav-links.active {
                display: flex;
            }

            .mobile-menu-toggle {
                display: block;
            }

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

            .logo {
                font-size: 22px;
            }

            .logo-icon {
                width: 38px;
                height: 38px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Site Header -->
    <header class="site-header">
        <div class="site-header-content">
            <a href="{{ url('/') }}" class="logo">
                <div class="logo-icon">⚡</div>
                <span>Yoola.ug</span>
            </a>
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
            <nav class="nav-links" id="navLinks">
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/products') }}">Products</a>
                <a href="{{ url('/power-calculator') }}" style="color: #FF0000;">Power Calculator</a>
                <a href="{{ url('/about') }}">About</a>
                <a href="{{ url('/contact') }}">Contact</a>
                <a href="{{ url('/shop') }}" class="btn-shop">Shop Now</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-content">
        <div class="power-calc-container">
            <div class="power-calc-header">
                <div class="header-icon">🏠</div>
                <h1>Uganda Home Appliance Power Cost Calculator</h1>
                <p>Discover how much your home appliances cost you every month</p>
            </div>

            <div class="power-calc-content">
                <div class="alert">
                    <strong>💡 Tariff Information:</strong> Calculations based on UMEME residential rates | Tier 1: 100 UGX/kWh (first 15 kWh) | Tier 2: 820 UGX/kWh (above 15 kWh)
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
                                <button class="calc-btn btn-danger remove-appliance" onclick="removeAppliance(this)" style="display: none;">✕ Remove</button>
                            </div>
                        </div>
                    </div>
                    <button class="calc-btn btn-primary" onclick="addAppliance()">+ Add Another Appliance</button>
                </div>

                <div class="calc-section">
                    <button class="calc-btn btn-calculate" onclick="calculateCost()">
                        <span style="position: relative; z-index: 1;">💰 Calculate Monthly Power Cost</span>
                    </button>
                </div>

                <div id="results" class="results">
                    <div class="results-header">
                        <h3>📊 Your Monthly Power Cost</h3>
                        <p>Based on your home appliances usage</p>
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

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-content">
            <p><strong>Yoola.ug</strong> - Electronics On A Budget | Free Kampala Delivery</p>
            <p>📍 Kampala, Uganda | 📞 Contact Us | 📧 info@yoola.ug</p>
            <div class="footer-links">
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/products') }}">Products</a>
                <a href="{{ url('/about') }}">About Us</a>
                <a href="{{ url('/contact') }}">Contact</a>
                <a href="{{ url('/terms') }}">Terms & Conditions</a>
            </div>
            <p style="margin-top: 20px; font-size: 13px; opacity: 0.7;">© {{ date('Y') }} Yoola.ug. All rights reserved.</p>
        </div>
    </footer>

    <script>
        "use strict";
        
        const TIER1_RATE = 100;
        const TIER2_RATE = 820;
        const TIER1_LIMIT = 15;
        const DAYS_PER_MONTH = 30;

        function toggleMobileMenu() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('active');
        }

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
                    <button class="calc-btn btn-danger remove-appliance" onclick="removeAppliance(this)">✕ Remove</button>
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
                alert('Please add at least one valid appliance with name, wattage, and daily usage hours');
                return;
            }

            const monthlyKwh = (totalDailyWh / 1000) * DAYS_PER_MONTH;
            
            let monthlyCost = 0;
            if (monthlyKwh <= TIER1_LIMIT) {
                monthlyCost = monthlyKwh * TIER1_RATE;
            } else {
                monthlyCost = (TIER1_LIMIT * TIER1_RATE) + ((monthlyKwh - TIER1_LIMIT) * TIER2_RATE);
            }

            document.getElementById('monthlyKwh').textContent = monthlyKwh.toFixed(2);
            document.getElementById('monthlyCost').textContent = 'UGX ' + monthlyCost.toLocaleString('en-UG', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });

            const resultsDiv = document.getElementById('results');
            resultsDiv.classList.add('show');
            resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        updateRemoveButtons();
    </script>
</body>
</html>