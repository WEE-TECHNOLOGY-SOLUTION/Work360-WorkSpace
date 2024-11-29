<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\VehicleInspectionManagement\Entities\InspectionRequest;
use Workdo\VehicleInspectionManagement\Entities\InspectionVehicle;
use Workdo\VehicleInspectionManagement\Events\CreateDefectsAndRepairs;
use Workdo\Webhook\Entities\SendWebhook;

class CreateDefectsAndRepairsLis
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
    public function handle(CreateDefectsAndRepairs $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $invoice = $event->invoice;

            $inspection_request = InspectionRequest::find($request->request_id);
            $vehicle = InspectionVehicle::find($inspection_request->vehicle_id);

            $web_array = [
                'Inspector Name' => $inspection_request->inspector_name,
                'Inspector Email' => $inspection_request->inspector_email,
                'Inspecton Vehicle' => $vehicle->model,
                'Status' => $inspection_request->status,
                'Invoice Issue Date' => $invoice->issue_date,
                'Invoice Due Date' => $invoice->due_date,
                'Service Charge' => $invoice->service_charge,
                'Parts' => $request->items
            ];

            $action = 'New Defects And Repairs';
            $module = 'VehicleInspectionManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
