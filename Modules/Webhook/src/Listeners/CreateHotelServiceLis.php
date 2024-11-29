<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Holidayz\Events\CreateHotelService;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Zapier\Entities\SendZap;

class CreateHotelServiceLis
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
    public function handle(CreateHotelService $event)
    {
        if (module_is_active('Webhook')) {

            $request = $event->request;
            $service = $event->service;

            $childServiceData = [];

            foreach ($request['category-group'] as $group) {
                $subName = $group['sub_services'];

                $childServiceData[] = [
                    'sub_services' => $subName,
                ];
            }

            $web_array = [
                'Service Title' => $service->name,
                'Sub Service' => $childServiceData,
            ];

            $action = 'New Hotel Services';
            $module = 'Holidayz';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
