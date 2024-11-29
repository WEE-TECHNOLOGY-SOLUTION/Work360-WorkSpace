<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Holidayz\Events\CreateHotel;
use Workdo\Webhook\Entities\SendWebhook;

class CreateHotelLis
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
    public function handle($event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $hotel = $event->hotel;

            $web_array = [
                'Hotel Title' => $hotel->name,
                'Hotel Email' => $hotel->email,
                'Hotel Phone' => $hotel->phone,
                'Hotel Rating' => $hotel->ratting,
                'Hotel CheckIn Time' => $hotel->check_in,
                'Hotel CheckOut Time' => $hotel->check_out,
                'Hotel Short Description' => $hotel->short_description,
                'City' => $hotel->city,
                'State' => $hotel->state,
                'Address' => $hotel->address,
                'Zip Code' => $hotel->zip_code,
                'Description' => $hotel->description,
                'Hotel Policy' => $hotel->policy
            ];

            $action = 'New Hotel';
            $module = 'Holidayz';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
