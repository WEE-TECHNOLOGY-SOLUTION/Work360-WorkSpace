<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GarageManagement\Events\CreateFuelType;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFuelTypeLis
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
    public function handle(CreateFuelType $event)
    {
        if (module_is_active('Webhook')) {
            $fuelType = $event->fuelType;

            $web_array = [
                'Fule Type' => $fuelType->name
            ];

            $action = 'New Fule Type';
            $module = 'GarageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
