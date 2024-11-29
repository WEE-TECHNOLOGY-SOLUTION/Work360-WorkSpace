<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\TourTravelManagement\Entities\Tour;
use Workdo\TourTravelManagement\Entities\TourInquiry;
use Workdo\TourTravelManagement\Events\CreateTourBooking;
use Workdo\Webhook\Entities\SendWebhook;

class CreateTourBookingLis
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
    public function handle(CreateTourBooking $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $tour_booking = $event->tour_booking;

            $tour_data = Tour::find($tour_booking->tour_id);
            $inquiry_data = TourInquiry::find($tour_booking->inquiry_id);

            $web_array = [
                'Tour Title' => $tour_data->tour_name,
                'Inquiry Person Name' => $inquiry_data->person_name,
                'Inquiry Person Email' => $inquiry_data->email_id,
                'Inquiry Person Mobile Number' => $inquiry_data->mobile_no,
                'Inquiry Date' => $inquiry_data->inquiry_date,
            ];

            $action = 'New Tour Booking';
            $module = 'TourTravelManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
