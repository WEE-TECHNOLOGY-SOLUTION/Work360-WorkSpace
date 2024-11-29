<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\PropertyManagement\Events\CreateProperty;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePropertyLis
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
    public function handle(CreateProperty $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $property = $event->property;

            $web_array = [
                'Property Title' => $property->name,
                'Property Address' => $property->address,
                'Property Country' => $property->country,
                'Property State' => $property->state,
                'Property City' => $property->city,
                'Property Pincode' => $property->pincode,
                'Property Description' => $property->description,
                'Property Security Deposite' => $property->security_deposit,
                'Property Maintenance Charge' => $property->maintenance_charge,
            ];

            $action = 'New Property';
            $module = 'PropertyManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
