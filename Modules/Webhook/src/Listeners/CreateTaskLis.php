<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Taskly\Events\CreateTask;

class CreateTaskLis
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
    public function handle(CreateTask $event)
    {
        if(module_is_active('Webhook'))
        {
            $request = $event->request;
            $task = $event->task;
            $project = \Workdo\Taskly\Entities\Project::where('id',$task->project_id)->first();
            $stage = \Workdo\Taskly\Entities\Stage::where('id',$task->status)->first();
            $assign_user = User::whereIn('id',$request->assign_to)->get()->pluck('name')->toArray();
            $milestone = \Workdo\Taskly\Entities\Milestone::where('id',$request->milestone_id)->first();
            $action = 'New Task';
            $module = 'Taskly';
            SendWebhook::SendWebhookCall($module ,$task,$action);
        }
    }
}
