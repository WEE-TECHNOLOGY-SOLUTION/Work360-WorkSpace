<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\BeverageManagement\Entities\RawMaterial;
use Workdo\BeverageManagement\Events\CreateBillItemMaterial;
use Workdo\Webhook\Entities\SendWebhook;

class CreateBillItemMaterialLis
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
    public function handle(CreateBillItemMaterial $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $bill_material_item = $event->bill_material_item;

            $product_item = [];

            $raw_items = json_decode($request->input('raw_item_array'), true);

            foreach ($raw_items as &$raw_item) {
                $raw_material_id = $raw_item['raw_material'];
                $raw_material_name = RawMaterial::find($raw_material_id)->name;

                $raw_item['raw_material_name'] = $raw_material_name;
                unset($raw_item['raw_material']);
            }

            $web_array = [
                'Product Name' => $request->product_name,
                'Product Items' => $raw_items
            ];

            $action = 'New Bill Item Material';
            $module = 'BeverageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
