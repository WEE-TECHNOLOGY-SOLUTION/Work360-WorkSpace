<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GarageManagement\Events\CreateGarageCategory;
use Workdo\Webhook\Entities\SendWebhook;

class CreateGarageCategoryLis
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
    public function handle(CreateGarageCategory $event)
    {
        if (module_is_active('Webhook')) {
            $category = $event->category;

            $web_array = [
                'Garage Category' => $category->name
            ];

            $action = 'New Garage Category';
            $module = 'GarageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
