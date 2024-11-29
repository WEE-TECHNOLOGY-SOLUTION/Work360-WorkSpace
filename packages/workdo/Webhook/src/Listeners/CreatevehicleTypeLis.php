<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GarageManagement\Events\CreatevehicleType;
use Workdo\Webhook\Entities\SendWebhook;

class CreatevehicleTypeLis
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
    public function handle(CreatevehicleType $event)
    {
        if (module_is_active('Webhook')) {
            $vehicleType = $event->vehicleType;

            $web_array = [
                'Vehicle Type Title' => $vehicleType->name,
            ];

            $action = 'New Vehicle Type';
            $module = 'GarageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
