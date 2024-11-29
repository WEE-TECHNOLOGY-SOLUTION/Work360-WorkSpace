<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Taskly\Events\UpdateTaskStage;

class UpdateTaskStageLis
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
    public function handle(UpdateTaskStage $event)
    {
        if(module_is_active('Webhook'))
        {
            $request = $event->request;
            $task = $event->task;
            $new_status   = \Workdo\Taskly\Entities\Stage::where('id',$request->new_status)->first();

            $task_status = new \Workdo\Taskly\Entities\Task;
            $task_status->task_title = $task->title;
            $task_status->status = $new_status->name;
            $action = 'Task Stage Update';
            $module = 'Taskly';
            SendWebhook::SendWebhookCall($module ,$task_status,$action);
        }
    }
}



