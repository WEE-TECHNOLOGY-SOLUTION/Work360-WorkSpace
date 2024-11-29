<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\BeverageManagement\Entities\Manufacturing;
use Workdo\BeverageManagement\Events\CreatePackaging;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePackagingLis
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
    public function handle(CreatePackaging $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $packaging = $event->packaging;

            $manufacturer = Manufacturing::find($packaging->manufacturing_id);

            $web_array = [
                'Product Name' => $manufacturer->product_name,
                'Schedule Date' => $manufacturer->schedule_date,
                'Raw Items' => json_decode($request->raw_item_array),
                'Total' => $packaging->total,
            ];

            $action = 'New Packaging';
            $module = 'BeverageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
