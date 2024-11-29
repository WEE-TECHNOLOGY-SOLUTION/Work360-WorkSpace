<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LaundryManagement\Events\LaundryServicesCreate;
use Workdo\Webhook\Entities\SendWebhook;

class LaundryServicesCreateLis
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
    public function handle(LaundryServicesCreate $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $service = $event->service;

            $web_array = [
                'Service Title' => $service->name,
                'Service Cost' => $service->cost,
            ];

            $action = 'New Laundry Service';
            $module = 'LaundryManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
