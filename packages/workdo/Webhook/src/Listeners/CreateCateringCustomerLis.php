<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CateringManagement\Entities\CateringEvents;
use Workdo\CateringManagement\Events\CreateCateringCustomer;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCateringCustomerLis
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
    public function handle(CreateCateringCustomer $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $CateringCustomer = $event->CateringCustomer;

            $event_type = CateringEvents::find($CateringCustomer->event_type);

            $web_array = [
                'Customer Name' => $CateringCustomer->name,
                'Customer Email' => $CateringCustomer->email,
                'Customer Phone Number' => $CateringCustomer->phone_no,
                'Comapany Name' => $CateringCustomer->comapny_name,
                'Event Type' => $event_type->name,
                'Event Date' => $CateringCustomer->event_date,
                'Number of Guests' => $CateringCustomer->number_of_guests,
                'Billing Address' => $CateringCustomer->billing_address,
                'preferences' => $CateringCustomer->preferences,
            ];

            $action = 'New Customer';
            $module = 'CateringManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
