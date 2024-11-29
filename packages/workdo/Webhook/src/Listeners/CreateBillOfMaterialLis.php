<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\BeverageManagement\Events\CreateBillOfMaterial;
use Workdo\Webhook\Entities\SendWebhook;

class CreateBillOfMaterialLis
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
    public function handle(CreateBillOfMaterial $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $bill_of_material = $event->bill_of_material;

            $rawItems = json_decode($request['raw_item_array'], true);

            $web_array = [
                'Product Name' => $bill_of_material->product_name,
                'Unit' => $bill_of_material->unit,
                'Quantity' => $request->quantity,
                'Raw Material' => $rawItems,
            ];

            $action = 'New Bill of Material';
            $module = 'BeverageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
