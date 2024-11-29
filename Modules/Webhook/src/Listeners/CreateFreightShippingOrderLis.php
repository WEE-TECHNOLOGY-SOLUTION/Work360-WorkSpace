<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FreightManagementSystem\Entities\FreightContainer;
use Workdo\FreightManagementSystem\Entities\FreightPrice;
use Workdo\FreightManagementSystem\Events\CreateFreightShippingOrder;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFreightShippingOrderLis
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
    public function handle(CreateFreightShippingOrder $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $shipping = $event->shipping;

            $container = FreightContainer::find($request->container);
            $pricing = FreightPrice::find($request->pricing);


            $web_array = [
                'Customer Name' => $shipping->customer_name,
                'Customer Email' => $shipping->customer_email,
                'Direction' => $shipping->direction,
                'Transport' => $shipping->transport,
                'Loading Port' => $shipping->loading_port,
                'Discharge Port' => $shipping->discharge_port,
                'Vessel' => $shipping->vessel,
                'Date' => $shipping->date,
                'Barcode' => $shipping->barcode,
                'Tracking Number' => $shipping->tracking_no,
                'Container' => $container->name,
                'Price' => $pricing->name,
                'Sale Price' => $request->sale_price,
            ];

            $action = 'Update Freight Shipping Order';
            $module = 'FreightManagementSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
