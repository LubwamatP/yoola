<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActiveSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    /**
     * Handle heartbeat from client - updates session presence
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $sessionId = session()->getId();
        $userId = auth('customer')->id();
        $productId = $request->input('product_id');
        $currentPage = $request->input('page');

        ActiveSession::updateOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => $userId,
                'current_product_id' => $productId,
                'current_page' => $currentPage,
                'device_type' => ActiveSession::detectDeviceType(),
                'browser' => ActiveSession::detectBrowser(),
                'ip_address' => $request->ip(),
                'referrer' => $request->header('referer'),
                'last_activity' => now(),
                'session_started' => now(),
            ]
        );

        return response()->json(['status' => 'ok', 'timestamp' => now()->timestamp]);
    }

    /**
     * Mark session as leaving (user navigating away or closing tab)
     */
    public function leave(Request $request): JsonResponse
    {
        $sessionId = session()->getId();

        ActiveSession::where('session_id', $sessionId)->delete();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Get active viewers for a specific product (for "X people viewing this" feature)
     */
    public function getProductViewers(Request $request): JsonResponse
    {
        $productId = $request->input('product_id');

        if (!$productId) {
            return response()->json(['error' => 'Product ID required'], 400);
        }

        $currentSessionId = session()->getId();

        // Count active viewers excluding current session (within 30 seconds)
        $viewerCount = ActiveSession::active(30)
            ->viewingProduct($productId)
            ->where('session_id', '!=', $currentSessionId)
            ->count();

        return response()->json([
            'product_id' => $productId,
            'viewer_count' => $viewerCount,
        ]);
    }
}
