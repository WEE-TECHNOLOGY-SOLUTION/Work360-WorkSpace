<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FreightManagementSystem\Entities\FreightContainer;
use Workdo\FreightManagementSystem\Events\UpdateFreightShipping;
use Workdo\Webhook\Entities\SendWebhook;

class UpdateFreightShippingLis
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
    public function handle(UpdateFreightShipping $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $service = $event->service;

            $user = User::find($service->user_id);
            $container = FreightContainer::find($service->container);

            $web_array = [
                'User Name' => $user->name,
                'User Email' => $user->email,
                'Customer Name' => $service->customer_name,
                'Customer Email' => $service->customer_email,
                'Direction' => $service->direction,
                'Transport' => $service->transport,
                'Loading Port' => $service->loading_port,
                'Discharge Port' => $service->discharge_port,
                'Vessel' => $service->vessel,
                'Date' => $service->date,
                'Barcode' => $service->barcode,
                'Tracking Number' => $service->tracking_no,
                'Container' => $container->name,
                'Quantity' => $service->quantity,
                'Volume' => $service->volume
            ];

            $action = 'Update Freight Shipping Container';
            $module = 'FreightManagementSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
