<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\BeverageManagement\Entities\CollectionCenter;
use Workdo\BeverageManagement\Events\CreateRawMaterial;
use Workdo\Webhook\Entities\SendWebhook;

class CreateRawMaterialLis
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
    public function handle(CreateRawMaterial $event)
    {
        if (module_is_active('Webhook')) {
            $raw_material = $event->raw_material;

            $collection_center = CollectionCenter::find($raw_material->collection_center_id);

            $web_array = [
                'Collection Center Location' => $collection_center->location_name,
                'Raw Material Title' => $raw_material->name,
                'Description' => $raw_material->description,
                'Price' => $raw_material->price,
                'Tax' => $raw_material->tax,
                'Quantity' => $raw_material->quantity,
                'Unit' => $raw_material->unit,
                'Status' => $raw_material->status == 1 ? 'Active' : 'Inactive',
            ];

            $action = 'New Raw Material';
            $module = 'BeverageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
