<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\VehicleInspectionManagement\Entities\InspectionList;
use Workdo\VehicleInspectionManagement\Entities\InspectionVehicle;
use Workdo\VehicleInspectionManagement\Events\CreateInspectionRequest;
use Workdo\Webhook\Entities\SendWebhook;

class CreateInspectionRequestLis
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
    public function handle(CreateInspectionRequest $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $inspectionRequest = $event->inspectionRequest;

            $inspection_id = explode(',', $inspectionRequest->inspections);
            $vehicle = InspectionVehicle::find($inspectionRequest->vehicle_id);
            $inspections = InspectionList::whereIn('id', $inspection_id)->get();

            $attributesArray = [];

            foreach ($inspections as $inspection) {
                $attributes = [
                    'Inspection Name' => $inspection->name,
                    'Inspection Period' => $inspection->period,
                    'Inspection Notes' => $inspection->notes,

                ];
                $attributesArray[] = $attributes;
            }

            $web_array = [
                'Inspector Name' => $inspectionRequest->inspector_name,
                'Inspector Email' => $inspectionRequest->inspector_email,
                'Inspection Vehicle' => $vehicle->model,
                'Inspections' => $attributesArray,
            ];

            $action = 'New Vehicle Inspection Request';
            $module = 'VehicleInspectionManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
