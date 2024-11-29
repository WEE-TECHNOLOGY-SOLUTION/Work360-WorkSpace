<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CateringManagement\Entities\CateringCustomer;
use Workdo\CateringManagement\Entities\EventDetail;
use Workdo\CateringManagement\Events\CreateCateringInvoice;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCateringInvoiceLis
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
    public function handle(CreateCateringInvoice $event)
    {
        if (module_is_active('Webhook')) {
            $cateringinvoice = $event->cateringinvoice;

            $event_details = EventDetail::find($cateringinvoice->event_detail_id);
            $customer = CateringCustomer::find($event_details->customer_id);

            $web_array = [
                'Event Location' => $event_details->event_location,
                'Venue Requirements' => $event_details->venue_requirements,
                'Delivery Pickup Point' => $event_details->delivery_pickup_option,
                'Additional Services' => $event_details->additional_services,
                'Service Price' => $event_details->services_price,
                'Customer Name' => $customer->name,
                'Customer Email' => $customer->email,
                'Customer Billing Address' => $customer->billing_address,
                'Event Date' => $customer->event_date,
                'Number of Guest' => $customer->number_of_guests,
                'Invoice Service Price' => $cateringinvoice->service_price,
                'Menu Total' => $cateringinvoice->menu_total,
            ];

            $action = 'New Catering Invoice';
            $module = 'CateringManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
