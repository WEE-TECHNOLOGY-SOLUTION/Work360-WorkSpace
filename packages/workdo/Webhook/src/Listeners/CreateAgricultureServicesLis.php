<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Entities\AgricultureActivities;
use Workdo\AgricultureManagement\Events\CreateAgricultureServices;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureServicesLis
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
    public function handle(CreateAgricultureServices $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agricultureservice = $event->agricultureservice;

            $activity = AgricultureActivities::find($agricultureservice->activity);

            $web_array = [
                'Agriculture Services Title' => $agricultureservice->name,
                'Agriculture Activity Title' => $activity->name,
                'Quantity' => $agricultureservice->qty,
                'UTM' => $agricultureservice->utm,
                'Unit Price' => $agricultureservice->unit_price,
                'Total Price' => $agricultureservice->total_price,
            ];

            $action = 'New Agriculture Services';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
