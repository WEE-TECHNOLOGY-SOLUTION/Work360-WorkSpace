<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\TimeTracker\Events\CreateTimeTracker;
use Workdo\Webhook\Entities\SendWebhook;

class CreateTimeTrackerLis
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
    public function handle(CreateTimeTracker $event)
    {
        if (module_is_active('Webhook')) {
            $tracker = $event->track;

            $web_array = [
                'Project Name' => $tracker->project_name,
                'Project Task' => $tracker->project_task,
                'Project Workspace' => $tracker->project_workspace,
                'Tracker Start Time' => $tracker->start_time
            ];

            $action = 'New Tracker';
            $module = 'TimeTracker';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
