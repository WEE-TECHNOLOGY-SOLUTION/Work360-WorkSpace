<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ConsignmentManagement\Events\CreateProduct;
use Workdo\Webhook\Entities\SendWebhook;

class CreateConsignmentProduct
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
    public function handle(CreateProduct $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $product = $event->product;

            $web_array = [
                'Product Title' => $product->name,
                'Product Weight' => $product->weight,
                'Product Unit Price' => $product->unit_price,
                'Product Quantity' => $product->quantity,
                'Product Description' => $product->description
            ];

            $action = 'New Product';
            $module = 'ConsignmentManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
