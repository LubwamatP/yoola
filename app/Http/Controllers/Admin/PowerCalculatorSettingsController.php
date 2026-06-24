<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerCalculatorTariff;
use App\Models\PowerCalculatorCategory;
use App\Models\PowerCalculatorAppliance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\View\View;

class PowerCalculatorSettingsController extends Controller
{
    /**
     * Main settings page
     */
    public function index(): View
    {
        $tariffs = collect();
        $categories = collect();
        $stats = ['total_appliances' => 0, 'total_categories' => 0, 'total_leads' => 0];

        try {
            if (Schema::hasTable('power_calculator_tariffs')) {
                $tariffs = PowerCalculatorTariff::orderBy('is_default', 'desc')->get();
            }

            if (Schema::hasTable('power_calculator_categories')) {
                $categories = PowerCalculatorCategory::with('appliances')->orderBy('sort_order')->get();
                $stats['total_categories'] = $categories->count();
                $stats['total_appliances'] = PowerCalculatorAppliance::count();
            }

            if (Schema::hasTable('power_calculator_leads')) {
                $stats['total_leads'] = DB::table('power_calculator_leads')->count();
            }
        } catch (\Exception $e) {
            // Tables don't exist yet
        }

        return view('admin-views.power-calculator.settings', [
            'tariffs' => $tariffs,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }

    /**
     * Store new tariff
     */
    public function storeTariff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'rate_lifeline' => 'required|numeric|min:0',
            'rate_tier_1' => 'required|numeric|min:0',
            'rate_tier_2' => 'required|numeric|min:0',
            'rate_tier_3' => 'required|numeric|min:0',
            'limit_lifeline' => 'required|integer|min:0',
            'limit_tier_1' => 'required|integer|min:0',
            'limit_tier_2' => 'required|integer|min:0',
            'limit_eligibility' => 'required|integer|min:0',
        ]);

        // If setting as default, unset others
        if ($request->input('is_default')) {
            PowerCalculatorTariff::where('is_default', true)->update(['is_default' => false]);
        }

        PowerCalculatorTariff::create($request->all());

