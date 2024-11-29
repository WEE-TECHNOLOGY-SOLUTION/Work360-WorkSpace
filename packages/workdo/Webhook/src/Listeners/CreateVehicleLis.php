<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Fleet\Entities\Driver;
use Workdo\Fleet\Entities\FuelType;
use Workdo\Fleet\Entities\VehicleType;
use Workdo\Fleet\Events\CreateVehicle;
use Workdo\Webhook\Entities\SendWebhook;

class CreateVehicleLis
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
    public function handle(CreateVehicle $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $vehicle = $event->Vehicle;

            $vehicle_type = VehicleType::find($vehicle->vehicle_type);
            $fuel_type = FuelType::find($vehicle->fuel_type);
            $driver = Driver::find($vehicle->driver_name);

            $web_array = [
                'Vehicle Name' => $vehicle->name,
                "Vehicle Type" => $vehicle_type->name,
                "Driver Name" => $driver->name,
                "Driver Phone" => $driver->phone,
                "Vehicle Registration Date" => $vehicle->registration_date,
                "Vehicle Registration Expire Date" => $vehicle->register_ex_date,
                "Vehicle Lincense Plate" => $vehicle->lincense_plate,
                "Vehicle Id Number" => $vehicle->vehical_id_num,
                "Vehicle Model Year" => $vehicle->model_year,
                "Vehicle Fuel Type" => $fuel_type->name,
                "Vehicle Seat Capacity" => $vehicle->seat_capacity,
                "Vehicle Status" => $vehicle->status
            ];

            $action = 'New Vehicle';
            $module = 'Fleet';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
