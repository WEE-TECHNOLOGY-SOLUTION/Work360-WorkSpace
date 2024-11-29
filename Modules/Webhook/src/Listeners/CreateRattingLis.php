<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LMS\Events\CreateRatting;
use Workdo\Webhook\Entities\SendWebhook;

class CreateRattingLis
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
    public function handle(CreateRatting $event)
    {
        if (module_is_active('Webhook')) {
            $ratting = $event->ratting;
            if (!empty($ratting)) {
                $student = \Workdo\LMS\Entities\Student::where('id', $ratting->student_id)->first();
                $course = \Workdo\LMS\Entities\Course::where('id', $ratting->course_id)->first();
                $tutor = User::where('id', $ratting->tutor_id)->first();

                // $ratting->student_id = !empty($student)? $student->name:null;
                // $ratting->course_id  = !empty($course)? $course->title:null;
                // $ratting->tutor_id   = !empty($tutor)? $tutor->name:null;
                $action = 'New Ratting';
                $module = 'LMS';
                SendWebhook::SendWebhookCall($module, $ratting, $action);
            }
        }
    }
}
