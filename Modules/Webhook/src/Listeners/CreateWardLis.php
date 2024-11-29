<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\HospitalManagement\Events\CreateWard;
use Workdo\Webhook\Entities\SendWebhook;

class CreateWardLis
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
    public function handle(CreateWard $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $ward = $event->ward;

            $web_array = [
                'Ward Title' => $ward->name
            ];

            $action = 'New Ward';
            $module = 'HospitalManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
