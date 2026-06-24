<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BundleProduct Model (Pivot)
 * 
 * Represents a product within a bundle
 *
 * @property int $id
 * @property int $bundle_id
 * @property int $product_id
 * @property int $quantity
 * @property int $display_order
 * @property string|null $variant
 * @property string|null $color
 * @property float|null $custom_price
 */
class BundleProduct extends Model
{
    protected $table = 'bundle_products';

    protected $fillable = [
        'bundle_id',
        'product_id',
        'quantity',
        'display_order',
        'variant',
        'color',
        'custom_price',
    ];

    protected $casts = [
        'bundle_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'display_order' => 'integer',
        'custom_price' => 'float',
    ];

    /**
     * Get the bundle
     */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get effective price for this product in bundle
     */
    public function getEffectivePrice(): float
    {
        return $this->custom_price ?? $this->product->unit_price ?? 0;
    }

    /**
     * Get total price for this item (price * quantity)
     */
    public function getTotalPrice(): float
    {
        return $this->getEffectivePrice() * $this->quantity;
    }

    protected static function boot(): void
    {
        parent::boot();

        // Recalculate bundle prices when products change
        static::saved(function ($model) {
            if ($model->bundle) {
                $model->bundle->recalculatePrices();
            }
        });

        static::deleted(function ($model) {
            if ($model->bundle) {
                $model->bundle->recalculatePrices();
            }
        });
    }
}
