<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Exam\Events\CreateExamGrade;
use Workdo\Webhook\Entities\SendWebhook;

class CreateExamGradeLis
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
    public function handle(CreateExamGrade $event)
    {
        if (module_is_active('Webhook')) {
            $examGrade = $event->examGrade;

            $web_array = [
                'Grade Name' => $examGrade->grade_name,
                'Grade Point' => $examGrade->grade_point,
                'Mark From' => $examGrade->mark_from,
                'Mark To' => $examGrade->mark_to,
                'Comment' => $examGrade->comment
            ];

            $action = 'New Exam Grade';
            $module = 'Exam';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
