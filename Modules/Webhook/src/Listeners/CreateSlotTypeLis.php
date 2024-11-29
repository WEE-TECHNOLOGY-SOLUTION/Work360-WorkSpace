<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ParkingManagement\Events\CreateSlotType;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSlotTypeLis
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
    public function handle(CreateSlotType $event)
    {
        if (module_is_active('Webhook')) {
            $slot_type = $event->slot_type;

            $web_array = [
                'Slot Type Title' => $slot_type->name,
                'Slot Type Location' => $slot_type->location,
            ];

            $action = 'New Slot Type';
            $module = 'ParkingManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
