<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\School\Entities\Classroom;
use Workdo\School\Entities\Subject;
use Workdo\School\Events\CreateSchoolHomework;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSchoolHomeworkLis
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
    public function handle(CreateSchoolHomework $event)
    {
        if (module_is_active('Webhook')) {
            $homework = $event->homework;

            $class = Classroom::find($homework->classroom);
            $subject = Subject::find($homework->subject);

            $web_array = [
                'Homework Title' => $homework->title,
                'Class' => $class->class_name,
                'Subject' => $subject->subject_name,
                'Homework Submission Date' => $homework->submission_date,
                'Homework Content' => $homework->content,
            ];

            $action = 'New Homework';
            $module = 'School';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