        return back()->with('success', translate('Tariff_created_successfully'));
    }

    /**
     * Update tariff
     */
    public function updateTariff(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'rate_lifeline' => 'required|numeric|min:0',
            'rate_tier_1' => 'required|numeric|min:0',
            'rate_tier_2' => 'required|numeric|min:0',
            'rate_tier_3' => 'required|numeric|min:0',
        ]);

        $tariff = PowerCalculatorTariff::findOrFail($id);

        // If setting as default, unset others
        if ($request->input('is_default') && !$tariff->is_default) {
            PowerCalculatorTariff::where('is_default', true)->update(['is_default' => false]);
        }

        $tariff->update($request->all());

        return back()->with('success', translate('Tariff_updated_successfully'));
    }

    /**
     * Delete tariff
     */
    public function deleteTariff(int $id)
    {
        $tariff = PowerCalculatorTariff::findOrFail($id);
        
        if ($tariff->is_default) {
            return back()->with('error', translate('Cannot_delete_default_tariff'));
        }

        $tariff->delete();

        return back()->with('success', translate('Tariff_deleted_successfully'));
    }

    /**
     * Store new category
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
        ]);

        $maxOrder = PowerCalculatorCategory::max('sort_order') ?? 0;

        PowerCalculatorCategory::create([
            'name' => $request->input('name'),
            'icon' => $request->input('icon'),
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return back()->with('success', translate('Category_created_successfully'));
    }

    /**
     * Update category
     */
    public function updateCategory(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $category = PowerCalculatorCategory::findOrFail($id);
        $category->update($request->only(['name', 'icon', 'is_active']));

        return back()->with('success', translate('Category_updated_successfully'));
    }

    /**
     * Delete category
     */
    public function deleteCategory(int $id)
    {
        $category = PowerCalculatorCategory::findOrFail($id);
        $category->delete(); // Will cascade delete appliances

        return back()->with('success', translate('Category_deleted_successfully'));
    }

    /**
     * Store new appliance
     */
    public function storeAppliance(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:power_calculator_categories,id',
            'name' => 'required|string|max:100',
            'wattage' => 'required|integer|min:1',
            'typical_hours' => 'required|numeric|min:0|max:24',
        ]);

        // Get category name from ID
        $category = PowerCalculatorCategory::find($request->input('category_id'));
        $categoryName = $category ? $category->name : 'Uncategorized';

        $maxOrder = PowerCalculatorAppliance::where('category', $categoryName)
            ->max('sort_order') ?? 0;

        PowerCalculatorAppliance::create([
            'category' => $categoryName,
            'name' => $request->input('name'),
            'wattage' => $request->input('wattage'),
            'default_hours' => $request->input('typical_hours'),
            'icon' => $request->input('icon'),
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return back()->with('success', translate('Appliance_added_successfully'));
    }

    /**
     * Update appliance
     */
    public function updateAppliance(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'wattage' => 'required|integer|min:1',
            'typical_hours' => 'required|numeric|min:0|max:24',
        ]);

        $appliance = PowerCalculatorAppliance::findOrFail($id);
        $appliance->update([
            'name' => $request->input('name'),
            'wattage' => $request->input('wattage'),
            'default_hours' => $request->input('typical_hours'),
            'icon' => $request->input('icon'),
            'is_active' => $request->input('is_active', true),
        ]);

        return back()->with('success', translate('Appliance_updated_successfully'));
    }

    /**
     * Delete appliance
     */
    public function deleteAppliance(int $id)
    {
        PowerCalculatorAppliance::findOrFail($id)->delete();

        return back()->with('success', translate('Appliance_deleted_successfully'));
    }

    /**
     * Seed default data
     */
    public function seedDefaults()
    {
        // Create all UEDCL tariffs if none exist
        if (PowerCalculatorTariff::count() === 0) {
            $tariffs = [
                // DOMESTIC - CODE 10.1
                [
                    'name' => 'Domestic Consumers',
                    'code' => '10.1',
                    'tariff_type' => 'domestic',
                    'voltage' => '240V Single Phase',
                    'description' => 'Low Voltage Single Phase supplied at 240 volts',
                    'rate_lifeline' => 250.0,
                    'rate_tier1' => 756.2,
                    'rate_tier2' => 412.0,
                    'rate_tier3' => 756.2,
                    'limit_lifeline' => 15,
                    'limit_tier1' => 80,
                    'limit_tier2' => 150,
                    'limit_eligibility' => 100,
                    'is_default' => true,
                    'is_active' => true,
                ],
                // COMMERCIAL - CODE 10.2
                [
                    'name' => 'Commercial Consumers',
                    'code' => '10.2',
                    'tariff_type' => 'commercial',
                    'voltage' => '415V Three Phase',
                    'max_demand' => 'Up to 100 Amperes',
                    'description' => 'Three-phase low voltage load not exceeding 100 Amperes',
                    'rate_peak' => 650.8,
                    'rate_shoulder' => 546.4,
                    'rate_off_peak' => 414.0,
                    'rate_average' => 546.4,
                    'is_active' => true,
                ],
                // MEDIUM MANUFACTURING - CODE 20.1
                [
                    'name' => 'Medium Consumers - Manufacturing',
                    'code' => '20.1',
                    'tariff_type' => 'industrial',
                    'voltage' => '415V',
                    'max_demand' => 'Up to 500kVA',
                    'description' => 'Low voltage 415V with maximum demand up to 500kVA',
                    'rate_peak' => 428.0,
                    'rate_shoulder' => 355.1,
                    'rate_off_peak' => 275.6,
                    'rate_average' => 355.1,
                    'is_active' => true,
                ],
                // MEDIUM SERVICE - CODE 20.2
                [
                    'name' => 'Medium Consumers - Service',
                    'code' => '20.2',
                    'tariff_type' => 'industrial',
                    'voltage' => '415V',
                    'max_demand' => 'Up to 500kVA',
                    'description' => 'Low voltage 415V with maximum demand up to 500kVA (Service)',
                    'rate_peak' => 497.5,
                    'rate_shoulder' => 412.8,
                    'rate_off_peak' => 320.4,
                    'rate_average' => 412.8,
                    'is_active' => true,
                ],
                // LARGE INDUSTRIAL MANUFACTURING - CODE 30.1 Block 1
                [
                    'name' => 'Large Industrial - Manufacturing (Block 1)',
                    'code' => '30.1-B1',
                    'tariff_type' => 'industrial',
                    'voltage' => '11kV/33kV',
                    'max_demand' => '500kVA - 1,500kVA',
                    'description' => 'High Voltage 11kV/33kV, demand 500-1500kVA (Block 1)',
                    'rate_peak' => 370.5,
                    'rate_shoulder' => 300.4,
                    'rate_off_peak' => 231.6,
                    'rate_average' => 300.4,
                    'is_active' => true,
                ],
                // LARGE INDUSTRIAL MANUFACTURING - CODE 30.1 Block 2
                [
                    'name' => 'Large Industrial - Manufacturing (Block 2)',
                    'code' => '30.1-B2',
                    'tariff_type' => 'industrial',
                    'voltage' => '11kV/33kV',
                    'max_demand' => '500kVA - 1,500kVA',
                    'description' => 'High Voltage 11kV/33kV, demand 500-1500kVA (Declining Block)',
                    'rate_peak' => 349.0,
                    'rate_shoulder' => 282.9,
                    'rate_off_peak' => 218.1,
                    'rate_average' => 282.9,
                    'is_active' => true,
                ],
                // LARGE INDUSTRIAL SERVICE - CODE 30.2
                [
                    'name' => 'Large Industrial - Service',
                    'code' => '30.2',
                    'tariff_type' => 'industrial',
                    'voltage' => '11kV/33kV',
                    'max_demand' => '500kVA - 1,500kVA',
                    'description' => 'High Voltage 11kV/33kV with demand 500-1500kVA (Service)',
                    'rate_peak' => 420.5,
                    'rate_shoulder' => 348.7,
                    'rate_off_peak' => 278.2,
                    'rate_average' => 348.7,
                    'is_active' => true,
                ],
                // EXTRA LARGE INDUSTRIAL - CODE 40.1
                [
                    'name' => 'Extra Large Industrial - Manufacturing',
                    'code' => '40.1',
                    'tariff_type' => 'industrial',
                    'voltage' => '11kV/33kV',
                    'max_demand' => 'At least 1,500kVA',
                    'description' => 'High Voltage 11kV/33kV with average demand at least 1,500kVA (Manufacturing)',
                    'rate_peak' => 229.1,
                    'rate_shoulder' => 203.6,
                    'rate_off_peak' => 184.6,
                    'rate_average' => 203.6,
                    'is_active' => true,
                ],
                // EXTRA LARGE INDUSTRIAL - CODE 40.2
                [
                    'name' => 'Extra Large Industrial - 10,000kVA+',
                    'code' => '40.2',
                    'tariff_type' => 'industrial',
                    'voltage' => '11kV/33kV',
                    'max_demand' => 'At least 10,000kVA',
                    'description' => 'High Voltage 11kV/33kV with average demand at least 10,000kVA',
                    'rate_peak' => 305.4,
                    'rate_shoulder' => 203.6,
                    'rate_off_peak' => 184.6,
                    'rate_average' => 219.3,
                    'is_active' => true,
                ],
                // PUBLIC AMENITIES - CODE 50
                [
                    'name' => 'Public Amenities',
                    'code' => '50',
                    'tariff_type' => 'commercial',
                    'description' => 'Public Hospitals and Street Lighting (Municipalities/Cities/Towns)',
                    'rate_average' => 360.0,
                    'is_active' => true,
                ],
            ];

            foreach ($tariffs as $tariff) {
                PowerCalculatorTariff::create($tariff);
            }
        }

        // Create default categories and appliances
        if (PowerCalculatorCategory::count() === 0) {
            $defaults = [
                ['name' => 'Kitchen Appliances', 'icon' => 'fi-sr-utensils', 'appliances' => [
                    ['name' => 'Refrigerator (Small)', 'wattage' => 100, 'typical_hours' => 24],
                    ['name' => 'Refrigerator (Large)', 'wattage' => 200, 'typical_hours' => 24],
                    ['name' => 'Freezer', 'wattage' => 150, 'typical_hours' => 24],
                    ['name' => 'Microwave', 'wattage' => 1000, 'typical_hours' => 0.5],
                    ['name' => 'Electric Kettle', 'wattage' => 1500, 'typical_hours' => 0.3],
                    ['name' => 'Rice Cooker', 'wattage' => 500, 'typical_hours' => 1],
                    ['name' => 'Blender', 'wattage' => 400, 'typical_hours' => 0.2],
                    ['name' => 'Electric Cooker', 'wattage' => 2000, 'typical_hours' => 2],
                ]],
                ['name' => 'Entertainment', 'icon' => 'fi-sr-tv-music', 'appliances' => [
                    ['name' => 'LED TV (32")', 'wattage' => 40, 'typical_hours' => 5],
                    ['name' => 'LED TV (43")', 'wattage' => 60, 'typical_hours' => 5],
                    ['name' => 'LED TV (55")', 'wattage' => 80, 'typical_hours' => 5],
                    ['name' => 'Sound System', 'wattage' => 100, 'typical_hours' => 3],
                    ['name' => 'DSTV Decoder', 'wattage' => 30, 'typical_hours' => 8],
                    ['name' => 'WiFi Router', 'wattage' => 10, 'typical_hours' => 24],
                ]],
                ['name' => 'Lighting', 'icon' => 'fi-sr-lightbulb-on', 'appliances' => [
                    ['name' => 'LED Bulb (9W)', 'wattage' => 9, 'typical_hours' => 6],
                    ['name' => 'LED Bulb (15W)', 'wattage' => 15, 'typical_hours' => 6],
                    ['name' => 'Fluorescent Tube', 'wattage' => 36, 'typical_hours' => 6],
                    ['name' => 'Security Light', 'wattage' => 50, 'typical_hours' => 12],
                ]],
                ['name' => 'Cooling & Heating', 'icon' => 'fi-sr-temperature-high', 'appliances' => [
                    ['name' => 'Standing Fan', 'wattage' => 50, 'typical_hours' => 8],
                    ['name' => 'Ceiling Fan', 'wattage' => 75, 'typical_hours' => 8],
                    ['name' => 'AC (1HP)', 'wattage' => 900, 'typical_hours' => 6],
                    ['name' => 'AC (1.5HP)', 'wattage' => 1200, 'typical_hours' => 6],
                    ['name' => 'AC (2HP)', 'wattage' => 1800, 'typical_hours' => 6],
                    ['name' => 'Water Heater', 'wattage' => 2000, 'typical_hours' => 0.5],
                ]],
                ['name' => 'Laundry', 'icon' => 'fi-sr-washer', 'appliances' => [
                    ['name' => 'Washing Machine', 'wattage' => 500, 'typical_hours' => 1],
                    ['name' => 'Iron', 'wattage' => 1000, 'typical_hours' => 0.5],
                    ['name' => 'Clothes Dryer', 'wattage' => 2000, 'typical_hours' => 1],
                ]],
                ['name' => 'Office/Work', 'icon' => 'fi-sr-laptop', 'appliances' => [
                    ['name' => 'Laptop', 'wattage' => 65, 'typical_hours' => 8],
                    ['name' => 'Desktop Computer', 'wattage' => 200, 'typical_hours' => 8],
                    ['name' => 'Printer', 'wattage' => 50, 'typical_hours' => 0.5],
                    ['name' => 'Phone Charger', 'wattage' => 10, 'typical_hours' => 2],
                ]],
            ];

            $sortOrder = 0;
            foreach ($defaults as $cat) {
                $category = PowerCalculatorCategory::create([
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'sort_order' => $sortOrder++,
                    'is_active' => true,
                ]);

                $appOrder = 0;
                foreach ($cat['appliances'] as $app) {
                    PowerCalculatorAppliance::create([
                        'category_id' => $category->id,
                        'name' => $app['name'],
                        'wattage' => $app['wattage'],
                        'typical_hours' => $app['typical_hours'],
                        'sort_order' => $appOrder++,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return back()->with('success', translate('Default_data_seeded_successfully'));
    }
}
