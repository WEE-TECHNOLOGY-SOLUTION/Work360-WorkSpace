<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Fleet\Events\CreateFleetCustomer;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFleetCustomerLis
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
    public function handle(CreateFleetCustomer $event)
    {
        if (module_is_active('Webhook')) {
            $customers = $event->customers;

            $web_array = [
                'Customer Name' => $customers->name,
                'Customer Email' => $customers->email,
                'Customer Phone Number' => $customers->phone,
                'Customer Address' => $customers->address
            ];

            $action = 'New Customer';
            $module = 'Fleet';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
