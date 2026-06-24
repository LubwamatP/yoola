<?php

namespace App\Listeners;

use App\Events\CustomerRegistrationEvent;
use App\Services\EnchargeService;
use App\Traits\EmailTemplateTrait;

class CustomerRegistrationListener
{
    use EmailTemplateTrait;

    public function __construct() {}

    public function handle(CustomerRegistrationEvent $event): void
    {
        // Send registration email (existing)
        $this->sendMail($event);

        // Encharge: sync new subscriber to email automation
        try {
            if ($event->email) {
                $data = $event->data ?? [];
                app(EnchargeService::class)->upsertSubscriber([
                    'email'     => $event->email,
                    'firstName' => $data['customer_name'] ?? '',
                    'source'    => 'yoola_registration',
                    'country'   => 'Uganda',
                    'city'      => 'Kampala',
                ]);
                app(EnchargeService::class)->trackEvent(
                    $event->email,
                    'Customer Registered',
                    ['registration_date' => now()->toDateString()]
                );
            }
        } catch (\Exception $e) {
            \Log::warning('Encharge registration sync failed: ' . $e->getMessage());
        }
    }

    private function sendMail(CustomerRegistrationEvent $event): void
    {
        $email = $event->email;
        $data  = $event->data;
        $this->sendingMail(
            sendMailTo: $email,
            userType: $data['userType'],
            templateName: $data['templateName'],
            data: $data
        );
    }
}
