<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FixEquipment\Entities\Depreciation;
use Workdo\FixEquipment\Entities\EquipmentCategory;
use Workdo\FixEquipment\Entities\EquipmentLocation;
use Workdo\FixEquipment\Entities\Manufacturer;
use Workdo\FixEquipment\Events\CreateAsset;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFixAssetLis
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
    public function handle(CreateAsset $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $asset = $event->asset;

            $location = EquipmentLocation::find($asset->location);
            $user = User::find($asset->supplier);
            $manufacturer = Manufacturer::find($asset->manufacturer);
            $category = EquipmentCategory::find($asset->category);
            $depreciation = Depreciation::find($asset->depreciation_method);

            $web_array = [
                'Asset Title' => $asset->title,
                'Model Title' => $asset->model_name,
                'Serial Number' => $asset->serial_number,
                'Asset Purchase Date' => $asset->purchase_date,
                'Asset Purchase Price' => $asset->purchase_price,
                'Asset Description' => $asset->description,
                'Asset Location' => $location->location_name,
                'Asset Manufacturer' => $manufacturer->title,
                'Asset Category' => $category->title,
                'Asset Depreciation Title' => $depreciation->title,
                'Depreciation Rate' => $depreciation->rate,
                'Asset Supplier' => $user->name,
                'Supplier Email' => $user->email,
                'Supplier Phone Number' => $user->mobile_no,
            ];

            $action = 'New Asset';
            $module = 'FixEquipment';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
