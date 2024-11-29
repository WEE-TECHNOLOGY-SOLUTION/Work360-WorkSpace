<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FreightManagementSystem\Events\CreateFreightContainer;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFreightContainerLis
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
    public function handle(CreateFreightContainer $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $container = $event->container;

            $web_array = [
                'Container Code' => $request->code,
                'Container Number' => $request->container_number,
                'Container Status' => $container->status,
                'Container Name' => $container->name,
                'Container Size' => $container->size,
                'Container Size UOM' => $container->size_uom,
                'Container Volume Price' => $container->volume_price,
            ];

            $action = 'New Freight Container';
            $module = 'FreightManagementSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
