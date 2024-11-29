<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MobileServiceManagement\Events\MobileServiceRequestTrackingStatusCreate;
use Workdo\Webhook\Entities\SendWebhook;

class MobileServiceRequestTrackingStatusCreateLis
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
    public function handle(MobileServiceRequestTrackingStatusCreate $event)
    {
        if (module_is_active('Webhook')) {
            $trackingStatus = $event->trackingStatus;

            $web_array = [
                'Status Title' => $trackingStatus->status_name,
            ];

            $action = 'New Mobile Service Traking Status';
            $module = 'MobileServiceManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
