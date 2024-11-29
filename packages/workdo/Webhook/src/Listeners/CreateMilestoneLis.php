<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Taskly\Events\CreateMilestone;

class CreateMilestoneLis
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
    public function handle(CreateMilestone $event)
    {
        if (module_is_active('Webhook')) {
            $milestone = $event->milestone;
            $project = \Workdo\Taskly\Entities\Project::where('id', $milestone->project_id)->first();

            $action = 'New Milestone';
            $module = 'Taskly';
            SendWebhook::SendWebhookCall($module, $milestone, $action);
        }
    }
}
