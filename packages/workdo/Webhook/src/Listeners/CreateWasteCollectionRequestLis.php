<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\WasteManagement\Entities\WasteCategory;
use Workdo\WasteManagement\Entities\WasteCategoryType;
use Workdo\WasteManagement\Entities\WasteLocation;
use Workdo\WasteManagement\Entities\WastePickupPoints;
use Workdo\WasteManagement\Entities\WasteTrip;
use Workdo\WasteManagement\Events\WasteCollectionRequestCreate;
use Workdo\Webhook\Entities\SendWebhook;

class CreateWasteCollectionRequestLis
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
    public function handle(WasteCollectionRequestCreate $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $WasteCollection = $event->WasteCollection;

            $pickup_point = WastePickupPoints::find($WasteCollection->pickup_point_id);
            $location = WasteLocation::find($WasteCollection->location_id);
            $category_type = WasteCategoryType::find($WasteCollection->category_type_id);
            $category = WasteCategory::find($WasteCollection->category_id);

            $web_array = [
                'Name' => $WasteCollection->name,
                'Email' => $WasteCollection->email,
                'Phone' => $WasteCollection->phone,
                'Date' => $WasteCollection->date,
                'Request Id' => $WasteCollection->request_id,
                'Location' => $location->name,
                'PickUp Point' => $pickup_point->name,
                'Category' => $category->name,
                'Category Type' => $category_type->name,
                'Status' => WasteTrip::$status[$WasteCollection->status]
            ];

            $action = 'New Collection Request';
            $module = 'WasteManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
