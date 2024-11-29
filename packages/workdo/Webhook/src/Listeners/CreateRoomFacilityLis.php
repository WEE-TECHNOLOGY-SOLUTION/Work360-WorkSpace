<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Holidayz\Events\CreateRoomFacility;
use Workdo\Webhook\Entities\SendWebhook;

class CreateRoomFacilityLis
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
    public function handle(CreateRoomFacility $event)
    {
        if (module_is_active('Webhook')) {

            $request = $event->request;
            $facility = $event->facility;

            $childFacilitiesData = [];

            foreach ($request['child_facilities'] as $facilitys) {
                $subName = $facilitys['sub_name'];
                $subPrice = $facilitys['sub_price'];

                $childFacilitiesData[] = [
                    'sub_name' => $subName,
                    'sub_price' => $subPrice,
                ];
            }

            $web_array = [
                'Facility Title' => $facility->name,
                'Short Description' => strip_tags($facility->short_description),
                'Child Facilities' => $childFacilitiesData
            ];

            $action = 'New Facilities';
            $module = 'Holidayz';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
