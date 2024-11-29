<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Appointment\Events\CreateAppointments;

class CreateAppointmentsLis
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
    public function handle(CreateAppointments $event)
    {
        if (module_is_active('Webhook')) {
            $schedule = $event->schedule;
            $schedule->appointment_name = $schedule->appointment->name;

            $action = 'New Appointment';
            $module = 'Appointment';
            SendWebhook::SendWebhookCall($module, $schedule, $action, $schedule->workspace);
        }
    }
}





