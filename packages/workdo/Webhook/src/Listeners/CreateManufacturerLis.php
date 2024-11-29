<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FixEquipment\Events\CreateManufacturer;
use Workdo\Webhook\Entities\SendWebhook;

class CreateManufacturerLis
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
    public function handle(CreateManufacturer $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $manufacturer = $event->manufacturer;

            $web_array = [
                'Manufacturer Title' => $manufacturer->title
            ];

            $action = 'New Manufacturer';
            $module = 'FixEquipment';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
