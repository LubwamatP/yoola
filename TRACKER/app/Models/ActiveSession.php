<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ActiveSession extends Model
{
    protected $table = 'active_sessions';

    protected $fillable = [
        'session_id',
        'user_id',
        'current_product_id',
        'current_page',
        'device_type',
        'browser',
        'ip_address',
        'country',
        'referrer',
        'last_activity',
        'session_started',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'session_started' => 'datetime',
    ];

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Current product being viewed
     */
    public function currentProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'current_product_id');
    }

    /**
     * Scope for active sessions (within timeout period)
     */
    public function scopeActive($query, int $timeoutSeconds = 30)
    {
        return $query->where('last_activity', '>=', Carbon::now()->subSeconds($timeoutSeconds));
    }

    /**
     * Scope for sessions viewing a specific product
     */
    public function scopeViewingProduct($query, int $productId)
    {
        return $query->where('current_product_id', $productId);
    }

    /**
     * Scope for sessions on a specific page type
     */
    public function scopeOnPage($query, string $page)
    {
        return $query->where('current_page', $page);
    }

    /**
     * Get count of active users (unique sessions within timeout)
     */
    public static function activeUsersCount(int $timeoutSeconds = 30): int
    {
        return self::active($timeoutSeconds)->count();
    }

    /**
     * Get count of users viewing a specific product right now
     */
    public static function viewingProductCount(int $productId, int $timeoutSeconds = 30): int
    {
        return self::active($timeoutSeconds)->viewingProduct($productId)->count();
    }

    /**
     * Update or create session with heartbeat
     */
    public static function heartbeat(
        string $sessionId,
        ?int $userId = null,
        ?int $productId = null,
        ?string $currentPage = null,
        ?string $deviceType = null,
        ?string $browser = null,
        ?string $ipAddress = null,
        ?string $referrer = null
    ): self {
        return self::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => $userId,
                'current_product_id' => $productId,
                'current_page' => $currentPage,
                'device_type' => $deviceType ?? self::detectDeviceType(),
                'browser' => $browser ?? self::detectBrowser(),
                'ip_address' => $ipAddress ?? request()->ip(),
                'referrer' => $referrer,
                'last_activity' => Carbon::now(),
                'session_started' => Carbon::now(),
            ]
        );
    }

    /**
     * Update existing session's last activity
     */
    public static function updateActivity(
        string $sessionId,
        ?int $productId = null,
        ?string $currentPage = null
    ): bool {
        $session = self::where('session_id', $sessionId)->first();

        if (!$session) {
            return false;
        }

        $data = ['last_activity' => Carbon::now()];

        if ($productId !== null) {
            $data['current_product_id'] = $productId;
        }

        if ($currentPage !== null) {
            $data['current_page'] = $currentPage;
        }

        return $session->update($data);
    }

    /**
     * Remove stale sessions
     */
    public static function cleanupStale(int $timeoutSeconds = 300): int
    {
        return self::where('last_activity', '<', Carbon::now()->subSeconds($timeoutSeconds))->delete();
    }

    /**
     * Detect device type from user agent
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
     * Detect browser from user agent
     */
    public static function detectBrowser(?string $userAgent = null): string
    {
        $userAgent = $userAgent ?? request()->userAgent() ?? '';

        if (preg_match('/edg/i', $userAgent)) {
            return 'Edge';
        }
        if (preg_match('/chrome/i', $userAgent)) {
            return 'Chrome';
        }
        if (preg_match('/safari/i', $userAgent)) {
            return 'Safari';
        }
        if (preg_match('/firefox/i', $userAgent)) {
            return 'Firefox';
        }
        if (preg_match('/opera|opr/i', $userAgent)) {
            return 'Opera';
        }
        if (preg_match('/msie|trident/i', $userAgent)) {
            return 'IE';
        }

        return 'Other';
    }

    /**
     * Get products being viewed right now with viewer counts
     */
    public static function getProductsBeingViewed(int $timeoutSeconds = 30, int $limit = 20): array
    {
        return self::active($timeoutSeconds)
            ->whereNotNull('current_product_id')
            ->selectRaw('current_product_id, COUNT(*) as viewer_count')
            ->groupBy('current_product_id')
            ->orderByDesc('viewer_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
