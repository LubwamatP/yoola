<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uganda Power Consumption and Backup Calculator | Yoola.ug</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        .device-list {
            margin-bottom: 20px;
        }

        .device-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 12px;
            margin-bottom: 12px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 5px;
        }

        .form-group input {
            padding: 10px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-success {
            background: #10b981;
            color: white;
            width: 100%;
            padding: 14px;
            font-size: 16px;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .btn-danger {
            background: #ef4444;
            color: white;
            padding: 8px 12px;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .results {
            display: none;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
        }

        .results.show {
            display: block;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .result-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .result-card .label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .result-card .value {
            font-size: 24px;
            font-weight: 700;
            color: #1e3a8a;
        }

        .result-card .unit {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }

        .alert {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #92400e;
        }

        @media (max-width: 768px) {
            .device-row {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 22px;
            }

            .content {
                padding: 20px;
            }

            .result-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔌 Uganda Power Consumption & Backup Calculator</h1>
            <p>Calculate your monthly electricity bill and battery backup requirements</p>
        </div>

        <div class="content">
            <div class="alert">
                <strong>Note:</strong> Calculations based on UMEME residential tariff structure (Tier 1: 100 UGX/kWh for first 15 kWh, Tier 2: 820 UGX/kWh thereafter)
            </div>

            <div class="section">
                <h2 class="section-title">Add Your Devices</h2>
                <div id="deviceList" class="device-list">
                    <div class="device-row">
                        <div class="form-group">
                            <label>Device Name</label>
                            <input type="text" class="device-name" placeholder="e.g., Laptop" value="Laptop">
                        </div>
                        <div class="form-group">
                            <label>Wattage (W)</label>
                            <input type="number" class="device-wattage" placeholder="60" value="60" min="0">
                        </div>
                        <div class="form-group">
                            <label>Daily Hours</label>
                            <input type="number" class="device-hours" placeholder="5" value="5" min="0" step="0.5">
                        </div>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button class="btn btn-danger remove-device" onclick="removeDevice(this)" style="display: none;">Remove</button>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" onclick="addDevice()">+ Add Another Device</button>
            </div>

            <div class="section">
                <button class="btn btn-success" onclick="calculate()">Calculate Power & Backup Requirements</button>
            </div>

            <div id="results" class="results">
                <h2 class="section-title">📊 Your Results</h2>
                <div class="result-grid">
                    <div class="result-card">
                        <div class="label">Monthly Consumption</div>
                        <div class="value" id="monthlyKwh">0</div>
                        <div class="unit">kWh</div>
                    </div>
                    <div class="result-card">
                        <div class="label">Estimated Monthly Bill</div>
                        <div class="value" id="monthlyCost">0</div>
                        <div class="unit">UGX</div>
                    </div>
                    <div class="result-card">
                        <div class="label">4-Hour Backup Energy</div>
                        <div class="value" id="backupWh">0</div>
                        <div class="unit">Wh</div>
                    </div>
                    <div class="result-card">
                        <div class="label">Recommended Battery</div>
                        <div class="value" id="batteryAh">0</div>
                        <div class="unit">Ah (12V)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Constants - Uganda Power Tariff Structure
        const TIER1_RATE = 100;  // UGX per kWh (first 15 kWh)
        const TIER2_RATE = 820;  // UGX per kWh (above 15 kWh)
        const TIER1_LIMIT = 15;  // kWh
        const SYSTEM_VOLTAGE = 12;  // Volts
        const DEPTH_OF_DISCHARGE = 0.8;  // 80% DoD
        const BACKUP_HOURS = 4;  // Standard backup duration
        const DAYS_PER_MONTH = 30;

        function addDevice() {
            const deviceList = document.getElementById('deviceList');
            const newRow = document.createElement('div');
            newRow.className = 'device-row';
            newRow.innerHTML = `
                <div class="form-group">
                    <label>Device Name</label>
                    <input type="text" class="device-name" placeholder="e.g., TV">
                </div>
                <div class="form-group">
                    <label>Wattage (W)</label>
                    <input type="number" class="device-wattage" placeholder="100" min="0">
                </div>
                <div class="form-group">
                    <label>Daily Hours</label>
                    <input type="number" class="device-hours" placeholder="3" min="0" step="0.5">
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button class="btn btn-danger remove-device" onclick="removeDevice(this)">Remove</button>
                </div>
            `;
            deviceList.appendChild(newRow);
            updateRemoveButtons();
        }

        function removeDevice(button) {
            const row = button.closest('.device-row');
            row.remove();
            updateRemoveButtons();
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.device-row');
            rows.forEach((row, index) => {
                const removeBtn = row.querySelector('.remove-device');
                if (rows.length === 1) {
                    removeBtn.style.display = 'none';
                } else {
                    removeBtn.style.display = 'block';
                }
            });
        }

        function calculate() {
            const deviceRows = document.querySelectorAll('.device-row');
            let totalDailyWh = 0;
            let totalWattage = 0;
            let validDevices = 0;

            // Aggregate all devices
            deviceRows.forEach(row => {
                const name = row.querySelector('.device-name').value.trim();
                const wattage = parseFloat(row.querySelector('.device-wattage').value) || 0;
                const hours = parseFloat(row.querySelector('.device-hours').value) || 0;

                if (name && wattage > 0 && hours > 0) {
                    const dailyWh = wattage * hours;
                    totalDailyWh += dailyWh;
                    totalWattage += wattage;
                    validDevices++;
                }
            });

            if (validDevices === 0) {
                alert('Please add at least one valid device with name, wattage, and usage hours.');
                return;
            }

            // STEP 1: Calculate Monthly Consumption and Cost
            const monthlyKwh = (totalDailyWh / 1000) * DAYS_PER_MONTH;
            
            let monthlyCost = 0;
            if (monthlyKwh <= TIER1_LIMIT) {
                monthlyCost = monthlyKwh * TIER1_RATE;
            } else {
                monthlyCost = (TIER1_LIMIT * TIER1_RATE) + ((monthlyKwh - TIER1_LIMIT) * TIER2_RATE);
            }

            // STEP 2: Calculate Backup Energy Requirement
            const backupWh = totalWattage * BACKUP_HOURS;

            // STEP 3: Calculate Recommended Battery Capacity
            const batteryAh = (backupWh / DEPTH_OF_DISCHARGE) / SYSTEM_VOLTAGE;

            // Display Results
            document.getElementById('monthlyKwh').textContent = monthlyKwh.toFixed(2);
            document.getElementById('monthlyCost').textContent = monthlyCost.toLocaleString('en-UG', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            document.getElementById('backupWh').textContent = backupWh.toFixed(2);
            document.getElementById('batteryAh').textContent = batteryAh.toFixed(2);

            // Show results with animation
            const resultsDiv = document.getElementById('results');
            resultsDiv.classList.add('show');
            resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Initialize
        updateRemoveButtons();
    </script>
</body>
</html>