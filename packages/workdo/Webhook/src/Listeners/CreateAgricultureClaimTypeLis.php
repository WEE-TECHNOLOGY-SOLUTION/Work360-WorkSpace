<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Events\CreateAgricultureClaimType;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureClaimTypeLis
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
    public function handle(CreateAgricultureClaimType $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $claimtype = $event->claimtype;

            $web_array = [
                'Agriculture Equipment Claim Type' => $claimtype->name,
            ];

            $action = 'New Agriculture Claim Type';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
