<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Entities\AgricultureDepartment;
use Workdo\AgricultureManagement\Events\CreateAgricultureOffices;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureOfficesLis
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
    public function handle(CreateAgricultureOffices $event)
    {
        if (Module_Alias_Name('Webhook')) {
            $request = $event->request;
            $agricultureoffice = $event->agricultureoffice;

            $department = AgricultureDepartment::find($agricultureoffice->department);

            $web_array = [
                'Agriculture Office Title' => $agricultureoffice->name,
                'Agriculture Office Department' => $department->name,
                'Agriculture Office Description' => $department->description,
            ];

            $action = 'New Agriculture Office';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
