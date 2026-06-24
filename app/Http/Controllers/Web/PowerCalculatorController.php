<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PowerCalculatorController extends Controller
{
    /**
     * UEDCL Q4 2025 Tariff Constants (Domestic Code 10.1)
     * Source: UEDCL-4th-QUARTER-TARIFFS-website.pdf
     */
    private const RATE_LIFELINE = 250.0;  // 0-15 kWh (only if eligible)
    private const RATE_TIER_1 = 756.2;    // 16-80 kWh
    private const RATE_TIER_2 = 412.0;    // 81-150 kWh
    private const RATE_TIER_3 = 756.2;    // Above 150 kWh

    private const LIMIT_LIFELINE = 15;    // Lifeline block size
    private const LIMIT_TIER_1 = 80;      // Tier 1 upper limit
    private const LIMIT_TIER_2 = 150;     // Tier 2 upper limit
    private const LIMIT_ELIGIBILITY = 100;// Lifeline only if total <= 100

    private const SYSTEM_VOLTAGE = 12;    // Volts for battery calculations
    private const DEPTH_OF_DISCHARGE = 0.8; // 80% DoD for lead-acid
    private const DAYS_PER_MONTH = 30;

    /**
     * Common Ugandan household appliances with typical wattages
     */
    private const APPLIANCE_PRESETS = [
        [
            'category' => 'Kitchen Appliances',
            'items' => [
                ['name' => 'Refrigerator (Small)', 'wattage' => 100, 'typical_hours' => 24],
                ['name' => 'Refrigerator (Large)', 'wattage' => 200, 'typical_hours' => 24],
                ['name' => 'Freezer', 'wattage' => 150, 'typical_hours' => 24],
                ['name' => 'Microwave', 'wattage' => 1000, 'typical_hours' => 0.5],
                ['name' => 'Electric Kettle', 'wattage' => 1500, 'typical_hours' => 0.3],
                ['name' => 'Rice Cooker', 'wattage' => 500, 'typical_hours' => 1],
                ['name' => 'Blender', 'wattage' => 400, 'typical_hours' => 0.2],
                ['name' => 'Electric Cooker', 'wattage' => 2000, 'typical_hours' => 2],
                ['name' => 'Toaster', 'wattage' => 800, 'typical_hours' => 0.2],
                ['name' => 'Coffee Maker', 'wattage' => 600, 'typical_hours' => 0.3],
            ]
        ],
        [
            'category' => 'Entertainment',
            'items' => [
                ['name' => 'LED TV (32")', 'wattage' => 40, 'typical_hours' => 5],
                ['name' => 'LED TV (43")', 'wattage' => 60, 'typical_hours' => 5],
                ['name' => 'LED TV (55")', 'wattage' => 80, 'typical_hours' => 5],
                ['name' => 'Plasma TV', 'wattage' => 200, 'typical_hours' => 5],
                ['name' => 'Sound System', 'wattage' => 100, 'typical_hours' => 3],
                ['name' => 'DSTV Decoder', 'wattage' => 30, 'typical_hours' => 8],
                ['name' => 'Game Console', 'wattage' => 150, 'typical_hours' => 2],
                ['name' => 'WiFi Router', 'wattage' => 10, 'typical_hours' => 24],
            ]
        ],
        [
            'category' => 'Cooling & Heating',
            'items' => [
                ['name' => 'Ceiling Fan', 'wattage' => 75, 'typical_hours' => 8],
                ['name' => 'Standing Fan', 'wattage' => 50, 'typical_hours' => 8],
                ['name' => 'Table Fan', 'wattage' => 35, 'typical_hours' => 8],
                ['name' => 'AC (1HP)', 'wattage' => 1000, 'typical_hours' => 8],
                ['name' => 'AC (1.5HP)', 'wattage' => 1500, 'typical_hours' => 8],
                ['name' => 'AC (2HP)', 'wattage' => 2000, 'typical_hours' => 8],
                ['name' => 'Water Heater (Instant)', 'wattage' => 3000, 'typical_hours' => 0.5],
                ['name' => 'Water Heater (Tank)', 'wattage' => 1500, 'typical_hours' => 2],
                ['name' => 'Electric Iron', 'wattage' => 1000, 'typical_hours' => 0.5],
            ]
        ],
        [
            'category' => 'Lighting',
            'items' => [
                ['name' => 'LED Bulb (9W)', 'wattage' => 9, 'typical_hours' => 6],
                ['name' => 'LED Bulb (15W)', 'wattage' => 15, 'typical_hours' => 6],
                ['name' => 'CFL Bulb', 'wattage' => 20, 'typical_hours' => 6],
                ['name' => 'Incandescent Bulb', 'wattage' => 60, 'typical_hours' => 6],
                ['name' => 'Fluorescent Tube', 'wattage' => 40, 'typical_hours' => 6],
                ['name' => 'Security Light', 'wattage' => 20, 'typical_hours' => 12],
            ]
        ],
        [
            'category' => 'Office & Computing',
            'items' => [
                ['name' => 'Laptop', 'wattage' => 60, 'typical_hours' => 8],
                ['name' => 'Desktop Computer', 'wattage' => 200, 'typical_hours' => 8],
                ['name' => 'Monitor (22")', 'wattage' => 30, 'typical_hours' => 8],
                ['name' => 'Printer', 'wattage' => 50, 'typical_hours' => 1],
                ['name' => 'Phone Charger', 'wattage' => 10, 'typical_hours' => 2],
            ]
        ],
        [
            'category' => 'Laundry',
            'items' => [
                ['name' => 'Washing Machine (Top Load)', 'wattage' => 500, 'typical_hours' => 1],
                ['name' => 'Washing Machine (Front Load)', 'wattage' => 400, 'typical_hours' => 1],
                ['name' => 'Dryer', 'wattage' => 3000, 'typical_hours' => 0.5],
            ]
        ],
        [
            'category' => 'Water & Pumps',
            'items' => [
                ['name' => 'Water Pump (0.5HP)', 'wattage' => 400, 'typical_hours' => 2],
                ['name' => 'Water Pump (1HP)', 'wattage' => 750, 'typical_hours' => 2],
                ['name' => 'Borehole Pump', 'wattage' => 1500, 'typical_hours' => 3],
            ]
        ],
    ];

    /**
     * Battery recommendations based on capacity
     */
    private const BATTERY_RECOMMENDATIONS = [
        ['min_ah' => 0, 'max_ah' => 50, 'type' => 'Lead-Acid (Automotive)', 'price_range' => '150,000 - 300,000 UGX'],
        ['min_ah' => 50, 'max_ah' => 100, 'type' => 'Deep Cycle Lead-Acid', 'price_range' => '400,000 - 600,000 UGX'],
        ['min_ah' => 100, 'max_ah' => 200, 'type' => 'Deep Cycle AGM', 'price_range' => '800,000 - 1,500,000 UGX'],
        ['min_ah' => 200, 'max_ah' => 500, 'type' => 'Lithium LiFePO4', 'price_range' => '2,000,000 - 5,000,000 UGX'],
        ['min_ah' => 500, 'max_ah' => PHP_INT_MAX, 'type' => 'Lithium Battery Bank', 'price_range' => '5,000,000+ UGX'],
    ];

    /**
     * Display the power calculator page
     */
    public function index(): View
    {
        return view(VIEW_FILE_NAMES['power_calculator'] ?? 'theme-views.power-calculator', [
            'presets' => self::APPLIANCE_PRESETS,
            'tariff_info' => [
                'rate_lifeline' => self::RATE_LIFELINE,
                'rate_tier_1' => self::RATE_TIER_1,
                'rate_tier_2' => self::RATE_TIER_2,
                'rate_tier_3' => self::RATE_TIER_3,
                'limit_lifeline' => self::LIMIT_LIFELINE,
                'limit_tier_1' => self::LIMIT_TIER_1,
                'limit_tier_2' => self::LIMIT_TIER_2,
                'limit_eligibility' => self::LIMIT_ELIGIBILITY,
            ],
        ]);
    }

    /**
     * Calculate power consumption and backup requirements
     */
    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'devices' => 'required|array|min:1',
            'devices.*.name' => 'required|string|max:100',
            'devices.*.wattage' => 'required|numeric|min:0|max:50000',
            'devices.*.hours' => 'required|numeric|min:0|max:24',
            'backup_hours' => 'sometimes|numeric|min:1|max:24',
        ]);

        $devices = $request->input('devices');
        $backupHours = $request->input('backup_hours', 4);

        $totalDailyWh = 0;
        $totalWattage = 0;
        $deviceBreakdown = [];

        foreach ($devices as $device) {
            $wattage = floatval($device['wattage']);
            $hours = floatval($device['hours']);
            $dailyWh = $wattage * $hours;
            
            $totalDailyWh += $dailyWh;
            $totalWattage += $wattage;

            $deviceBreakdown[] = [
                'name' => $device['name'],
                'wattage' => $wattage,
                'hours' => $hours,
                'daily_wh' => round($dailyWh, 2),
                'monthly_kwh' => round(($dailyWh / 1000) * self::DAYS_PER_MONTH, 2),
            ];
        }

        // Monthly consumption in kWh
        $monthlyKwh = ($totalDailyWh / 1000) * self::DAYS_PER_MONTH;

        // Calculate electricity bill using UEDCL tiered pricing
        $billingResult = $this->calculateElectricityBill($monthlyKwh);
        $monthlyCost = $billingResult['cost'];
        $lifelineEligible = $billingResult['lifeline_eligible'];

        // Backup calculations
        $backupWh = $totalWattage * $backupHours;
        $batteryAh = ($backupWh / self::DEPTH_OF_DISCHARGE) / self::SYSTEM_VOLTAGE;

        // Get battery recommendation
        $batteryRecommendation = $this->getBatteryRecommendation($batteryAh);

        // Inverter sizing (add 20% safety margin)
        $inverterVa = $totalWattage * 1.2;
        $inverterRecommendation = $this->getInverterRecommendation($inverterVa);

        // Solar panel sizing (assuming 5 peak sun hours in Uganda)
        $solarWatts = ($totalDailyWh / 5) * 1.3; // 30% system loss factor

        return response()->json([
            'success' => true,
            'summary' => [
                'total_wattage' => round($totalWattage, 2),
                'daily_consumption_wh' => round($totalDailyWh, 2),
                'monthly_consumption_kwh' => round($monthlyKwh, 2),
                'monthly_bill_ugx' => round($monthlyCost),
                'monthly_bill_formatted' => number_format(round($monthlyCost)) . ' UGX',
                'lifeline_eligible' => $lifelineEligible,
            ],
            'backup' => [
                'backup_hours' => $backupHours,
                'backup_energy_wh' => round($backupWh, 2),
                'battery_ah' => round($batteryAh, 2),
                'battery_recommendation' => $batteryRecommendation,
                'inverter_va' => round($inverterVa, 2),
                'inverter_recommendation' => $inverterRecommendation,
            ],
            'solar' => [
                'recommended_watts' => round($solarWatts, 2),
                'panels_250w' => ceil($solarWatts / 250),
                'panels_450w' => ceil($solarWatts / 450),
            ],
            'devices' => $deviceBreakdown,
            'tariff_info' => [
                'rate_lifeline' => self::RATE_LIFELINE,
                'rate_tier_1' => self::RATE_TIER_1,
                'rate_tier_2' => self::RATE_TIER_2,
                'rate_tier_3' => self::RATE_TIER_3,
                'limit_lifeline' => self::LIMIT_LIFELINE,
                'limit_tier_1' => self::LIMIT_TIER_1,
                'limit_tier_2' => self::LIMIT_TIER_2,
                'limit_eligibility' => self::LIMIT_ELIGIBILITY,
            ],
        ]);
    }

    /**
     * Calculate electricity bill using UEDCL Q4 2025 tiered pricing
     * Implements lifeline eligibility logic from official tariff document
     */
    private function calculateElectricityBill(float $monthlyKwh): array
    {
        $monthlyCost = 0;
        $eligibleForLifeline = false;

        // CHECK: Lifeline Eligibility (consumption must not exceed 100 units)
        if ($monthlyKwh <= self::LIMIT_ELIGIBILITY) {
            $eligibleForLifeline = true;

            // Scenario A: Consumption <= 100 units (Gets Lifeline)
            if ($monthlyKwh <= self::LIMIT_LIFELINE) {
                // 0-15 units
                $monthlyCost = $monthlyKwh * self::RATE_LIFELINE;
            } elseif ($monthlyKwh <= self::LIMIT_TIER_1) {
                // 16-80 units
                $monthlyCost = (self::LIMIT_LIFELINE * self::RATE_LIFELINE) +
                               (($monthlyKwh - self::LIMIT_LIFELINE) * self::RATE_TIER_1);
            } else {
                // 81-100 units
                $monthlyCost = (self::LIMIT_LIFELINE * self::RATE_LIFELINE) +
                               ((self::LIMIT_TIER_1 - self::LIMIT_LIFELINE) * self::RATE_TIER_1) +
                               (($monthlyKwh - self::LIMIT_TIER_1) * self::RATE_TIER_2);
            }
        } else {
            // Scenario B: Consumption > 100 units (LOSE Lifeline on first 15)
            if ($monthlyKwh <= self::LIMIT_TIER_2) {
                // 101-150 units
                $monthlyCost = (self::LIMIT_TIER_1 * self::RATE_TIER_1) +
                               (($monthlyKwh - self::LIMIT_TIER_1) * self::RATE_TIER_2);
            } else {
                // > 150 units
                $monthlyCost = (self::LIMIT_TIER_1 * self::RATE_TIER_1) +
                               ((self::LIMIT_TIER_2 - self::LIMIT_TIER_1) * self::RATE_TIER_2) +
                               (($monthlyKwh - self::LIMIT_TIER_2) * self::RATE_TIER_3);
            }
        }

        return [
            'cost' => $monthlyCost,
            'lifeline_eligible' => $eligibleForLifeline,
        ];
    }

    /**
     * Get battery recommendation based on required capacity
     */
    private function getBatteryRecommendation(float $batteryAh): array
    {
        foreach (self::BATTERY_RECOMMENDATIONS as $rec) {
            if ($batteryAh >= $rec['min_ah'] && $batteryAh < $rec['max_ah']) {
                return [
                    'type' => $rec['type'],
                    'capacity_ah' => ceil($batteryAh / 10) * 10, // Round up to nearest 10
                    'price_range' => $rec['price_range'],
                ];
            }
        }

        return [
            'type' => 'Commercial Battery System',
            'capacity_ah' => ceil($batteryAh),
            'price_range' => 'Contact us for quote',
        ];
    }

    /**
     * Get inverter recommendation based on required VA
     */
    private function getInverterRecommendation(float $inverterVa): array
    {
        $sizes = [600, 1000, 1500, 2000, 3000, 5000, 10000];
        $recommendedSize = 600;

        foreach ($sizes as $size) {
            if ($inverterVa <= $size) {
                $recommendedSize = $size;
                break;
            }
            $recommendedSize = $size;
        }

        return [
            'minimum_va' => $recommendedSize,
            'recommended_va' => $recommendedSize > 1000 ? $recommendedSize : ($recommendedSize * 1.5),
            'type' => $recommendedSize <= 1500 ? 'Modified Sine Wave' : 'Pure Sine Wave',
        ];
    }

    /**
     * Get appliance presets for frontend
     */
    public function getPresets(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'presets' => self::APPLIANCE_PRESETS,
        ]);
    }

    /**
     * Get product recommendations from Yoola catalog
     */
    public function getProductRecommendations(Request $request): JsonResponse
    {
        $batteryAh = $request->input('battery_ah', 0);
        $inverterVa = $request->input('inverter_va', 0);

        // Search for relevant products in our catalog
        $products = [];

        // Battery products
        if ($batteryAh > 0) {
            $batteries = \App\Models\Product::active()
                ->where(function ($query) {
                    $query->where('name', 'like', '%battery%')
                          ->orWhere('name', 'like', '%inverter%')
                          ->orWhere('name', 'like', '%ups%')
                          ->orWhere('name', 'like', '%solar%')
                          ->orWhere('name', 'like', '%power%');
                })
                ->select('id', 'name', 'slug', 'unit_price', 'thumbnail')
                ->take(6)
                ->get();

            $products['power_solutions'] = $batteries->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'url' => route('product', $product->slug),
                    'price' => currency_converter($product->unit_price),
                    'image' => getStorageImages(path: $product->thumbnail, type: 'product'),
                ];
            });
        }

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Capture lead from power calculator
     * Stores contact info + calculation data for follow-up
     */
    public function captureLead(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'calculation' => 'sometimes|array',
        ]);

        $name = $request->input('name');
        $phone = $request->input('phone');
        $calculation = $request->input('calculation', []);

        // Store in database
        try {
            \DB::table('power_calculator_leads')->insert([
                'name' => $name,
                'phone' => $phone,
                'monthly_kwh' => $calculation['monthlyKwh'] ?? null,
                'monthly_cost' => $calculation['monthlyCost'] ?? null,
                'battery_ah' => $calculation['batteryAh'] ?? null,
                'inverter_va' => $calculation['inverterVa'] ?? null,
                'solar_panels' => $calculation['solarPanels'] ?? null,
                'total_wattage' => $calculation['totalWattage'] ?? null,
                'appliances' => json_encode($calculation['appliances'] ?? []),
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Fallback to file storage if DB not available
            \Illuminate\Support\Facades\Log::channel('daily')->info('Power Calculator Lead', [
                'name' => $name,
                'phone' => $phone,
                'calculation' => $calculation,
                'ip' => $request->ip(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you! We\'ll send your personalized report shortly.',
        ]);
    }
}
