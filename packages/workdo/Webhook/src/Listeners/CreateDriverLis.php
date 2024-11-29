<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Fleet\Entities\License;
use Workdo\Fleet\Events\CreateDriver;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Zapier\Entities\SendZap;

class CreateDriverLis
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
    public function handle(CreateDriver $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $driver = $event->driver;

            $license = License::find($driver->lincese_type);

            $web_array = [
                'Driver Name' => $driver->name,
                'Driver Email' => $driver->email,
                'Driver Phone Number' => $driver->phone,
                'Driver License' => $driver->lincese_number,
                'Expiry Date' => $driver->expiry_date,
                'Join Date' => $driver->join_date,
                'Address' => $driver->address,
                'Date Of Birth' => $driver->dob,
                'Working Time' => $driver->Working_time,
                'Driver Status' => $driver->driver_status
            ];

            $action = 'New Driver';
            $module = 'Fleet';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
