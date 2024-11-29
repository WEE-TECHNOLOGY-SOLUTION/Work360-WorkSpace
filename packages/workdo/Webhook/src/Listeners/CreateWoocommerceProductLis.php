<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ProductService\Entities\ProductService;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\WordpressWoocommerce\Events\CreateWoocommerceProduct;

class CreateWoocommerceProductLis
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
    public function handle(CreateWoocommerceProduct $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $product = $event->Product;

            $products = ProductService::find($product->original_id);

            $prduct_data = [
                'Product Title' => $products->name,
                'SKU' => $products->sku,
                'Sale Price' => $products->sale_price,
                'Purchase Price' => $products->purchase_price,
                'Product Category' => $product->type,
            ];

            $action = 'New Product';
            $module = 'WordpressWoocommerce';
            SendWebhook::SendWebhookCall($module, $prduct_data, $action);
        }
    }
}
