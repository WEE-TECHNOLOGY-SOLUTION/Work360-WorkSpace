<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GarageManagement\Entities\RepairCategory;
use Workdo\GarageManagement\Entities\VehicleType;
use Workdo\GarageManagement\Events\CreateService;
use Workdo\Webhook\Entities\SendWebhook;

class CreateServiceLis
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
    public function handle(CreateService $event)
    {
        if (module_is_active('Webhook')) {
            $service = $event->service;

            $assign_to = User::find($service->assign_to);
            $vehicle_type = VehicleType::find($service->vehicle_type);
            $vehicle_category = RepairCategory::find($service->repair_category);

            $web_array = [
                'Card Number' => $service->card_number,
                'Title' => $service->title,
                'Assign To' => $assign_to->name,
                'Vehicle Type' => $vehicle_type->name,
                'Vehicle Category' => $vehicle_category->name,
                'Service Date' => $service->service_date,
                'Return Date' => $service->return_date,
                'Service Charge' => $service->service_charge,
                'Type of Services' => $service->type_of_service,
                'Moter Test' => $service->motor_test,
                'Wash' => $service->wash,
                'Notes' => $service->notes,
            ];

            $action = 'New Service';
            $module = 'GarageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
