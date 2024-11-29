<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FixEquipment\Entities\EquipmentCategory;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Entities\Manufacturer;
use Workdo\FixEquipment\Events\CreateConsumables;
use Workdo\FixEquipment\Http\Controllers\CategoryController;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFixEquipmentConsumablesLis
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
    public function handle(CreateConsumables $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $consumables = $event->consumables;

            $asset = FixAsset::find($consumables->asset);
            $category = EquipmentCategory::find($consumables->category);
            $manufacturer = Manufacturer::find($consumables->manufacturer);

            $web_array = [
                'Consumables Title' => $consumables->title,
                'Consumables Purchase Date' => $consumables->date,
                'Consumables Price' => $consumables->price,
                'Consumables Quantity' => $consumables->quantity,
                'Consumables Manufacturer' => $manufacturer->title,
                'Asset Title' => $asset->title,
                'Asset Purchase Date' => $asset->purchase_date,
                'Asset Category' => $category->title,
            ];

            $action = 'New Consumables';
            $module = 'FixEquipment';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
