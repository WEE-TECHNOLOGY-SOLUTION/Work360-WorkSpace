<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FixEquipment\Events\CreateCategory;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFixEquipmentCategory
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
            $request = $event->request;
            $category = $event->category;

            $web_array = [
                'Category Title' => $category->title,
                'Category Type' => $category->category_type
            ];

            $action = 'New Category';
            $module = 'FixEquipment';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
