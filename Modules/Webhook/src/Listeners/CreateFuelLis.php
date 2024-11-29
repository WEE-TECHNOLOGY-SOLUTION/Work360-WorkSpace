<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Fleet\Entities\Driver;
use Workdo\Fleet\Entities\FuelType;
use Workdo\Fleet\Entities\Vehicle;
use Workdo\Fleet\Events\CreateFuel;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFuelLis
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
    public function handle(CreateFuel $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $fuel = $event->fuel;

            $driver = Driver::find($fuel->driver_name);
            $vehicle = Vehicle::find($fuel->vehicle_name);
            $fuel_type = FuelType::find($fuel->fuel_type);

            $web_array = [
                'Driver Name' => $driver->name,
                'Driver Email' => $driver->email,
                'Vehicle Title' => $vehicle->name,
                'Fill Date' => $fuel->fill_date,
                'Fuel Type' => $fuel_type->name,
                'Quantity' => $fuel->quantity,
                'Cost' => $fuel->cost,
                'Total Cost' => $fuel->total_cost,
                'Odometer Reading' => $fuel->odometer_reading,
                'Notes' => $fuel->notes
            ];

            $module = 'Fleet';
            $action = 'New Fuel';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
