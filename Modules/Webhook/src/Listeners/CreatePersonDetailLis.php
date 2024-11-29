<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\TourTravelManagement\Entities\Tour;
use Workdo\TourTravelManagement\Entities\TourInquiry;
use Workdo\TourTravelManagement\Events\CreatePersonDetail;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePersonDetailLis
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
    public function handle(CreatePersonDetail $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $personal_information = $event->person_information;

            $tour_data = Tour::find($personal_information->tour_id);
            $inquiry_data = TourInquiry::find($personal_information->inquiry_id);

            $web_array = [
                'Tour Title' => $tour_data->tour_name,
                'Tour Inquiry Person' => $inquiry_data->person_name,
                'Person Email' => $inquiry_data->email_id,
                'Personal Details' => $request->person_details
            ];

            $action = 'New Person Detail';
            $module = 'TourTravelManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
