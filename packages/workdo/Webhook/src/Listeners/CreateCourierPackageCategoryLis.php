<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CourierManagement\Events\Courierpackagecategorycreate;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCourierPackageCategoryLis
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
    public function handle(Courierpackagecategorycreate $event)
    {
        if (module_is_active('Webhook')) {
            $packageCategory = $event->packageCategory;

            $web_array = [
                'Package Category' => $packageCategory->category
            ];

            $action = 'New Courier Package Category';
            $module = 'CourierManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
