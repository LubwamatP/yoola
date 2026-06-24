<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerCalculatorTariff extends Model
{
    protected $table = 'power_calculator_tariffs';

    protected $fillable = [
        'name',
        'code',
        'tariff_type',
        'rate_lifeline',
        'rate_tier1',
        'rate_tier2',
        'rate_tier3',
        'rate_peak',
        'rate_shoulder',
        'rate_off_peak',
        'rate_average',
        'limit_lifeline',
        'limit_tier1',
        'limit_tier2',
        'limit_eligibility',
        'voltage',
        'max_demand',
        'description',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'rate_lifeline' => 'decimal:2',
        'rate_tier1' => 'decimal:2',
        'rate_tier2' => 'decimal:2',
        'rate_tier3' => 'decimal:2',
        'rate_peak' => 'decimal:2',
        'rate_shoulder' => 'decimal:2',
        'rate_off_peak' => 'decimal:2',
        'rate_average' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function getDefault()
    {
        return self::where('is_default', true)->where('is_active', true)->first()
            ? self::where('is_active', true)->where('tariff_type', 'domestic')->first()
            ? self::where('is_active', true)->first();
    }

    public static function getDomestic()
    {
        return self::where('tariff_type', 'domestic')->where('is_active', true)->first();
    }

    public static function getByCode(string $code)
    {
        return self::where('code', $code)->where('is_active', true)->first();
    }

    public function isDomestic(): bool
    {
        return $this->tariff_type === 'domestic';
    }

    public function isTimeOfUse(): bool
    {
        return in_array($this->tariff_type, ['commercial', 'industrial']);
    }
}
