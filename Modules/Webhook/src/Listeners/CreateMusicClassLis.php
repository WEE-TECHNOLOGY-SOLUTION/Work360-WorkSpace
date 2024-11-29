<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MusicInstitute\Entities\MusicInstrument;
use Workdo\MusicInstitute\Entities\MusicTeacher;
use Workdo\MusicInstitute\Events\CreateMusicClass;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMusicClassLis
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
    public function handle(CreateMusicClass $event)
    {
        if (module_is_active('Webhook')) {
            $class = $event->class;

            $instruction = MusicInstrument::find($class->instrument_id);
            $teacher = MusicTeacher::find($class->teacher_id);

            $studentId = explode(',', $class->student_id);

            $usersData = User::whereIn('id', $studentId)->get();

            $studentInfo = [];
            foreach ($usersData as $user) {
                $studentInfo[] = [
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            }

            $web_array = [
                'Class Title' => $class->name,
                'Class Start Date / Time' => $class->start_date_time,
                'Class End Date / Time' => $class->end_date_time,
                'Teacher Name' => $teacher->name,
                'Teacher Email' => $teacher->email,
                'Instruction Title' => $instruction->name,
                'Instruction Brand' => $instruction->brand,
                'Students' => $studentInfo,
                'Location' => $class->location,
                'Schedule' => $class->schedule
            ];

            $action = 'New Music Class';
            $module = 'MusicInstitute';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
