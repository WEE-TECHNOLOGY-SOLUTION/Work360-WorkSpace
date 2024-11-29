<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\TourTravelManagement\Entities\Tour;
use Workdo\TourTravelManagement\Events\CreateTourInquiry;
use Workdo\Webhook\Entities\SendWebhook;

class CreateTourInquiryLis
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
    public function handle(CreateTourInquiry $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $tour = $event->tour;

            $tour_data = Tour::find($tour->tour_id);

            $web_array = [
                'Tour Title' => $tour_data->tour_name,
                'Person Name' => $tour->person_name,
                'Person Email' => $tour->email_id,
                'Person Mobile Number' => $tour->mobile_no,
                'Person Address' => $tour->address,
                'Tour Inquiry Date' => $tour->inquiry_date,
                'Tour Start Date' => $tour->tour_start_date,
                'Tour End Date' => $tour->tour_end_date,
                'Total Number of Person' => $tour->no_of_person,
                'Total Number of Adults' => $tour->no_of_adult,
                'Total Number of Child' => $tour->no_of_child,
                'Minimum Budget' => $tour->budget_minimum,
                'Maximum Budget' => $tour->budget_maximum,
                'Tour Destination' => $tour->tour_destination,
                'Tour Country' => $tour->country,
                'Total Number of Days' => $tour->no_of_days,
                'Total Number of Nights' => $tour->no_of_nights,
                'Tour Payment Status' => $tour->payment_status
            ];

            $action = 'New Tour Inquiry';
            $module = 'TourTravelManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
