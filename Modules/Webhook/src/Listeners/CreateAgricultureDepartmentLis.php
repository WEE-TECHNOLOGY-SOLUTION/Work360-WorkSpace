<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Events\CreateAgricultureDepartment;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureDepartmentLis
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
    public function handle(CreateAgricultureDepartment $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agriculturedepartment = $event->agriculturedepartment;

            $web_array = [
                'Agriculture Department Title' => $agriculturedepartment->name,
                'Agriculture Department Description' => $agriculturedepartment->description,
            ];

            $action = 'New Agriculture Department';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
