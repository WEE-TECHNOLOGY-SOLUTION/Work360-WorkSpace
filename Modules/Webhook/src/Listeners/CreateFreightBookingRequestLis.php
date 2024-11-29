<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FreightManagementSystem\Events\CreateFreightBookingRequest;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFreightBookingRequestLis
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
    public function handle(CreateFreightBookingRequest $event)
    {
        if (module_is_active('Webhook')) {
            $booking_request = $event->booking_request;

            $web_array = [
                'Direction' => $booking_request->direction,
                'Transport' => $booking_request->transport,
                'Customer Name' => $booking_request->customer_name,
                'Customer Email' => $booking_request->customer_email,
                'Loading Port' => $booking_request->loading_port,
                'Discharge Port' => $booking_request->discharge_port,
                'Vessel' => $booking_request->vessel,
                'Date' => $booking_request->date,
                'Barcode' => $booking_request->barcode,
                'Tracking Number' => $booking_request->tracking_no
            ];

            $action = 'New Freight Booking Request';
            $module = 'FreightManagementSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
