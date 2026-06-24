<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Encharge.io Email Automation Service
 * Handles subscriber sync, events, and flow triggers for Yoola.ug
 *
 * Docs: https://app.encharge.io/api-docs
 * Auth: X-Encharge-Token header
 */
class EnchargeService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.encharge.io/v1';

    public function __construct()
    {
        $this->apiKey = env('ENCHARGE_API_KEY', '');
    }

    private function headers(): array
    {
        return [
            'X-Encharge-Token' => $this->apiKey,
            'Content-Type'     => 'application/json',
            'Accept'           => 'application/json',
        ];
    }

    private function post(string $endpoint, array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(10)
                ->post("{$this->baseUrl}{$endpoint}", $data);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            Log::warning("Encharge API error [{$endpoint}]", [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error("Encharge request failed [{$endpoint}]", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function get(string $endpoint, array $params = []): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(10)
                ->get("{$this->baseUrl}{$endpoint}", $params);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Upsert a subscriber (create or update by email)
     * Called on: new customer registration, order placed
     */
    public function upsertSubscriber(array $fields): array
    {
        // Required: email
        // Optional: firstName, lastName, phone, tags[], customFields[]
        return $this->post('/people', ['fields' => $fields]);
    }

    /**
     * Track a custom event for a subscriber
     * Used to trigger Encharge automations/flows
     */
    public function trackEvent(string $email, string $name, array $properties = []): array
    {
        return $this->post('/events', [
            'name'   => $name,
            'email'  => $email,
            'fields' => $properties,
        ]);
    }

    /**
     * Add/remove tags from a subscriber
     */
    public function addTags(string $email, array $tags): array
    {
        return $this->post('/people/tags', [
            'email' => $email,
            'tags'  => $tags,
        ]);
    }

    public function removeTags(string $email, array $tags): array
    {
        return $this->post('/people/tags/delete', [
            'email' => $email,
            'tags'  => $tags,
        ]);
    }

    // ── YOOLA-SPECIFIC TRIGGERS ────────────────────────────────────────────

    /**
     * Called when a new customer registers
     */
    public function onCustomerRegistered(array $customer): array
    {
        $this->upsertSubscriber([
            'email'     => $customer['email'],
            'firstName' => $customer['f_name'] ?? '',
            'lastName'  => $customer['l_name'] ?? '',
            'phone'     => $customer['phone'] ?? '',
            'source'    => 'yoola_registration',
            'city'      => 'Kampala',
            'country'   => 'Uganda',
        ]);

        return $this->trackEvent($customer['email'], 'Customer Registered', [
            'registration_date' => now()->toDateString(),
        ]);
    }

    /**
     * Called when an order is placed
     */
    public function onOrderPlaced(array $order, string $email): array
    {
        $this->upsertSubscriber([
            'email'            => $email,
            'total_orders'     => $order['order_count'] ?? 1,
            'total_spent_ugx'  => $order['total'] ?? 0,
            'last_order_date'  => now()->toDateString(),
        ]);

        return $this->trackEvent($email, 'Order Placed', [
            'order_id'    => $order['id'] ?? '',
            'order_total' => $order['order_amount'] ?? 0,
            'currency'    => 'UGX',
            'items_count' => $order['details_count'] ?? 1,
        ]);
    }

    /**
     * Called when order status changes to "delivered"
     */
    public function onOrderDelivered(string $email, array $order): array
    {
        return $this->trackEvent($email, 'Order Delivered', [
            'order_id'       => $order['id'],
            'delivered_date' => now()->toDateString(),
        ]);
    }

    /**
     * Called when a customer abandons their cart
     */
    public function onCartAbandoned(string $email, array $cartItems): array
    {
        return $this->trackEvent($email, 'Cart Abandoned', [
            'cart_items_count' => count($cartItems),
            'cart_value_ugx'   => collect($cartItems)->sum('price'),
        ]);
    }

    /**
     * Called when a customer uses the Power Calculator
     */
    public function onPowerCalculatorUsed(string $email, array $result): array
    {
        $this->upsertSubscriber([
            'email'                   => $email,
            'calculator_monthly_bill' => $result['monthly_bill_ugx'] ?? 0,
        ]);

        return $this->trackEvent($email, 'Power Calculator Used', [
            'monthly_bill_ugx'    => $result['monthly_bill_ugx'] ?? 0,
            'appliance_count'     => $result['appliance_count'] ?? 0,
            'recommended_product' => $result['top_recommendation'] ?? '',
        ]);
    }

    /**
     * Win-back: tag inactive customers for re-engagement flow
     */
    public function tagInactiveCustomers(int $inactiveDays = 60): int
    {
        // This would be called by a cron/scheduled command
        // Returns count of customers tagged
        $customers = \DB::table('customers')
            ->where('updated_at', '<', now()->subDays($inactiveDays))
            ->whereNotNull('email')
            ->select('email', 'f_name', 'l_name')
            ->limit(200)
            ->get();

        $count = 0;
        foreach ($customers as $customer) {
            $this->addTags($customer->email, ['inactive-' . $inactiveDays . 'd', 'win-back']);
            $count++;
        }

        return $count;
    }

    /**
     * Get account info (health check)
     */
    public function getAccountInfo(): array
    {
        return $this->get('/accounts/me');
    }

    /**
     * Get all segments
     */
    public function getSegments(): array
    {
        return $this->get('/segments');
    }
}
