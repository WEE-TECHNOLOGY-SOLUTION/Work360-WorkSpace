<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\School\Events\CreateClassroom;
use Workdo\Webhook\Entities\SendWebhook;

class CreateClassroomLis
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
    public function handle(CreateClassroom $event)
    {
        if (module_is_active('Webhook')) {
            $classroom = $event->classroom;

            $web_array = [
                'Class Name' => $classroom->class_name,
                'Class Capacity' => $classroom->class_capacity,
            ];

            $action = 'New Classroom';
            $module = 'School';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
