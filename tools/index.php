<?php
/**
 * YOOLA ELECTRICITY BILL CALCULATOR
 * Helps Ugandans estimate their monthly UMEME bill and find energy-efficient alternatives
 * 
 * This is linkbait - shareable, useful, and drives traffic
 */

// Common appliances with wattage
$appliances = [
    'tv_32' => ['name' => '32" TV (LED)', 'watts' => 50, 'category' => 'Entertainment'],
    'tv_43' => ['name' => '43" TV (LED)', 'watts' => 80, 'category' => 'Entertainment'],
    'tv_55' => ['name' => '55" TV (LED)', 'watts' => 120, 'category' => 'Entertainment'],
    'tv_65' => ['name' => '65" TV (LED)', 'watts' => 150, 'category' => 'Entertainment'],
    'fridge_small' => ['name' => 'Small Fridge (150L)', 'watts' => 100, 'category' => 'Kitchen'],
    'fridge_medium' => ['name' => 'Medium Fridge (250L)', 'watts' => 150, 'category' => 'Kitchen'],
    'fridge_large' => ['name' => 'Large Fridge (400L+)', 'watts' => 200, 'category' => 'Kitchen'],
    'freezer_chest' => ['name' => 'Chest Freezer', 'watts' => 200, 'category' => 'Kitchen'],
    'ac_12k' => ['name' => 'AC 12,000 BTU', 'watts' => 1200, 'category' => 'Cooling'],
    'ac_18k' => ['name' => 'AC 18,000 BTU', 'watts' => 1800, 'category' => 'Cooling'],
    'ac_24k' => ['name' => 'AC 24,000 BTU', 'watts' => 2500, 'category' => 'Cooling'],
    'ac_inverter_12k' => ['name' => 'Inverter AC 12,000 BTU', 'watts' => 800, 'category' => 'Cooling'],
    'ac_inverter_18k' => ['name' => 'Inverter AC 18,000 BTU', 'watts' => 1200, 'category' => 'Cooling'],
    'fan' => ['name' => 'Standing Fan', 'watts' => 60, 'category' => 'Cooling'],
    'microwave' => ['name' => 'Microwave', 'watts' => 1000, 'category' => 'Kitchen'],
    'kettle' => ['name' => 'Electric Kettle', 'watts' => 1500, 'category' => 'Kitchen'],
    'iron' => ['name' => 'Electric Iron', 'watts' => 1200, 'category' => 'Home'],
    'washing_machine' => ['name' => 'Washing Machine', 'watts' => 500, 'category' => 'Laundry'],
    'water_heater' => ['name' => 'Water Heater', 'watts' => 3000, 'category' => 'Home'],
    'bulb_led' => ['name' => 'LED Bulb', 'watts' => 10, 'category' => 'Lighting'],
    'bulb_incandescent' => ['name' => 'Incandescent Bulb', 'watts' => 60, 'category' => 'Lighting'],
    'computer' => ['name' => 'Desktop Computer', 'watts' => 200, 'category' => 'Electronics'],
    'laptop' => ['name' => 'Laptop', 'watts' => 50, 'category' => 'Electronics'],
    'router' => ['name' => 'WiFi Router', 'watts' => 10, 'category' => 'Electronics'],
];

// UMEME tariff rates (UGX per kWh) - 2026 rates
$tariff = [
    'domestic' => [
        'lifeline' => 250, // First 15 units
        'tier1' => 755,    // 16-80 units
        'tier2' => 805,    // 81+ units
    ],
    'service_charge' => 3360, // Monthly service charge
];

