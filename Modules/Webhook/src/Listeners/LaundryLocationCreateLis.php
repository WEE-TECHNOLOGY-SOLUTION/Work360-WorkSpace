<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LaundryManagement\Events\LaundryLocationCreate;
use Workdo\Webhook\Entities\SendWebhook;

class LaundryLocationCreateLis
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
    public function handle(LaundryLocationCreate $event)
    {
        if (module_is_active('Webhook')) {
            $location = $event->location;

            $web_array = [
                'Location' => $location->name,
                'Cost' => $location->cost,
            ];

            $action = 'New Laundry Location';
            $module = 'LaundryManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
