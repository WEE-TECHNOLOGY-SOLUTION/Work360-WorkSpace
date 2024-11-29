<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Events\CreateAgricultureServiceProduct;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureServiceProductLis
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
    public function handle(CreateAgricultureServiceProduct $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $serviceproduct = $event->serviceproduct;

            $web_array = [
                'Agriculture Service Product' => $serviceproduct->name,
                'Service Product Purchase Price' => $serviceproduct->purchase_price,
                'Service Product Sale Price' => $serviceproduct->sales_price,
            ];

            $action = 'New Agriculture Service Product';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
