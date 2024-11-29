<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GarageManagement\Entities\FuelType;
use Workdo\GarageManagement\Entities\VehicleBrand;
use Workdo\GarageManagement\Entities\VehicleColor;
use Workdo\GarageManagement\Entities\VehicleType;
use Workdo\GarageManagement\Events\CreateGarageVehicle;
use Workdo\Webhook\Entities\SendWebhook;

class CreateGarageVehicleLis
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
    public function handle(CreateGarageVehicle $event)
    {
        if (module_is_active('Webhook')) {
            $garagevehicle = $event->garagevehicle;

            $vehicle_type = VehicleType::find($garagevehicle->vehicle_type);
            $vehicle_brand = VehicleBrand::find($garagevehicle->vehicle_brand);
            $vehicle_color = VehicleColor::find($garagevehicle->vehicle_color);
            $vehicle_fule_type = FuelType::find($garagevehicle->vehicle_fueltype);

            $web_array = [
                'Vehicle Type' => $vehicle_type->name,
                'Vehicle Brand' => $vehicle_brand->name,
                'Vehicle Color' => $vehicle_color->name,
                'Vehicle Fule Type' => $vehicle_fule_type->name,
                'Vehicle Model Name' => $garagevehicle->model_name,
                'Vehicle Model Year' => $garagevehicle->model_year,
                'Vehicle Plate Number' => $garagevehicle->plate_number,
                'Vehicle Key Number' => $garagevehicle->key_number,
                'Vehicle Gear Box' => $garagevehicle->gear_box,
                'Vehicle Engine Number' => $garagevehicle->engine_number,
                'Vehicle Production Date' => $garagevehicle->production_date,
                'Vehicle Cost' => $garagevehicle->cost,
                'Vehicle Notes' => $garagevehicle->notes
            ];

            $action = 'New Garage Vehicle';
            $module = 'GarageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
