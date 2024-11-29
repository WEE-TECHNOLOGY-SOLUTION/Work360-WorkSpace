<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FreightManagementSystem\Events\CreateFreightService;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFreightServiceLis
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
    public function handle(CreateFreightService $event)
    {
        if (module_is_active('Webhook')) {
            $service = $event->service;

            $web_array = [
                'Service Title' => $service->name,
                'Service Cost Price' => $service->cost_price,
                'Service Sale Price' => $service->sale_price,
            ];

            $action = 'New Freight Service';
            $module = 'FreightManagementSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
