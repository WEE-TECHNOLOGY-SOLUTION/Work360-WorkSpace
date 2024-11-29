<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Exam\Entities\ExamList;
use Workdo\Exam\Events\CreateExamTimeTable;
use Workdo\Webhook\Entities\SendWebhook;

class CreateExamTimeTableLis
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
    public function handle(CreateExamTimeTable $event)
    {
        if (module_is_active('Webhook')) {
            $timetable = $event->timetable;

            $exam = ExamList::find($timetable->exam_name);

            $web_array = [
                'Exam Title' => $exam->examlist,
                'Subject' => json_decode($timetable->subject)
            ];

            $action = 'New Exam Time Table';
            $module = 'Exam';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
