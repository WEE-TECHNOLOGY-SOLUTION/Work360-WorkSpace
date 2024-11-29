<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ChildcareManagement\Events\CreateNutrition;
use Workdo\Webhook\Entities\SendWebhook;

class CreateNutritionLis
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
    public function handle(CreateNutrition $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $nutrition = $event->nutrition;

            $web_array = [
                'Name' => $nutrition->name,
                'Food' => json_decode($nutrition->food_name)
            ];

            $action = 'New Nutrition';
            $module = 'ChildcareManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
