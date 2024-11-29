<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Entities\AgricultureDepartment;
use Workdo\AgricultureManagement\Entities\AgricultureOffices;
use Workdo\AgricultureManagement\Events\CreateAgricultureUser;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureUserLis
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
    public function handle(CreateAgricultureUser $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agricultureuser = $event->agricultureuser;

            $department = AgricultureDepartment::find($agricultureuser->department);
            $office = AgricultureOffices::find($agricultureuser->office);

            $web_array = [
                'User Name' => $agricultureuser->name,
                'Email' => $agricultureuser->email,
                'Phone Number' => $agricultureuser->phone,
                'Department' => $department->name,
                'Office' => $office->name,
                'Total Area' => $agricultureuser->total_area,
                'Cultivate Areas' => $agricultureuser->cultivate_area,
            ];

            $action = 'New Agriculture User';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
