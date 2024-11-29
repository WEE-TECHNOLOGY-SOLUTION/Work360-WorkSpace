<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FixEquipment\Entities\EquipmentCategory;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Entities\Manufacturer;
use Workdo\FixEquipment\Events\CreateAccessories;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAccessoriesLis
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
    public function handle(CreateAccessories $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $accessories = $event->accessories;

            $asset = FixAsset::find($accessories->asset);
            $category = EquipmentCategory::find($accessories->category);
            $manufacturer = Manufacturer::find($accessories->manufacturer);
            $supplier = User::find($accessories->supplier);

            $web_array = [
                'Asset Title' => $asset->title,
                'Asset Purchase Date' => $asset->purchase_date,
                'Accessories Title' => $accessories->title,
                'Accessories Category' => $category->title,
                'Accessories Manufacturer' => $manufacturer->title,
                'Accessories Price' => $accessories->price,
                'Accessories Quantity' => $accessories->quantity,
                'Supplier Name' => $supplier->name,
                'Supplier Email' => $supplier->email,
                'Supplier Mobile Number' => $supplier->mobile_no,
            ];

            $action = 'New Accessories';
            $module = 'FixEquipment';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
