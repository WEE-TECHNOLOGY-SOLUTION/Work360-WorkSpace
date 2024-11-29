<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\School\Entities\Classroom;
use Workdo\School\Events\CreateSubject;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSubjectLis
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
    public function handle(CreateSubject $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $subject = $event->subject;

            $class = Classroom::find($subject->class_id);
            $teacher = User::find($subject->teacher);

            $web_array = [
                'Class' => $class->class_name,
                'Teacher Name' => $teacher->name,
                'Teacher Email' => $teacher->email,
                'Teacher Mobile Number' => $teacher->mobile_no,
                'Subject Name' => $subject->subject_name,
                'Subject Code' => $subject->subject_code
            ];

            $action = 'New Subject';
            $module = 'School';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
