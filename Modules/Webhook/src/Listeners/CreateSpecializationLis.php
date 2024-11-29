<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\HospitalManagement\Events\CreateSpecialization;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSpecializationLis
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
    public function handle(CreateSpecialization $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $specialization = $event->specialization;

            $web_array = [
                'Specialization Title' => $specialization->name
            ];

            $action = 'New Specialization';
            $module = 'HospitalManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
