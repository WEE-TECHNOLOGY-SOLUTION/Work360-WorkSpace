<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Hrm\Events\CreateEvent;

class CreateEventLis
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
    public function handle(CreateEvent $event)
    {
        if(module_is_active('Webhook')){
            $request = $event->request;
            $event_cal = $event->event;
            if(in_array('0',$request->department_id))
            {
                $department_name = 'All Departments';
            }
            else
            {
                $department_name = 'Not Found';
                $department = \Workdo\Hrm\Entities\Department::whereIn('id',$request->department_id)->get()->pluck('name')->toArray();
                if(count($department) > 0)
                {
                    $department_name = implode(',',$department);
                }
            }
            if(in_array('0',$request->employee_id))
            {
                $employee_name = 'All Employees';
            }
            else
            {
                $employee_name = 'Not Found';
                $employee = \Workdo\Hrm\Entities\Employee::whereIn('id',$request->employee_id)->get()->pluck('name')->toArray();
                if(count($employee) > 0)
                {
                    $employee_name = implode(',',$employee);
                }
            }
            if($request->branch_id == '0')
            {
                $branch_name = 'All Branch';
            }
            else
            {
                $branch = \Workdo\Hrm\Entities\Branch::where('id',$request->branch_id)->first();
                $branch_name = $branch->name;
            }
            $event_cal->branch_name = $branch_name;
            $event_cal->department_name = $department_name;
            $event_cal->employee_name = $employee_name;

            $action = 'New Event';
            $module = 'Hrm';
            SendWebhook::SendWebhookCall($module,$event_cal,$action);
        }
    }
}
