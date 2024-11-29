<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ChildcareManagement\Events\UpdateChidcare;
use Workdo\Webhook\Entities\SendWebhook;

class UpdateChidcareLis
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
    public function handle(UpdateChidcare $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $childcare = $event->childcare;

            $web_array = [
                'Childcare Title' => $childcare->name,
                'Grade Level' => $childcare->grade_level,
                'Contact Number' => $childcare->contact_number,
                'Start Time' => $childcare->start_time,
                'End Time' => $childcare->end_time,
                'Address' => $childcare->address,
                'Notes' => $childcare->notes
            ];

            $action = 'Update Child Care';
            $module = 'ChildcareManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
