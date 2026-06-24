<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductView extends Model
{
    protected $table = 'product_views';

    protected $fillable = [
        'product_id',
        'user_id',
        'session_id',
        'viewed_at',
        'duration_seconds',
        'device_type',
        'referrer',
        'source',
        'added_to_cart',
        'purchased',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'added_to_cart' => 'boolean',
        'purchased' => 'boolean',
        'duration_seconds' => 'integer',
    ];

    /**
     * Product relationship
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for views within last N hours
     */
    public function scopeLastHours($query, int $hours = 24)
    {
        return $query->where('viewed_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope for views within last N days
     */
    public function scopeLastDays($query, int $days = 7)
    {
        return $query->where('viewed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for a specific product
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope for a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for a specific session
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope for mobile devices
     */
    public function scopeMobile($query)
    {
        return $query->where('device_type', 'mobile');
    }

    /**
     * Scope for views that led to cart addition
     */
    public function scopeAddedToCart($query)
    {
        return $query->where('added_to_cart', true);
    }

    /**
     * Scope for views that led to purchase
     */
    public function scopePurchased($query)
    {
        return $query->where('purchased', true);
    }

    /**
     * Get device type from user agent
     */
    public static function detectDeviceType(?string $userAgent = null): string
    {
        $userAgent = $userAgent ?? request()->userAgent() ?? '';

        if (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini|iemobile/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Get source from referrer
     */
    public static function detectSource(?string $referrer = null): string
    {
        $referrer = $referrer ?? request()->header('referer') ?? '';
        $currentHost = request()->getHost();

        if (empty($referrer)) {
            return 'direct';
        }

        $referrerHost = parse_url($referrer, PHP_URL_HOST);

        if ($referrerHost !== $currentHost) {
            return 'external';
        }

        if (str_contains($referrer, '/search')) {
            return 'search';
        }

        if (str_contains($referrer, '/category') || str_contains($referrer, '/categories')) {
            return 'category';
        }

        if (str_contains($referrer, '/shop') || str_contains($referrer, '/vendor')) {
            return 'shop';
        }

        if ($referrer === url('/') || str_ends_with($referrer, '/')) {
            return 'homepage';
        }

        return 'internal';
    }
}
