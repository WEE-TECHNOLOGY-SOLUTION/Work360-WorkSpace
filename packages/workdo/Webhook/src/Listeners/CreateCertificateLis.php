<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MovieShowBookingSystem\Events\CreateCertificate;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCertificateLis
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CreateCertificate $event)
    {
        if (module_is_active('Webhook')) {
            $certificate = $event->certificate;

            $web_array = [
                'Certificate Type' => $certificate->name
            ];

            $action = 'New Certificate';
            $module = 'MovieShowBookingSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
