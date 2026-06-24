<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PowerCalculatorAppliance extends Model
{
    protected $table = 'power_calculator_appliances';

    protected $fillable = [
        'category',
        'name',
        'name_local',
        'wattage',
        'default_hours',
        'icon',
        'sort_order',
        'is_active',
        'recommended_product_id',
        'energy_tip',
    ];

    protected $casts = [
        'wattage' => 'integer',
        'default_hours' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Category is a string field, not a foreign key
    public function getCategoryNameAttribute(): string
    {
        return $this->category ?? 'Uncategorized';
    }
}
