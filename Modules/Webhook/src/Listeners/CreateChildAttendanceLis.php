<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ChildcareManagement\Entities\Child;
use Workdo\ChildcareManagement\Events\CreateChildAttendance;
use Workdo\Webhook\Entities\SendWebhook;

class CreateChildAttendanceLis
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
    public function handle(CreateChildAttendance $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $childAttendance = $event->childAttendance;

            $child = Child::find($childAttendance->child_id);

            $web_array = [
                'Child First Name' => $child->first_name,
                'Child Last Name' => $child->last_name,
                'Date' => $childAttendance->date,
                'Clock In' => $childAttendance->clock_in,
                'Clock Out' => $childAttendance->clock_out,
                'Status' => $childAttendance->status,
            ];

            $action = 'New Child Attendance';
            $module = 'ChildcareManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
