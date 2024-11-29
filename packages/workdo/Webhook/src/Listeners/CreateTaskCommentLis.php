<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Taskly\Events\CreateTaskComment;

class CreateTaskCommentLis
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
    public function handle(CreateTaskComment $event)
    {
        if(module_is_active('Webhook'))
        {
            $comment = $event->comment;
            $task = \Workdo\Taskly\Entities\Task::where('id',$comment->task_id)->first();
            $comments = new \Workdo\Taskly\Entities\Comment;
            $comments->comment = $comment->comment;
            $comments->created_by = $comment->created_by;
            $comments->user_type = $comment->user_type;
            $comments->task_title = $task->title;
            $action = 'New Task Comment';
            $module = 'Taskly';
            SendWebhook::SendWebhookCall($module ,$comments,$action);
        }
    }
}



