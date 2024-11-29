<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GarageManagement\Events\CreateVehicleColor;
use Workdo\Webhook\Entities\SendWebhook;

class CreateVehicleColorLis
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
    public function handle(CreateVehicleColor $event)
    {
        if (module_is_active('Webhook')) {
            $vehicleColor = $event->vehicleColor;

            $web_array = [
                'Vehicle Color Title' => $vehicleColor->name,
            ];

            $action = 'New Vehicle Color';
            $module = 'GarageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
