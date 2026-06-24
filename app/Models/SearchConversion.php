<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchConversion extends Model
{
    protected $table = 'search_conversions';

    protected $fillable = [
        'search_analytics_id',
        'order_id',
        'product_id',
        'order_amount',
    ];

    protected $casts = [
        'order_amount' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Search analytics relationship
     */
    public function searchAnalytics(): BelongsTo
    {
        return $this->belongsTo(SearchAnalytics::class, 'search_analytics_id');
    }

    /**
     * Order relationship
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Product relationship
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
