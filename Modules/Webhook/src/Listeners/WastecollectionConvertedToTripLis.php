<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\WasteManagement\Entities\WasteTrip;
use Workdo\WasteManagement\Events\WastecollectionConvertedToTrip;
use Workdo\Webhook\Entities\SendWebhook;

class WastecollectionConvertedToTripLis
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
    public function handle(WastecollectionConvertedToTrip $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $WasteTrip = $event->WasteTrip;
            $WasteCollection = $event->WasteCollection;

            $staff = User::find($WasteTrip->staff_id);

            $web_array = [
                'Waste Collection' => $WasteCollection->name,
                'Email' => $WasteCollection->email,
                'Phone' => $WasteCollection->phone,
                'Date' => $WasteCollection->date,
                'Status' => WasteTrip::$status[$WasteCollection->status],
                'Staff Name' => $staff->name,
                'Staff Email' => $staff->email,
                'Staff Mobile Number' => $staff->mobile_no,
            ];

            $action = 'Collection Converted To Trip';
            $module = 'WasteManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
