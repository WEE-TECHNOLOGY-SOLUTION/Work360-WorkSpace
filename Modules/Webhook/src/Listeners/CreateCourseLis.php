<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\LMS\Events\CreateCourse;

class CreateCourseLis
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
    public function handle(CreateCourse $event)
    {
        if (module_is_active('Webhook')) {
            $course = $event->course;
            if (!empty($course)) {
                $category        = \Workdo\LMS\Entities\CourseCategory::where('id', $course->sub_category)->first();
                $sub_category    = \Workdo\LMS\Entities\CourseSubcategory::where('id', $course->sub_category)->first();
                // $course->category = !empty($category)? $category->name:null;
                // $course->sub_category = !empty($sub_category)? $sub_category->name:null;
                $action = 'New Course';
                $module = 'LMS';
                SendWebhook::SendWebhookCall($module, $course, $action);
            }
        }
    }
}
