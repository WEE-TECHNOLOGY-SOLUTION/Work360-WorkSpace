<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MusicInstitute\Events\CreateMusicTeacher;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMusicTeacherLis
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
    public function handle(CreateMusicTeacher $event)
    {
        if (module_is_active('Webhook')) {
            $teacher = $event->teacher;

            $web_array = [
                'Teacher Name' => $teacher->name,
                'Teacher Email' => $teacher->email,
                'Date of Birth' => $teacher->dob,
                'Teacher Mobile Number' => $teacher->mobile_no,
                'Gender' => $teacher->gender,
                'Expertise' => $teacher->expertise,
                'Certification Detail' => $teacher->certification_detail
            ];

            $action = 'New Music Teacher';
            $module = 'MusicInstitute';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
