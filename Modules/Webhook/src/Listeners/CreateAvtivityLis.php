<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ChildcareManagement\Events\CreateAvtivity;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAvtivityLis
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
    public function handle(CreateAvtivity $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $activity = $event->activity;

            $web_array = [
                'Activity Title' => $activity->name,
                'Activity Start Time' => $activity->start_time,
                'Activity End Time' => $activity->end_time,
                'Activity Description' => $activity->description
            ];

            $action = 'New Activity';
            $module = 'ChildcareManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
