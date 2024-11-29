<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FixEquipment\Entities\EquipmentCategory;
use Workdo\FixEquipment\Entities\FixAsset;
use Workdo\FixEquipment\Events\CreateLicence;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFixEquipmentLicence
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
    public function handle(CreateLicence $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $license = $event->license;

            $asset = FixAsset::find($license->asset);
            $category = EquipmentCategory::find($license->category);

            $web_array = [
                'Asset Title' => $asset->title,
                'Asset Purchase Date' => $asset->purchase_date,
                'License Title' => $license->title,
                'License Category' => $category->title,
                'License Number' => $request->license_number,
                'License Purchase Date' => $request->purchase_date,
                'License Expire Date' => $request->expire_date,
                'License Purchase Price' => $request->purchase_price,
            ];

            $action = 'New Licence';
            $module = 'FixEquipment';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
