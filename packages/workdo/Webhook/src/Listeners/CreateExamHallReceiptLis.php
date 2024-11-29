<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Exam\Entities\ExamHall;
use Workdo\Exam\Entities\ExamList;
use Workdo\Exam\Events\CreateExamHallReceipt;
use Workdo\Webhook\Entities\SendWebhook;

class CreateExamHallReceiptLis
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
    public function handle(CreateExamHallReceipt $event)
    {
        if (module_is_active('Webhook')) {
            $examhallreceipt = $event->examhallreceipt;

            $exam = ExamList::find($examhallreceipt->exam_name);
            $hall = ExamHall::find($examhallreceipt->exam_hall);

            $web_array = [
                'Exam' => $exam->examlist,
                'Exam Hall' => $hall->hall_name,
                'Students' => json_decode($examhallreceipt->student_name, true),
            ];

            $action = 'New Exam Hall Receipt';
            $module = 'Exam';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
