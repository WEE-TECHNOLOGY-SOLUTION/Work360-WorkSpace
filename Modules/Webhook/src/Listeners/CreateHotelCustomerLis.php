<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Holidayz\Events\CreateHotelCustomer;
use Workdo\Webhook\Entities\SendWebhook;

class CreateHotelCustomerLis
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
    public function handle(CreateHotelCustomer $event)
    {
        if (module_is_active('Webhook')) {

            $request = $event->request;
            $customer = $event->customer;

            $web_array = [
                'Customer Name' => $customer->name,
                'Last Name' => $customer->last_name,
                'Email' => $customer->email,
                'Date of Birth' => $customer->dob,
                'Id Number' => $customer->id_number,
                'Company Title' => $customer->company,
                'VAT Number' => $customer->vat_number,
                'Home Phone' => $customer->home_phone,
                'Mobile Phone' => $customer->mobile_phone,
                'Other' => $customer->other
            ];

            $action = 'New Customer';
            $module = 'Holidayz';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
