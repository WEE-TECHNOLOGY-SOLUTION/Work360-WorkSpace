<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CourierManagement\Events\Courierservicetypecreate;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCourierServiceType
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
    public function handle(Courierservicetypecreate $event)
    {
        if (module_is_active('Webhook')) {
            $serviceType = $event->serviceType;

            $web_array = [
                'Courier Service Type' => $serviceType->service_type,
            ];

            $action = 'New Courier Service Type';
            $module = 'CourierManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
