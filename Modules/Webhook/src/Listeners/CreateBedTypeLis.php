<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\HospitalManagement\Events\CreateBedType;
use Workdo\Webhook\Entities\SendWebhook;

class CreateBedTypeLis
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
    public function handle(CreateBedType $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $bedtype = $event->bedtype;

            $web_array = [
                'Bad Type Title' => $bedtype->name
            ];

            $action = 'New Bed Type';
            $module = 'HospitalManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
