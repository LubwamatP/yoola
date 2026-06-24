<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ElectricityTariff;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ElectricityTariffController extends Controller
{
    /**
     * Display a listing of tariffs
     */
    public function index(Request $request): View
    {
        $tariffs = ElectricityTariff::ordered()->paginate(20);
        
        return view('admin-views.electricity-tariffs.index', compact('tariffs'));
    }

    /**
     * Show form for creating new tariff
     */
    public function create(): View
    {
        return view('admin-views.electricity-tariffs.create');
    }

    /**
     * Store a new tariff
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:electricity_tariffs,code',
            'rate_per_kwh' => 'required|numeric|min:0',
            'service_fee' => 'required|numeric|min:0',
            'vat_percentage' => 'required|numeric|min:0|max:100',
            'min_units' => 'required|integer|min:0',
            'max_units' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        ElectricityTariff::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'rate_per_kwh' => $request->rate_per_kwh,
            'service_fee' => $request->service_fee,
            'vat_percentage' => $request->vat_percentage,
            'min_units' => $request->min_units,
            'max_units' => $request->max_units,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        Toastr::success(translate('Tariff created successfully'));
        return redirect()->route('admin.electricity-tariffs.index');
    }

    /**
     * Show form for editing tariff
     */
    public function edit(int $id): View
    {
        $tariff = ElectricityTariff::findOrFail($id);
        return view('admin-views.electricity-tariffs.edit', compact('tariff'));
    }

    /**
     * Update tariff
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $tariff = ElectricityTariff::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:electricity_tariffs,code,' . $id,
            'rate_per_kwh' => 'required|numeric|min:0',
            'service_fee' => 'required|numeric|min:0',
            'vat_percentage' => 'required|numeric|min:0|max:100',
            'min_units' => 'required|integer|min:0',
            'max_units' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $tariff->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'rate_per_kwh' => $request->rate_per_kwh,
            'service_fee' => $request->service_fee,
            'vat_percentage' => $request->vat_percentage,
            'min_units' => $request->min_units,
            'max_units' => $request->max_units,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        Toastr::success(translate('Tariff updated successfully'));
        return redirect()->route('admin.electricity-tariffs.index');
    }

    /**
     * Toggle tariff status
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $tariff = ElectricityTariff::findOrFail($id);
        $tariff->update(['is_active' => !$tariff->is_active]);

        return response()->json([
            'success' => true,
            'status' => $tariff->is_active,
            'message' => $tariff->is_active ? 'Tariff activated' : 'Tariff deactivated',
        ]);
    }

    /**
     * Delete tariff
     */
    public function destroy(int $id): RedirectResponse
    {
        $tariff = ElectricityTariff::findOrFail($id);
        $tariff->delete();

        Toastr::success(translate('Tariff deleted successfully'));
        return redirect()->route('admin.electricity-tariffs.index');
    }

    /**
     * API: Get all active tariffs
     */
    public function apiList(): JsonResponse
    {
        $tariffs = ElectricityTariff::active()->ordered()->get();
        return response()->json($tariffs);
    }
}
