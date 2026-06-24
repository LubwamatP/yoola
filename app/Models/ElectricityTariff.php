<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectricityTariff extends Model
{
    protected $fillable = [
        'name',
        'code',
        'rate_per_kwh',
        'service_fee',
        'vat_percentage',
        'min_units',
        'max_units',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rate_per_kwh' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active tariffs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered tariffs
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Calculate cost for given units
     */
    public function calculateCost(int $units): array
    {
        $baseCost = $units * $this->rate_per_kwh;
        $vatAmount = $baseCost * ($this->vat_percentage / 100);
        $totalCost = $baseCost + $vatAmount + $this->service_fee;

        return [
            'units' => $units,
            'rate_per_kwh' => $this->rate_per_kwh,
            'base_cost' => round($baseCost, 2),
            'vat_percentage' => $this->vat_percentage,
            'vat_amount' => round($vatAmount, 2),
            'service_fee' => $this->service_fee,
            'total_cost' => round($totalCost, 2),
        ];
    }

    /**
     * Get tariff for given units (handles tiered pricing)
     */
    public static function getTariffForUnits(int $units, string $type = 'domestic'): ?self
    {
        $prefix = match($type) {
            'commercial' => 'COMMERCIAL',
            'industrial' => 'INDUSTRIAL',
            default => 'DOM',
        };

        return self::active()
            ->where('code', 'LIKE', $prefix . '%')
            ->where('min_units', '<=', $units)
            ->where(function ($query) use ($units) {
                $query->whereNull('max_units')
                    ->orWhere('max_units', '>=', $units);
            })
            ->orderBy('min_units', 'desc')
            ->first();
    }
}