function calculateBill($appliances_used, $tariff) {
    $total_kwh = 0;
    $breakdown = [];
    
    foreach ($appliances_used as $appliance => $hours_per_day) {
        global $appliances;
        if (isset($appliances[$appliance])) {
            $watts = $appliances[$appliance]['watts'];
            $daily_kwh = ($watts * $hours_per_day) / 1000;
            $monthly_kwh = $daily_kwh * 30;
            $total_kwh += $monthly_kwh;
            $breakdown[$appliance] = [
                'name' => $appliances[$appliance]['name'],
                'hours' => $hours_per_day,
                'monthly_kwh' => round($monthly_kwh, 2),
                'watts' => $watts
            ];
        }
    }
    
    // Calculate bill based on tariff tiers
    $bill = $tariff['service_charge'];
    $remaining = $total_kwh;
    
    if ($remaining > 0) {
        $lifeline = min($remaining, 15);
        $bill += $lifeline * $tariff['domestic']['lifeline'];
        $remaining -= $lifeline;
    }
    
    if ($remaining > 0) {
        $tier1 = min($remaining, 65);
        $bill += $tier1 * $tariff['domestic']['tier1'];
        $remaining -= $tier1;
    }
    
    if ($remaining > 0) {
        $bill += $remaining * $tariff['domestic']['tier2'];
    }
    
    return [
        'total_kwh' => round($total_kwh, 2),
        'bill' => round($bill),
        'breakdown' => $breakdown
    ];
}

