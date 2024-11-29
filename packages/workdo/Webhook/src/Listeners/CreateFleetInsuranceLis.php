<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Fleet\Entities\Recurring;
use Workdo\Fleet\Entities\Vehicle;
use Workdo\Fleet\Events\CreateInsurance;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFleetInsuranceLis
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
    public function handle(CreateInsurance $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $insurance = $event->insurance;

            $vehicle = Vehicle::find($insurance->vehicle_name);
            $recurring = Recurring::find($insurance->scheduled_period);

            $web_array = [
                'Insurance Provider' => $insurance->insurance_provider,
                'Vehicle Name' => $vehicle->name,
                'Start Date' => $insurance->start_date,
                'End Date' => $insurance->end_date,
                'Schedule Date' => $insurance->scheduled_date,
                'Recurring Period Title' => $recurring->name,
                'Deductible' => $insurance->deductible,
                'Charge Payable' => $insurance->charge_payable,
                'Policy Number' => $insurance->policy_number,
                'Notes' => $insurance->notes
            ];

            $module = 'Fleet';
            $action = 'New Insurance';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
