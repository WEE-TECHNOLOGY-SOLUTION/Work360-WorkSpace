<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FreightManagementSystem\Events\CreateFreightCustomer;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFreightCustomerLis
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
    public function handle(CreateFreightCustomer $event)
    {
        if (module_is_active('Webhook')) {
            $customer = $event->customer;

            $web_array = [
                'Customer Name' => $customer->name,
                'Customer Email' => $customer->email,
                'Country' => $customer->country,
                'State' => $customer->state,
                'City' => $customer->city,
                'Zip' => $customer->zip,
                'Address' => $customer->address,
            ];

            $action = 'New Freight Customer';
            $module = 'FreightManagementSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
