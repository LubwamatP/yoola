<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PowerCalculatorCategory extends Model
{
    protected $table = 'power_calculator_categories';

    protected $fillable = [
        'name',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get appliances by matching category name
     */
    public function appliances(): HasMany
    {
        return $this->hasMany(PowerCalculatorAppliance::class, 'category', 'name')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public static function getActiveWithAppliances()
    {
        return self::where('is_active', true)
            ->with('appliances')
            ->orderBy('sort_order')
            ->get();
    }
}
