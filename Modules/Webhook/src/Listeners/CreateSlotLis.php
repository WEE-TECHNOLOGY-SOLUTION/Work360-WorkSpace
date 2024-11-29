<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ParkingManagement\Entities\SlotType;
use Workdo\ParkingManagement\Events\CreateSlot;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSlotLis
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
    public function handle(CreateSlot $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $slot = $event->slot;
            $slot_type = SlotType::find($slot->slot_type);

            $web_array = [
                'Slot Title' => $slot->name,
                'Slote Type Title' => $slot_type->name,
                'Type' => $slot->type == 0 ? 'Public' : 'Private',
                'Number of Slots' => $slot->no_of_slot,
                'Start Time' => $slot->start_time,
                'End Time' => $slot->end_time,
                'Slot Status' => $slot->status == 0 ? 'Active' : 'Inactive',
                'Amount' => $slot->amount,
            ];

            $action = 'New Slot';
            $module = 'ParkingManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
