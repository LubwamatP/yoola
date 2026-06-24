<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AIOperationsController extends Controller
{
    private function callGemini(string $prompt): string
    {
        $key = env('GEMINI_API_KEY', '');
        if (!$key) return 'Gemini API key not configured in .env';
        try {
            $response = Http::timeout(20)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$key}",
                ['contents' => [['parts' => [['text' => $prompt]]]]]
            );
            return $response->json('candidates.0.content.parts.0.text') ?? 'No response from Gemini.';
        } catch (\Exception $e) {
            return 'AI Error: ' . $e->getMessage();
        }
    }

    private function getStats(): array
    {
        return Cache::remember('ai_ops_stats', 300, function () {
            try {
                $totalProducts  = DB::table('products')->where('status', 1)->count();
                $missingMeta    = DB::table('products')->where('status', 1)
                                   ->where(function($q){ $q->whereNull('meta_description')->orWhere('meta_description',''); })
                                   ->count();
                $missingImg     = DB::table('products')->where('status', 1)
                                   ->where(function($q){ $q->whereNull('thumbnail')->orWhere('thumbnail',''); })
                                   ->count();
                $totalOrders    = DB::table('orders')->count();
                $recentOrders   = DB::table('orders')->where('created_at', '>=', now()->subDays(7))->count();
                $totalCustomers = DB::table('users')->where('user_type','customer')->count();
                return compact('totalProducts','missingMeta','missingImg','totalOrders','recentOrders','totalCustomers');
            } catch(\Exception $e) {
                return ['totalProducts'=>0,'missingMeta'=>0,'missingImg'=>0,'totalOrders'=>0,'recentOrders'=>0,'totalCustomers'=>0];
            }
        });
    }

    /** DASHBOARD */
    public function dashboard()
    {
        $stats = $this->getStats();
        return view('admin.ai-operations.dashboard', compact('stats'));
    }

    /** SMART NOTIFICATIONS */
    public function notifications()
    {
        $stats = $this->getStats();
        $insights = Cache::remember('ai_smart_notifications', 3600, function() use ($stats) {
            $prompt = "You are Yoola Alpha, AI marketing engine for Yoola.ug — Uganda's top electronics e-commerce store in Kampala. Analyze this data and generate 6 smart actionable business notifications for the store owner. Focus on: SEO gaps, sales opportunities, inventory alerts, and Uganda market strategy. Use UGX prices. Data: Active products: {$stats['totalProducts']}, Missing SEO meta: {$stats['missingMeta']}, Missing images: {$stats['missingImg']}, Total orders: {$stats['totalOrders']}, Orders last 7 days: {$stats['recentOrders']}, Total customers: {$stats['totalCustomers']}. Return ONLY a valid JSON array: [{\"type\":\"warning\",\"title\":\"Title\",\"message\":\"Actionable detail\",\"priority\":1}]. Types: warning, info, success, danger.";
            $raw = $this->callGemini($prompt);
            $raw = preg_replace('/```json|```/','', $raw);
            $raw = trim(preg_replace('/^[^[]+/', '', $raw));
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        });
        return view('admin.ai-operations.notifications', compact('stats', 'insights'));
    }

    /** REFRESH NOTIFICATIONS */
    public function triggerNotification(Request $request)
    {
        Cache::forget('ai_smart_notifications');
        Cache::forget('ai_ops_stats');
        return redirect()->route('admin.ai-operations.notifications')->with('success', 'AI Notifications refreshed!');
    }

    /** CONVERSATIONS LIST */
    public function conversations()
    {
        return view('admin.ai-operations.conversations');
    }

    /** GET CONVERSATION MESSAGES */
    public function getConversationMessages($id)
    {
        return response()->json(['messages' => [], 'id' => $id]);
    }

    /** TAKEOVER CONVERSATION */
    public function takeoverConversation($id)
    {
        return response()->json(['status' => 'ok', 'message' => 'Conversation taken over']);
    }

    /** HAND BACK CONVERSATION */
    public function handbackConversation($id)
    {
        return response()->json(['status' => 'ok', 'message' => 'Handed back to AI']);
    }

    /** SEND MESSAGE IN CONVERSATION */
    public function sendMessage(Request $request, $id)
    {
        $response = $this->callGemini($request->input('message', ''));
        return response()->json(['response' => $response]);
    }

    /** AI CHAT (from conversations page) */
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:2000']);
        $context = "You are Yoola Alpha, the AI marketing brain for Yoola.ug — Uganda's top electronics e-commerce store in Kampala. Help with: SEO strategy, product descriptions, social media content, pricing, competitor analysis, organic traffic growth. Give practical Uganda-specific advice. Use UGX for prices. Be direct and actionable.";
        $response = $this->callGemini($context . "\n\nUser: " . $request->message . "\n\nYoola Alpha:");
        return response()->json(['response' => $response]);
    }

    /** LEADS */
    public function leads()
    {
        try {
            $leads = DB::table('power_calculator_leads')->orderByDesc('created_at')->paginate(20);
        } catch(\Exception $e) {
            $leads = collect([]);
        }
        return view('admin.ai-operations.leads', compact('leads'));
    }

    public function markLeadContacted($id)
    {
        try { DB::table('power_calculator_leads')->where('id', $id)->update(['contacted' => 1, 'updated_at' => now()]); } catch(\Exception $e) {}
        return redirect()->back()->with('success', 'Lead marked as contacted');
    }

    public function exportLeads()
    {
        try {
            $leads = DB::table('power_calculator_leads')->get();
            $csv = "Name,Email,Phone,Monthly Units,Created\n";
            foreach($leads as $l) {
                $csv .= implode(',', [$l->name??'', $l->email??'', $l->phone??'', $l->monthly_units??'', $l->created_at??'']) . "\n";
            }
            return response($csv, 200, ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment;filename=yoola-leads.csv']);
        } catch(\Exception $e) {
            return redirect()->back()->with('error', 'Export failed');
        }
    }

    /** SETTINGS */
    public function settings()
    {
        $geminiKey = env('GEMINI_API_KEY', '');
        return view('admin.ai-operations.settings', compact('geminiKey'));
    }
}
