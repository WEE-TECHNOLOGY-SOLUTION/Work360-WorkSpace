<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CarDealership\Entities\Category;
use Workdo\CarDealership\Events\CreateDealershipProduct;
use Workdo\Webhook\Entities\SendWebhook;

class CreateDealershipProductLis
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
    public function handle(CreateDealershipProduct $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $dealershipProduct = $event->dealershipProduct;

            $category = Category::find($dealershipProduct->category_id);

            $web_array = [
                'Product' => $dealershipProduct->name,
                'SKU' => $dealershipProduct->sku,
                'Category' => $category->name,
                'Description' => $dealershipProduct->description,
                'Quantity' => $dealershipProduct->quantity,
                'Sale Price' => $dealershipProduct->sale_price,
                'Purchase Price' => $dealershipProduct->purchase_price,
            ];

            $action = 'New Dealership Product';
            $module = 'CarDealership';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
