<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Fleet\Entities\Vehicle;
use Workdo\VehicleBookingManagement\Events\CreateVehicleRoute;
use Workdo\Webhook\Entities\SendWebhook;

class CreateVehicleRouteLis
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
    public function handle(CreateVehicleRoute $event)
    {
        if (module_is_active('Webhook')) {
            $route = $event->route;

            $vehicle = Vehicle::find($route->vehicle_id);

            $web_array = [
                'Vehicle Name' => $vehicle->name,
                'Starting Point' => $route->starting_point,
                'Dropping Point' => $route->dropping_point,
                'Start Address' => $route->start_address,
                'End Address' => $route->end_address,
                'Start Time' => $route->start_time,
                'End Time' => $route->end_time,
                'Price' => $route->price
            ];

            $action = 'New Vehicle Route';
            $module = 'VehicleBookingManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
