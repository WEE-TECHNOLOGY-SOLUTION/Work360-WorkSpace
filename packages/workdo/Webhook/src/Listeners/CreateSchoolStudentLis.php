<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\School\Events\CreateSchoolStudent;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSchoolStudentLis
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
    public function handle(CreateSchoolStudent $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $student = $event->student;

            $web_array = [
                'Student Name' => $student->name,
                'Student Date of Birth' => $student->std_date_of_birth,
                'Student Gender' => $student->student_gender,
                'Student Roll Number' => $student->roll_number,
                'Student Address' => $student->std_address,
                'Student State' => $student->std_state,
                'Student City' => $student->std_city,
                'Student Zip Code' => $student->std_zip_code,
                'Student Contact' => $student->contact,
                'Student Email' => $student->email,
            ];

            $action = 'New Students';
            $module = 'School';
            SendWebhook::SendWebhookCall($module, $web_array, $action); 
        }
    }
}
