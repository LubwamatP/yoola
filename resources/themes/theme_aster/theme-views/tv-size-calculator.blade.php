<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Optimal TV Size | Yoola.ug</title>
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
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
            color: white;
            padding: 45px 35px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
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
            font-size: 56px;
            margin-bottom: 15px;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        .header h1 {
            font-size: 38px;
            font-weight: 700;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 17px;
            font-weight: 300;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        .content {
            padding: 45px 35px;
        }

        .section {
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

        .input-container {
            background: linear-gradient(135deg, #f9f9f9 0%, #f5f5f5 100%);
            border-radius: 16px;
            padding: 35px;
            margin-bottom: 30px;
            border: 2px solid #e5e5e5;
        }

        .input-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group label .required {
            color: #FF0000;
            font-weight: 700;
        }

        .form-group label .optional {
            color: #999;
            font-weight: 400;
            text-transform: none;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 16px 55px 16px 18px;
            border: 2px solid #e5e5e5;
            border-radius: 12px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
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
            font-weight: 400;
        }

        .input-unit {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            font-weight: 600;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn {
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
            width: 400px;
            height: 400px;
        }

        .btn-calculate:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(255, 0, 0, 0.5);
        }

        .btn-calculate:active {
            transform: translateY(-1px);
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
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

        .range-highlight {
            background: white;
            padding: 30px;
            border-radius: 14px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            border: 2px solid #FF0000;
            margin-bottom: 30px;
        }

        .range-highlight .label {
            font-size: 13px;
            color: #666;
            margin-bottom: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .range-highlight .value {
            font-size: 36px;
            font-weight: 700;
            color: #FF0000;
            margin-bottom: 8px;
        }

        .range-highlight .text {
            font-size: 14px;
            color: #999;
        }

        .interpretation {
            background: white;
            padding: 28px;
            border-radius: 14px;
            border-left: 6px solid #FF0000;
        }

        .interpretation-title {
            font-size: 18px;
            font-weight: 700;
            color: #FF0000;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .interpretation p {
            font-size: 14px;
            color: #555;
            line-height: 1.7;
            margin-bottom: 12px;
        }

        .interpretation ul {
            margin-left: 20px;
            color: #555;
            font-size: 14px;
            line-height: 1.8;
        }

        .interpretation ul li {
            margin-bottom: 8px;
        }

        .interpretation ul li strong {
            color: #FF0000;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 28px;
            }

            .header p {
                font-size: 15px;
            }

            .content {
                padding: 30px 20px;
            }

            .input-grid {
                grid-template-columns: 1fr;
            }

            .result-grid {
                grid-template-columns: 1fr;
            }

            .result-card .value {
                font-size: 34px;
            }

            .range-highlight .value {
                font-size: 28px;
            }

            .section-title {
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .header {
                padding: 35px 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .btn-calculate {
                padding: 18px;
                font-size: 17px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">📺</div>
            <h1>Find Your Optimal TV Size</h1>
            <p>Calculate the perfect TV screen size based on your viewing distance using industry standards</p>
        </div>

        <div class="content">
            <div class="alert">
                <strong>💡 How It Works:</strong> This calculator uses SMPTE and THX industry standards. SMPTE (30° viewing angle) provides comfortable viewing, while THX (40° viewing angle) offers cinematic immersion. Enter your viewing distance to find your ideal TV size range.
            </div>

            <div class="section">
                <h2 class="section-title">
                    <span class="section-icon">📏</span>
                    <span>Enter Your Room Details</span>
                </h2>
                
                <div class="input-container">
                    <div class="input-grid">
                        <div class="form-group">
                            <label>Viewing Distance <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <input type="number" id="viewingDistance" placeholder="10" min="1" step="0.5">
                                <span class="input-unit">ft</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Room Width <span class="optional">(Optional)</span></label>
                            <div class="input-wrapper">
                                <input type="number" id="roomWidth" placeholder="12" min="1" step="0.5">
                                <span class="input-unit">ft</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Room Length <span class="optional">(Optional)</span></label>
                            <div class="input-wrapper">
                                <input type="number" id="roomLength" placeholder="15" min="1" step="0.5">
                                <span class="input-unit">ft</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <button class="btn btn-calculate" onclick="calculateTVSize()">
                    <span style="position: relative; z-index: 1;">🎯 Calculate Ideal Size</span>
                </button>
            </div>

            <div id="results" class="results">
                <div class="results-header">
                    <h3>📊 Your Recommended TV Size</h3>
                    <p>Based on industry viewing standards</p>
                </div>

                <div class="result-grid">
                    <div class="result-card">
                        <div class="icon">📍</div>
                        <div class="label">Viewing Distance Entered</div>
                        <div class="value" id="distanceDisplay">0</div>
                        <div class="unit">feet</div>
                    </div>

                    <div class="result-card">
                        <div class="icon">📐</div>
                        <div class="label">Minimum Size (SMPTE 30°)</div>
                        <div class="value" id="minSize">0</div>
                        <div class="unit">inches</div>
                    </div>

                    <div class="result-card">
                        <div class="icon">🎬</div>
                        <div class="label">Maximum Size (THX 40°)</div>
                        <div class="value" id="maxSize">0</div>
                        <div class="unit">inches</div>
                    </div>
                </div>

                <div class="range-highlight">
                    <div class="label">Recommended TV Size Range</div>
                    <div class="value" id="sizeRange">0" - 0"</div>
                    <div class="text">Diagonal Screen Measurement</div>
                </div>

                <div class="interpretation">
                    <div class="interpretation-title">
                        <span>📖</span>
                        <span>Understanding Your Results</span>
                    </div>
                    <p>The calculated range provides flexibility based on your preferences:</p>
                    <ul>
                        <li><strong>Minimum Size (<span id="minSizeText">0</span>")</strong>: Based on SMPTE standards (30° viewing angle). This offers comfortable everyday viewing, ideal for casual TV watching and general entertainment.</li>
                        <li><strong>Maximum Size (<span id="maxSizeText">0</span>")</strong>: Based on THX standards (40° viewing angle). This provides a more immersive cinematic experience, perfect for movies, sports, and gaming.</li>
                    </ul>
                    <p style="margin-top: 15px;">
                        <strong>Recommendation:</strong> Choose a size within this range based on your content preferences and room aesthetics. Larger screens offer more immersion, while smaller screens in the range maintain viewing comfort.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        "use strict";
        
        // Industry Standard Constants
        const SMPTE_FACTOR = 1.6;  // 30-degree viewing angle
        const THX_FACTOR = 1.2;     // 40-degree viewing angle
        const FEET_TO_INCHES = 12;

        function calculateTVSize() {
            // Get input values
            const viewingDistanceFt = parseFloat(document.getElementById('viewingDistance').value);
            
            // Validate required input
            if (!viewingDistanceFt || viewingDistanceFt <= 0) {
                alert('Please enter a valid viewing distance in feet');
                return;
            }

            // Step 1: Convert viewing distance from feet to inches
            const viewingDistanceInches = viewingDistanceFt * FEET_TO_INCHES;

            // Step 2: Calculate minimum and maximum recommended sizes
            const minTVSize = Math.round(viewingDistanceInches / SMPTE_FACTOR);
            const maxTVSize = Math.round(viewingDistanceInches / THX_FACTOR);

            // Display results
            document.getElementById('distanceDisplay').textContent = viewingDistanceFt.toFixed(1);
            document.getElementById('minSize').textContent = minTVSize;
            document.getElementById('maxSize').textContent = maxTVSize;
            document.getElementById('sizeRange').textContent = minTVSize + '" - ' + maxTVSize + '"';
            document.getElementById('minSizeText').textContent = minTVSize;
            document.getElementById('maxSizeText').textContent = maxTVSize;

            // Show results section with animation
            const resultsDiv = document.getElementById('results');
            resultsDiv.classList.add('show');
            
            // Smooth scroll to results
            resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Allow Enter key to trigger calculation
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        calculateTVSize();
                    }
                });
            });
        });
    </script>
</body>
</html>