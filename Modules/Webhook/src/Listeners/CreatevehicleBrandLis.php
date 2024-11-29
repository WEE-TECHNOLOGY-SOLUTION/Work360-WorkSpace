<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GarageManagement\Events\CreatevehicleBrand;
use Workdo\Webhook\Entities\SendWebhook;

class CreatevehicleBrandLis
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
    public function handle(CreatevehicleBrand $event)
    {
        if (module_is_active('Webhook')) {
            $vehicleBrand = $event->vehicleBrand;

            $web_array = [
                'Vehicle Brand Title' => $vehicleBrand->name
            ];

            $action = 'New Vehicle Brand';
            $module = 'GarageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
