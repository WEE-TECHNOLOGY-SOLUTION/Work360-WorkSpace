<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Taskly\Events\CreateBug;

class CreateBugLis
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
    public function handle(CreateBug $event)
    {
        if (module_is_active('Webhook')) {
            $bug = $event->bug;
            $project = \Workdo\Taskly\Entities\Project::where('id', $bug->project_id)->first();
            $stage = \Workdo\Taskly\Entities\BugStage::where('id', $bug->status)->first();
            $assign_user = User::where('id', $bug->assign_to)->first();

            $action = 'New Bug';
            $module = 'Taskly';
            SendWebhook::SendWebhookCall($module, $bug, $action);
        }
    }
}