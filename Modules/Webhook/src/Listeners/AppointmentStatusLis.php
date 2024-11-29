<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Appointment\Events\AppointmentStatus;

class AppointmentStatusLis
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
    public function handle(AppointmentStatus $event)
    {
        if(module_is_active('Webhook'))
        {
            $schedule = $event->schedule;
            $action = 'Appointment Status';
            $module = 'Appointment';
            SendWebhook::SendWebhookCall($module ,$schedule,$action, $schedule->workspace);
        }
    }
}
