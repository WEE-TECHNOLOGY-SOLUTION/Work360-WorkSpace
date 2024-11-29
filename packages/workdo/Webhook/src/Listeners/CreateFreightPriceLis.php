<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FreightManagementSystem\Events\CreateFreightPrice;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFreightPriceLis
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
    public function handle(CreateFreightPrice $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $price = $event->price;

            $web_array = [
                'Price Title' => $price->name,
                'Volume Price' => $price->volume_price,
                'Weight Price' => $price->weight_price,
                'Service Price' => $price->service_price
            ];

            $action = 'New Freight Price';
            $module = 'FreightManagementSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
