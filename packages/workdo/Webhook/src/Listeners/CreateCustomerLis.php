<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Events\CreateCustomer;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCustomerLis
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
    public function handle(CreateCustomer $event)
    {
        if(module_is_active('Webhook')){
            $customer = $event->customer;
            unset($customer->customer_id,$customer->user_id,$customer->password);
            $action = 'New Customer';
            $module = 'Account';
            SendWebhook::SendWebhookCall($module ,$customer,$action);
        }
    }
}
