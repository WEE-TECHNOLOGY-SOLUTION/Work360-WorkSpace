<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Events\CreateAgriculturefleet;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgriculturefleetLis
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
    public function handle(CreateAgriculturefleet $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agriculture_fleet = $event->agriculture_fleet;

            $web_array = [
                "Fleet Title" => $agriculture_fleet->name,
                "Fleet Capacity" => $agriculture_fleet->capacity,
                "Fleet Price" => $agriculture_fleet->price,
                "Fleet Quantity" => $agriculture_fleet->quantity,
            ];

            $action = 'New Agriculture Fleet';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
