<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FixEquipment\Events\CreateLocation;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFixEquipmentLocationLis
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
    public function handle(CreateLocation $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $location = $event->location;

            $web_array = [
                'Location Title' => $location->location_name,
                'Location Address' => $location->address,
                'Location Description' => $location->location_description,
            ];

            $action = 'New Location';
            $module = 'FixEquipment';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
