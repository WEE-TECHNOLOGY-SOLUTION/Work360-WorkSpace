<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ProductService\Entities\Category;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\WordpressWoocommerce\Events\CreateWoocommerceCategory;

class CreateWoocommerceCategoryLis
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
    public function handle(CreateWoocommerceCategory $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $category = $event->Category;

            $category_data = Category::find($category->original_id);

            $new_category = [
                'Title' => $category_data->name,
                "Color" => $category_data->color
            ];
            $action = 'New Product Category';
            $module = 'WordpressWoocommerce';
            SendWebhook::SendWebhookCall($module, $new_category, $action);
        }
    }
}