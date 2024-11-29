<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\School\Entities\Classroom;
use Workdo\School\Entities\Subject;
use Workdo\School\Events\CreateTimetable;
use Workdo\Webhook\Entities\SendWebhook;

class CreateTimetableLis
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
    public function handle(CreateTimetable $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $timetable = $event->timetable;

            $class = Classroom::find($timetable->class_id);
            $subject_ids = explode(',', $timetable->subject_ids);
            $subjects = Subject::WhereIn('id', $subject_ids)->get();

            $subjectData = [];

            foreach ($subjects as $subject) {
                $subjectCode = $subject->subject_code;
                $subjectName = $subject->subject_name;

                $subjectData[] = [
                    'subject_code' => $subjectCode,
                    'subject_name' => $subjectName,
                ];
            }

            $timeTable = json_decode($timetable->all_time, true);
            $result = [];

            foreach ($timeTable as $day => $slots) {
                foreach ($slots as $slotNumber => $slot) {
                    $result[] = [
                        'day' => $day,
                        'first_time' => $slot['first_time'],
                        'last_time' => $slot['last_time']
                    ];
                }
            }

            $web_array = [
                'Class' => $class->class_name,
                'Start Time' => $timetable->start_time,
                'End Time' => $timetable->end_time,
                'Subject' => $subjectData,
                'Time Table' => $result
            ];

            $action = 'New Time Table';
            $module = 'School';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
