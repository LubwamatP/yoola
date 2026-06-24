<?php

namespace App\Models;

use App\Traits\CacheManagerTrait;
use App\Traits\StorageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

/**
 * Bundle Model
 * 
 * Represents a product bundle with discount
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $short_description
 * @property string $bundle_type
 * @property int|null $bundle_category_id
 * @property string|null $banner_image
 * @property string|null $thumbnail
 * @property string $discount_type
 * @property float $discount_value
 * @property float $original_price
 * @property float $bundle_price
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property bool $is_active
 * @property bool $is_featured
 * @property bool $show_on_homepage
 * @property int $display_order
 * @property string|null $background_color
 * @property string|null $text_color
 * @property string|null $badge_text
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property int $view_count
 * @property int $purchase_count
 * @property string $added_by
 * @property int|null $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Bundle extends Model
{
    use StorageTrait, CacheManagerTrait;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'bundle_type',
        'bundle_category_id',
        'banner_image',
        'banner_image_storage_type',
        'thumbnail',
        'thumbnail_storage_type',
        'discount_type',
        'discount_value',
        'original_price',
        'bundle_price',
        'start_date',
        'end_date',
        'is_active',
        'is_featured',
        'show_on_homepage',
        'display_order',
        'background_color',
        'text_color',
        'badge_text',
        'meta_title',
        'meta_description',
        'meta_image',
        'view_count',
        'purchase_count',
        'added_by',
        'user_id',
    ];

    protected $casts = [
        'bundle_category_id' => 'integer',
        'discount_value' => 'float',
        'original_price' => 'float',
        'bundle_price' => 'float',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'show_on_homepage' => 'boolean',
        'display_order' => 'integer',
        'view_count' => 'integer',
        'purchase_count' => 'integer',
        'user_id' => 'integer',
    ];

    protected $appends = ['banner_full_url', 'thumbnail_full_url', 'savings_amount', 'savings_percentage'];

    // ==================== RELATIONSHIPS ====================

    /**
     * Products in this bundle (with pivot data)
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'bundle_products')
            ->withPivot(['quantity', 'display_order', 'variant', 'color', 'custom_price'])
            ->withTimestamps()
            ->orderBy('bundle_products.display_order');
    }

    /**
     * Bundle products (pivot records)
     */
    public function bundleProducts(): HasMany
    {
        return $this->hasMany(BundleProduct::class);
    }

    /**
     * Bundle category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BundleCategory::class, 'bundle_category_id');
    }

    /**
     * Translations
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    /**
     * SEO meta info
     */
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMetaInfo::class, 'seoable');
    }

    /**
     * Storage records for images
     */
    public function storage(): MorphMany
    {
        return $this->morphMany(Storage::class, 'data');
    }

    // ==================== ACCESSORS ====================

    /**
     * Get translated name
     */
    public function getNameAttribute($name): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor') || strpos(url()->current(), '/seller')) {
            return $name;
        }
        return $this->translations->where('key', 'name')->first()?->value ?? $name;
    }

    /**
     * Get translated description
     */
    public function getDescriptionAttribute($description): string|null
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/vendor')) {
            return $description;
        }
        return $this->translations->where('key', 'description')->first()?->value ?? $description;
    }

    /**
     * Get full URL for banner image
     */
    public function getBannerFullUrlAttribute(): string|null|array
    {
        $value = $this->banner_image;
        if (!$value) {
            return dynamicAsset('public/assets/front-end/img/placeholder-bundle.png');
        }
        
        $storageType = $this->banner_image_storage_type ?? 'public';
        if (count($this->storage) > 0) {
            $storage = $this->storage->where('key', 'banner_image')->first();
            $storageType = $storage['value'] ?? 'public';
        }
        
        return $this->storageLink('bundle', $value, $storageType);
    }

    /**
     * Get full URL for thumbnail
     */
    public function getThumbnailFullUrlAttribute(): string|null|array
    {
        $value = $this->thumbnail;
        if (!$value) {
            return dynamicAsset('public/assets/front-end/img/placeholder-bundle-thumb.png');
        }
        
        $storageType = $this->thumbnail_storage_type ?? 'public';
        if (count($this->storage) > 0) {
            $storage = $this->storage->where('key', 'thumbnail')->first();
            $storageType = $storage['value'] ?? 'public';
        }
        
        return $this->storageLink('bundle/thumbnail', $value, $storageType);
    }

    /**
     * Get savings amount (original - bundle price)
     */
    public function getSavingsAmountAttribute(): float
    {
        return max(0, $this->original_price - $this->bundle_price);
    }

    /**
     * Get savings percentage
     */
    public function getSavingsPercentageAttribute(): float
    {
        if ($this->original_price <= 0) {
            return 0;
        }
        return round((($this->original_price - $this->bundle_price) / $this->original_price) * 100, 1);
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Active bundles only
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Featured bundles only
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Homepage bundles
     */
    public function scopeHomepage(Builder $query): Builder
    {
        return $query->where('show_on_homepage', true);
    }

    /**
     * Scope: Currently valid (within date range)
     */
    public function scopeValid(Builder $query): Builder
    {
        $now = Carbon::now();
        
        return $query->where(function ($q) use ($now) {
            // No date restrictions
            $q->whereNull('start_date')->whereNull('end_date');
        })->orWhere(function ($q) use ($now) {
            // Only start date set and we're past it
            $q->whereNotNull('start_date')
              ->whereNull('end_date')
              ->where('start_date', '<=', $now);
        })->orWhere(function ($q) use ($now) {
            // Only end date set and we haven't passed it
            $q->whereNull('start_date')
              ->whereNotNull('end_date')
              ->where('end_date', '>=', $now);
        })->orWhere(function ($q) use ($now) {
            // Both dates set and we're within range
            $q->whereNotNull('start_date')
              ->whereNotNull('end_date')
              ->where('start_date', '<=', $now)
              ->where('end_date', '>=', $now);
        });
    }

    /**
     * Scope: Available bundles (active + valid dates + has products)
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->active()->valid()->has('products');
    }

    /**
     * Scope: By bundle type
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('bundle_type', $type);
    }

    /**
     * Scope: Ordered by display order
     */
    public function scopeOrdered(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('display_order', $direction);
    }

    // ==================== METHODS ====================

    /**
     * Calculate and update prices based on products
     */
    public function recalculatePrices(): self
    {
        $originalPrice = 0;

        foreach ($this->products as $product) {
            $quantity = $product->pivot->quantity ?? 1;
            $customPrice = $product->pivot->custom_price;
            
            // Use custom price if set, otherwise use product unit price
            $productPrice = $customPrice ?? $product->unit_price;
            $originalPrice += $productPrice * $quantity;
        }

        $this->original_price = $originalPrice;

        // Calculate bundle price based on discount type
        if ($this->discount_type === 'percentage') {
            $discountAmount = ($originalPrice * $this->discount_value) / 100;
            $this->bundle_price = max(0, $originalPrice - $discountAmount);
        } else {
            // Fixed discount
            $this->bundle_price = max(0, $originalPrice - $this->discount_value);
        }

        $this->save();

        return $this;
    }

    /**
     * Check if bundle is currently active and valid
     */
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return $this->products()->count() > 0;
    }

    /**
     * Increment view count
     */
    public function incrementViewCount(): self
    {
        $this->increment('view_count');
        return $this;
    }

    /**
     * Increment purchase count
     */
    public function incrementPurchaseCount(): self
    {
        $this->increment('purchase_count');
        return $this;
    }

    /**
     * Get products array for cart
     */
    public function getCartItems(): array
    {
        $items = [];

        foreach ($this->products as $product) {
            $quantity = $product->pivot->quantity ?? 1;
            
            // Calculate proportional discount for each product
            $productPrice = $product->unit_price;
            $productTotal = $productPrice * $quantity;
            $discountRatio = $this->original_price > 0 
                ?? $productTotal / $this->original_price 
                : 0;
            $productDiscount = $this->savings_amount * $discountRatio;

            $items[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $productPrice,
                'discount' => $productDiscount / $quantity, // Per unit discount
                'variant' => $product->pivot->variant,
                'color' => $product->pivot->color,
                'bundle_id' => $this->id,
            ];
        }

        return $items;
    }

    /**
     * Generate unique slug
     */
    public static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = \Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        $query = static::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            
            $query = static::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    // ==================== BOOT ====================

    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate slug if not provided
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->name);
            }
        });

        // Clear cache on save/delete
        static::saved(function ($model) {
            cacheRemoveByType(type: 'bundles');
        });

        static::deleted(function ($model) {
            cacheRemoveByType(type: 'bundles');
        });

        // Global scope for translations
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                if (strpos(url()->current(), '/api')) {
                    return $query->where('locale', App::getLocale());
                } else {
                    return $query->where('locale', getDefaultLanguage());
                }
            }]);
        });

        // Handle storage type updates
        static::saved(function ($model) {
            $storage = config('filesystems.disks.default') ?? 'public';
            
            if ($model->isDirty('banner_image')) {
                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id' => $model->id,
                    'key' => 'banner_image',
                ], [
                    'value' => $storage,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($model->isDirty('thumbnail')) {
                DB::table('storages')->updateOrInsert([
                    'data_type' => get_class($model),
                    'data_id' => $model->id,
                    'key' => 'thumbnail',
                ], [
                    'value' => $storage,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
