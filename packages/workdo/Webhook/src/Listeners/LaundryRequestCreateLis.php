<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LaundryManagement\Entities\LaundryLocation;
use Workdo\LaundryManagement\Entities\LaundryService;
use Workdo\LaundryManagement\Events\LaundryRequestCreate;
use Workdo\Webhook\Entities\SendWebhook;

class LaundryRequestCreateLis
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
    public function handle(LaundryRequestCreate $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $laundryrequest = $event->laundryrequest;

            $services_id = explode(',', $laundryrequest->services);
            $services = LaundryService::whereIn('id', $services_id)->get();
            $location = LaundryLocation::find($laundryrequest->location);

            $allData = [];

            foreach ($services as $service) {
                $data = [
                    'Service Title' => $service->name,
                    'Service Cost' => $service->cost,
                ];
                $allData[] = $data;
            }

            $web_array = [
                'Customer Name' => $laundryrequest->name,
                'Phone' => $laundryrequest->phone,
                'Email' => $laundryrequest->email,
                'Address' => $laundryrequest->address,
                'Services' => $allData,
                'Location' => $location->name,
                'Number of Cloths' => $laundryrequest->cloth_no,
                'Total' => $laundryrequest->total,
                'Pickup Date' => $laundryrequest->pickup_date,
                'Delivery Date' => $laundryrequest->delivery_date,
                'Instructions' => $laundryrequest->instructions,
            ];

            $action = 'New Laundry Request';
            $module = 'LaundryManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
