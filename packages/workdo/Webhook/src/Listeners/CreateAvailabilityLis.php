<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Rotas\Entities\Employee;
use Workdo\Rotas\Events\CreateAvailability;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAvailabilityLis
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
    public function handle(CreateAvailability $event)
    {
        $availability = $event->availability;

        if (module_is_active('Webhook')) {
            $user = Employee::where('id', $availability->employee_id)->first();
            
            $action = 'New Availabilitys';
            $module = 'Rotas';

            SendWebhook::SendWebhookCall($module, $availability, $action);
        }
    }
}