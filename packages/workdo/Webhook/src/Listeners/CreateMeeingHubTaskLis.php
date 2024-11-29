<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MeetingHub\Events\CreateMeeingHubTask;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Zapier\Entities\SendZap;

class CreateMeeingHubTaskLis
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
    public function handle(CreateMeeingHubTask $event)
    {
        if (module_is_active('Webhook')) {
            $task = $event->task;

            $web_array = [
                'Task Title' => $task->name,
                'Task Date' => $task->date,
                'Task Time' => $task->time,
                'Task Priority' => $task->priority,
                'Task Status' => MeetingHubMeeting::$statues[$task->status],
            ];

            $action = 'New Meeting Task';
            $module = 'MeetingHub';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
