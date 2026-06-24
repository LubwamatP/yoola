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
        'is_bot',
        'bot_name',
        'user_agent',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'session_started' => 'datetime',
        'is_bot' => 'boolean',
    ];

    /**
     * Known bot patterns (50+ patterns)
     */
    public static array $botPatterns = [
        // Search Engine Bots
        'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider', 'yandexbot',
        'sogou', 'exabot', 'facebot', 'ia_archiver',
        // SEO Tools
        'semrush', 'ahrefs', 'moz', 'majestic', 'screaming frog', 'seokicks',
        'sistrix', 'linkdex', 'blexbot', 'dotbot', 'rogerbot',
        // Social Media
        'twitterbot', 'linkedinbot', 'pinterest', 'whatsapp', 'telegrambot',
        'slackbot', 'discordbot', 'facebookexternalhit',
        // Monitoring & Testing
        'uptimerobot', 'pingdom', 'gtmetrix', 'pagespeed', 'lighthouse',
        'webpagetest', 'site24x7', 'statuscake',
        // Generic Bot Indicators
        'bot', 'crawler', 'spider', 'scraper', 'headless', 'phantom',
        'selenium', 'puppeteer', 'playwright', 'wget', 'curl', 'python-requests',
        'go-http-client', 'java', 'perl', 'ruby', 'axios', 'node-fetch',
        // Other Known Bots
        'applebot', 'amazonbot', 'bytespider', 'petalbot', 'seznambot',
        'coccocbot', 'yeti', 'naver', 'daum', 'qwantify',
    ];

    /**
     * Detect if user agent is a bot
     */
    public static function detectBot(?string $userAgent = null): array
    {
        $userAgent = strtolower($userAgent ?? request()->userAgent() ?? '');
        
        foreach (self::$botPatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return ['is_bot' => true, 'bot_name' => ucfirst($pattern)];
            }
        }
        
        return ['is_bot' => false, 'bot_name' => null];
    }

    /**
     * Scope for human sessions only (not bots)
     */
    public function scopeHumans($query)
    {
        return $query->where(function($q) {
            $q->where('is_bot', false)->orWhereNull('is_bot');
        });
    }

    /**
     * Scope for bot sessions only
     */
    public function scopeBots($query)
    {
        return $query->where('is_bot', true);
    }

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
                'device_type' => $deviceType ? self::detectDeviceType(),
                'browser' => $browser ? self::detectBrowser(),
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
