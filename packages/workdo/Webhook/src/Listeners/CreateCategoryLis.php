<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CarDealership\Events\CreateCategory;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCategoryLis
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
    public function handle(CreateCategory $event)
    {
        if (module_is_active('Webhook')) {
            $category = $event->category;

            $web_array = [
                'Car Category Title' => $category->name,
                'Color' => $category->color
            ];

            $action = 'New Car Category';
            $module = 'CarDealership';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
