<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Events\CreateAgricultureEquipment;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureEquipmentLis
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
    public function handle(CreateAgricultureEquipment $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agriclutureequipment = $event->agriclutureequipment;

            $web_array = [
                'Agriculture Equipment Title' => $agriclutureequipment->name,
                'Agriculture Equipment Price' => $agriclutureequipment->price,
                'Agriculture Equipment Quantity' => $agriclutureequipment->quantity,
            ];

            $action = 'New Agriculture Equipment';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
