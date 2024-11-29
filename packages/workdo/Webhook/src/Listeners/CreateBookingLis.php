<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Fleet\Entities\Vehicle;
use Workdo\Fleet\Events\CreateBooking;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Zapier\Entities\SendZap;

class CreateBookingLis
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
    public function handle(CreateBooking $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $bookings = $event->bookings;

            $customer = User::find($bookings->customer_name);
            $vehicle = Vehicle::find($bookings->vehicle_name);

            $web_array = [
                'Customer Name' => $customer->name,
                'Customer Email' => $customer->email,
                'Vehicle Title' => $vehicle->name,
                "Trip Type" => $bookings['trip_type'],
                "Start Date" => $bookings['start_date'],
                "End Date" => $bookings['end_date'],
                "Start Address" => $bookings['start_address'],
                "End Address" => $bookings['end_address'],
                "Total Price" => $bookings['total_price'],
                "booking Status" => $bookings['status'],
                "Notes" => $bookings['notes']
            ];

            $action = 'New Booking';
            $module = 'Fleet';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
