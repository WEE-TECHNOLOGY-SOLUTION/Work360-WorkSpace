<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MusicInstitute\Events\CreateMusicStudent;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMusicStudentLis
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
    public function handle(CreateMusicStudent $event)
    {
        if (module_is_active('Webhook')) {
            $student = $event->student;

            $web_array = [
                'Student Name' => $student->name,
                'Student Email' => $student->email,
                'Student Date of Birth' => $student->dob,
                'Student Mobile Number' => $student->mobile_no,
                'Instrument' => $student->instrument,
                'Language' => $student->language,
                'Gender' => $student->gender,
                'Address' => $student->address,
                'City' => $student->city,
                'State' => $student->state,
                'Country' => $student->country,
                'Zip Code' => $student->zip_code
            ];

            $action = 'New Music Student';
            $module = 'MusicInstitute';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
