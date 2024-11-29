<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\VCard\Events\CreateAppointment;

class CreateAppointmentLis
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
    public function handle(CreateAppointment $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $appointment = $event->appointment;
            $business_name   = \Workdo\VCard\Entities\Business::find($request->business_id);
            if (!empty($business_name)) {
                // $appointment->business_id = $business_name->title;
            }
            $action = 'New Appointment';
            $module = 'VCard';
            SendWebhook::SendWebhookCall($module, $appointment, $action);
        }
    }
}