// Energy saving tips based on usage
function getSavingTips($breakdown) {
    $tips = [];
    
    foreach ($breakdown as $appliance => $data) {
        if (strpos($appliance, 'ac_') === 0 && strpos($appliance, 'inverter') === false) {
            $tips[] = "💡 Switch to an Inverter AC - saves up to 40% on cooling costs!";
        }
        if ($appliance === 'bulb_incandescent') {
            $tips[] = "💡 Replace incandescent bulbs with LED - saves 80% on lighting!";
        }
        if ($appliance === 'water_heater' && $data['hours'] > 2) {
            $tips[] = "💡 Use a timer for your water heater - only heat when needed!";
        }
        if (strpos($appliance, 'fridge') !== false) {
            $tips[] = "💡 Ensure your fridge has proper ventilation and isn't overpacked!";
        }
    }
    
    return array_unique($tips);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Free Electricity Bill Calculator Uganda | UMEME Bill Estimator | Yoola</title>
    <meta name="description" content="Calculate your monthly UMEME electricity bill for free. Find out which appliances cost you the most and get tips to save money on your power bill in Uganda.">
    <meta name="keywords" content="UMEME bill calculator, electricity calculator Uganda, power bill estimator, save electricity Uganda, Yoola">
    <link rel="canonical" href="https://yoola.ug/electricity-calculator">
    
    <!-- Open Graph for social sharing -->
    <meta property="og:title" content="Free UMEME Bill Calculator - Find Out What's Costing You">
    <meta property="og:description" content="Discover which appliances are eating your electricity and get free recommendations to cut your bill by up to 40%">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://yoola.ug/electricity-calculator">
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        header { background: #DC2626; color: white; padding: 30px 20px; text-align: center; }
        header h1 { font-size: 28px; margin-bottom: 10px; }
        header p { opacity: 0.9; }
        .calculator { background: white; border-radius: 12px; padding: 30px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section-title { font-size: 18px; font-weight: 600; margin: 20px 0 15px; color: #DC2626; }
        .appliance-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; }
        .appliance-item { display: flex; align-items: center; gap: 10px; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; }
        .appliance-item:hover { border-color: #DC2626; }
        .appliance-item input { width: 60px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; text-align: center; }
        .appliance-item label { flex: 1; font-size: 14px; }
        .appliance-item .watts { font-size: 12px; color: #666; }
        .btn { background: #DC2626; color: white; border: none; padding: 15px 40px; font-size: 18px; border-radius: 8px; cursor: pointer; width: 100%; margin-top: 20px; }
        .btn:hover { background: #b91c1c; }
        .results { background: #f0fdf4; border: 2px solid #22c55e; border-radius: 12px; padding: 25px; margin-top: 20px; display: none; }
        .results.show { display: block; }
        .result-big { font-size: 42px; font-weight: 700; color: #DC2626; }
        .result-label { font-size: 14px; color: #666; }
        .breakdown { margin-top: 20px; }
        .breakdown-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e0e0e0; }
        .tips { background: #fef3c7; border-radius: 8px; padding: 20px; margin-top: 20px; }
        .tips h3 { color: #92400e; margin-bottom: 10px; }
        .tips ul { margin-left: 20px; }
        .tips li { margin: 8px 0; }
        .cta-box { background: #DC2626; color: white; border-radius: 12px; padding: 25px; margin-top: 30px; text-align: center; }
        .cta-box h3 { margin-bottom: 10px; }
        .cta-box a { color: white; font-weight: 600; }
        .share-buttons { margin-top: 20px; text-align: center; }
        .share-buttons a { display: inline-block; padding: 10px 20px; margin: 5px; border-radius: 6px; text-decoration: none; color: white; }
        .share-fb { background: #1877f2; }
        .share-wa { background: #25d366; }
        .share-tw { background: #1da1f2; }
        footer { text-align: center; padding: 30px; color: #666; }
        footer a { color: #DC2626; }
    </style>
</head>
<body>
    <header>
        <h1>⚡ Free Electricity Bill Calculator</h1>
        <p>Find out what's really costing you on your UMEME bill</p>
    </header>
    
    <div class="container">
        <div class="calculator">
            <form id="calcForm">
                <p style="margin-bottom: 20px; color: #666;">Enter how many hours per day you use each appliance:</p>
                
                <div class="section-title">🎬 Entertainment</div>
                <div class="appliance-grid">
                    <?php foreach ($appliances as $key => $app): ?>
                        <?php if ($app['category'] === 'Entertainment'): ?>
                        <div class="appliance-item">
                            <input type="number" name="<?= $key ?>" min="0" max="24" value="0" step="0.5">
                            <label><?= $app['name'] ?> <span class="watts">(<?= $app['watts'] ?>W)</span></label>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <div class="section-title">❄️ Cooling</div>
                <div class="appliance-grid">
                    <?php foreach ($appliances as $key => $app): ?>
                        <?php if ($app['category'] === 'Cooling'): ?>
                        <div class="appliance-item">
                            <input type="number" name="<?= $key ?>" min="0" max="24" value="0" step="0.5">
                            <label><?= $app['name'] ?> <span class="watts">(<?= $app['watts'] ?>W)</span></label>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <div class="section-title">🍳 Kitchen</div>
                <div class="appliance-grid">
                    <?php foreach ($appliances as $key => $app): ?>
                        <?php if ($app['category'] === 'Kitchen'): ?>
                        <div class="appliance-item">
                            <input type="number" name="<?= $key ?>" min="0" max="24" value="0" step="0.5">
                            <label><?= $app['name'] ?> <span class="watts">(<?= $app['watts'] ?>W)</span></label>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <div class="section-title">🏠 Home & Laundry</div>
                <div class="appliance-grid">
                    <?php foreach ($appliances as $key => $app): ?>
                        <?php if ($app['category'] === 'Home' || $app['category'] === 'Laundry'): ?>
                        <div class="appliance-item">
                            <input type="number" name="<?= $key ?>" min="0" max="24" value="0" step="0.5">
                            <label><?= $app['name'] ?> <span class="watts">(<?= $app['watts'] ?>W)</span></label>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <div class="section-title">💡 Lighting & Electronics</div>
                <div class="appliance-grid">
                    <?php foreach ($appliances as $key => $app): ?>
                        <?php if ($app['category'] === 'Lighting' || $app['category'] === 'Electronics'): ?>
                        <div class="appliance-item">
                            <input type="number" name="<?= $key ?>" min="0" max="24" value="0" step="0.5">
                            <label><?= $app['name'] ?> <span class="watts">(<?= $app['watts'] ?>W)</span></label>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <button type="submit" class="btn">⚡ Calculate My Bill</button>
            </form>
            
            <div class="results" id="results">
                <div style="text-align: center;">
                    <div class="result-label">Your Estimated Monthly Bill</div>
                    <div class="result-big">UGX <span id="billAmount">0</span></div>
                    <div style="margin-top: 10px; color: #666;">
                        <span id="kwhAmount">0</span> kWh per month
                    </div>
                </div>
                
                <div class="breakdown" id="breakdown">
                    <h3 style="margin-bottom: 15px;">📊 What's Costing You Most:</h3>
                    <!-- Filled by JS -->
                </div>
                
                <div class="tips" id="tips">
                    <h3>💡 Money-Saving Tips:</h3>
                    <ul id="tipsList"></ul>
                </div>
                
                <div class="cta-box">
                    <h3>🎯 Want to Cut Your Bill?</h3>
                    <p>Our team can recommend energy-efficient appliances that save you money!</p>
                    <p style="margin-top: 15px;">
                        <a href="https://wa.me/256780221421?text=Hi! I used your electricity calculator and want recommendations for energy-efficient appliances">
                            📱 WhatsApp Us for Free Advice
                        </a>
                    </p>
                </div>
                
                <div class="share-buttons">
                    <p style="margin-bottom: 10px; color: #666;">Share this tool with friends:</p>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=https://yoola.ug/electricity-calculator" class="share-fb" target="_blank">Facebook</a>
                    <a href="https://wa.me/?text=Check out this free electricity bill calculator - find out what's really costing you! https://yoola.ug/electricity-calculator" class="share-wa" target="_blank">WhatsApp</a>
                    <a href="https://twitter.com/intent/tweet?url=https://yoola.ug/electricity-calculator&text=Free UMEME bill calculator - find out what's eating your electricity!" class="share-tw" target="_blank">Twitter</a>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
        <p>Built with ❤️ by <a href="https://yoola.ug">Yoola.ug</a> - Uganda's Trusted Electronics Store</p>
        <p style="margin-top: 10px; font-size: 12px;">Tariff rates based on UMEME 2026 domestic rates. Actual bills may vary.</p>
    </footer>
    
    <script>
        document.getElementById('calcForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const appliances = <?= json_encode($appliances) ?>;
            const tariff = <?= json_encode($tariff) ?>;
            
            let totalKwh = 0;
            let breakdown = [];
            
            for (let [key, hours] of formData.entries()) {
                hours = parseFloat(hours) || 0;
                if (hours > 0 && appliances[key]) {
                    const watts = appliances[key].watts;
                    const dailyKwh = (watts * hours) / 1000;
                    const monthlyKwh = dailyKwh * 30;
                    const monthlyCost = monthlyKwh * 755; // Average rate
                    totalKwh += monthlyKwh;
                    breakdown.push({
                        name: appliances[key].name,
                        kwh: monthlyKwh.toFixed(1),
                        cost: Math.round(monthlyCost),
                        key: key
                    });
                }
            }
            
            // Sort by cost
            breakdown.sort((a, b) => b.cost - a.cost);
            
            // Calculate tiered bill
            let bill = tariff.service_charge;
            let remaining = totalKwh;
            
            if (remaining > 0) {
                const lifeline = Math.min(remaining, 15);
                bill += lifeline * tariff.domestic.lifeline;
                remaining -= lifeline;
            }
            if (remaining > 0) {
                const tier1 = Math.min(remaining, 65);
                bill += tier1 * tariff.domestic.tier1;
                remaining -= tier1;
            }
            if (remaining > 0) {
                bill += remaining * tariff.domestic.tier2;
            }
            
            // Display results
            document.getElementById('billAmount').textContent = Math.round(bill).toLocaleString();
            document.getElementById('kwhAmount').textContent = totalKwh.toFixed(1);
            
            // Show breakdown
            const breakdownHtml = breakdown.slice(0, 5).map(item => 
                `<div class="breakdown-item">
                    <span>${item.name}</span>
                    <span><strong>UGX ${item.cost.toLocaleString()}</strong> (${item.kwh} kWh)</span>
                </div>`
            ).join('');
            document.getElementById('breakdown').innerHTML = '<h3 style="margin-bottom: 15px;">📊 What\'s Costing You Most:</h3>' + breakdownHtml;
            
            // Generate tips
            let tips = [];
            breakdown.forEach(item => {
                if (item.key.includes('ac_') && !item.key.includes('inverter')) {
                    tips.push('Switch to an Inverter AC - saves up to 40% on cooling!');
                }
                if (item.key === 'bulb_incandescent') {
                    tips.push('Replace incandescent bulbs with LED - saves 80% on lighting!');
                }
                if (item.key === 'water_heater') {
                    tips.push('Use a timer for your water heater - heat only when needed!');
                }
            });
            tips.push('Shop energy-efficient appliances at Yoola.ug for long-term savings!');
            
            document.getElementById('tipsList').innerHTML = [...new Set(tips)].map(t => `<li>${t}</li>`).join('');
            
            // Show results
            document.getElementById('results').classList.add('show');
            document.getElementById('results').scrollIntoView({ behavior: 'smooth' });
        });
    </script>
</body>
</html>

