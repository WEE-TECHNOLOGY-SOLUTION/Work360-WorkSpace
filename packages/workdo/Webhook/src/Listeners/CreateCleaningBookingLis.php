<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CleaningManagement\Events\CreateCleaningBooking;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCleaningBookingLis
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
    public function handle(CreateCleaningBooking $event)
    {
        if (module_is_active(('Webhook'))) {
            $request = $event->request;
            $booking = $event->booking;

            $web_array = [
                'Customer Name' => $booking->customer_name,
                'Building Type' => $booking->building_type,
                'Customer Address' => $booking->address,
                'Description' => $booking->description,
                'Booking Date' => $booking->booking_date,
                'Cleaning Date' => $booking->cleaning_date,
                'Status' => $booking->status == 0 ? 'Requested' : '',
            ];

            $action = 'New Cleaning Team';
            $module = 'CleaningManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
