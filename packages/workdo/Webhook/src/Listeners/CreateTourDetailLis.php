<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\TourTravelManagement\Entities\Tour;
use Workdo\TourTravelManagement\Events\CreateTourDetail;
use Workdo\Webhook\Entities\SendWebhook;

class CreateTourDetailLis
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
    public function handle(CreateTourDetail $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $tour = $event->tour;

            $tour_data = Tour::find($tour->tour_id);

            $web_array = [
                'Tour Title' => $tour_data->tour_name,
                'Tour Details' => $request->tour_details,
            ];

            $action = 'New Tour Detail';
            $module = 'TourTravelManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
