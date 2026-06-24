<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * BundleCategory Model
 * 
 * Represents a category for organizing bundles
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon
 * @property int $display_order
 * @property bool $is_active
 */
class BundleCategory extends Model
{
    protected $table = 'bundle_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get bundles in this category
     */
    public function bundles(): HasMany
    {
        return $this->hasMany(Bundle::class, 'bundle_category_id');
    }

    /**
     * Scope: Active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Ordered by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Get active bundles count
     */
    public function getActiveBundlesCountAttribute(): int
    {
        return $this->bundles()->available()->count();
    }

    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate slug if not provided
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = \Str::slug($model->name);
            }
        });
    }
}
