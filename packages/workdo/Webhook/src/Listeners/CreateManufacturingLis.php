<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\BeverageManagement\Entities\RawMaterial;
use Workdo\BeverageManagement\Events\CreateManufacturing;
use Workdo\Webhook\Entities\SendWebhook;

class CreateManufacturingLis
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
    public function handle(CreateManufacturing $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $manufacturing = $event->manufacturing;

            $raw_material = [];

            for ($i = 0; $i < count($request->raw_quantity); $i++) {
                $raw_material_name = RawMaterial::find($request->raw_material[$i])->name;
                $raw_material[] = [
                    'raw_quantity' => $request->raw_quantity[$i],
                    'raw_material' => $raw_material_name,
                    'raw_unit' => $request->raw_unit[$i],
                ];
            }

            $web_array = [
                'Product Name' => $manufacturing->product_name,
                'Schedule Date' => $manufacturing->schedule_date,
                'Raw Material' => $raw_material,
                'Total Amount' => $manufacturing->total
            ];

            $action = 'New Manufacturing';
            $module = 'BeverageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
