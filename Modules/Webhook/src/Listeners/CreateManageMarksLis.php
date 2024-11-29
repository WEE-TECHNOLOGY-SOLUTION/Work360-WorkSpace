<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Exam\Entities\ExamGrade;
use Workdo\Exam\Entities\ExamList;
use Workdo\Exam\Events\CreateManageMarks;
use Workdo\School\Entities\Classroom;
use Workdo\School\Entities\Subject;
use Workdo\Webhook\Entities\SendWebhook;

class CreateManageMarksLis
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
    public function handle(CreateManageMarks $event)
    {
        if (module_is_active('Webhook')) {
            $manageMark = $event->manageMark;

            $grade = ExamGrade::find($manageMark->grade);
            $class = Classroom::find($manageMark->class);
            $exam = ExamList::find($manageMark->exam);
            $subject = Subject::find($manageMark->subject);

            $web_array = [
                'Grade' => $grade->grade_name,
                'Class' => $class->class_name,
                'Exam' => $exam->examlist,
                'Subject' => $subject->subject_name,
                'Student Marks' => json_decode($manageMark->student_marks)
            ];

            $action = 'New Mange Marks';
            $module = 'Exam';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
