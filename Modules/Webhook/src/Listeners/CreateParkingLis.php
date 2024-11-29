<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ParkingManagement\Entities\Slot;
use Workdo\ParkingManagement\Entities\SlotType;
use Workdo\ParkingManagement\Events\CreateParking;
use Workdo\Webhook\Entities\SendWebhook;

class CreateParkingLis
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
    public function handle(CreateParking $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $parking = $event->parking;

            $slot = Slot::find($parking->slot_id);
            $slot_type = SlotType::find($parking->slot_type_id);

            $web_array = [
                'Slot' => $slot->name,
                'Slot Type' => $slot_type->name,
                'Name' => $parking->name,
                'Email' => $parking->email,
                'Mobile' => $parking->mobile,
                'Vehicle' => $parking->vehicle,
                'Vehicle Number' => $parking->vehicle_number,
                'Check In Time' => $parking->check_in,
                'Block of Slote' => $parking->block_of_slot,
            ];

            $action = 'New Parking';
            $module = 'ParkingManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
