<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ChildcareManagement\Entities\Activity;
use Workdo\ChildcareManagement\Events\CreateClass;
use Workdo\Webhook\Entities\SendWebhook;

class CreateClassLis
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
    public function handle(CreateClass $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $class = $event->class;

            $teacher = User::find($class->teacher_id);
            $activity_id = explode(',', $class->activity_id);
            $activity = Activity::whereIn('id', $activity_id)->get();

            $activityArray = [];

            foreach ($activity as $item) {
                $attributes = [
                    'Activity Title' => $item->name,
                    'Activity Start Time' => $item->start_time,
                    'Activity End Time' => $item->end_time,
                    'Activity Description' => $item->description,
                ];
                $activityArray[] = $attributes;
            }

            $web_array = [
                'Room Number' => $class->room_number,
                'Class Capacity' => $class->capacity,
                'Class Level' => $class->class_level,
                'Date' => $class->date,
                'Teacher Name' => $teacher->name,
                'Teacher Email' => $teacher->email,
                'Teacher Phone Number' => $teacher->mobile_no,
                'Activity' => $activityArray
            ];

            $action = 'New Class';
            $module = 'ChildcareManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
