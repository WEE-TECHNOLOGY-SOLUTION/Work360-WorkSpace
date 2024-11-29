<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\VehicleInspectionManagement\Events\CreateInspectionList;
use Workdo\Webhook\Entities\SendWebhook;

class CreateInspectionListLis
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
    public function handle(CreateInspectionList $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $inspectionList = $event->inspectionList;

            $web_array = [
                'Inspection Name' => $inspectionList->name,
                'Inspection Period' => $inspectionList->period,
                'Inspection Notes' => $inspectionList->notes
            ];

            $action = 'New Inspection List';
            $module = 'VehicleInspectionManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
