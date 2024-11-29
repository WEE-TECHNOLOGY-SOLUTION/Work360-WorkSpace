<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Recruitment\Events\CreateInterviewSchedule;

class CreateInterviewScheduleLis
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
    public function handle(CreateInterviewSchedule $event)
    {
        if(module_is_active('Webhook'))
        {
            $schedule = $event->schedule;
            if($schedule->user_id){

                $user = User::where('id',$schedule->user_id)->first();
                // $schedule->employee = $user->name;
            }
            if($schedule->candidate){
                $candidates = \Workdo\Recruitment\Entities\JobApplication::where('id', $schedule->candidate)->first();
                // $schedule->candidate = $candidates->name;
            }
            unset($schedule->user_id);
            $action = 'Interview Schedule';
            $module = 'Recruitment';
            SendWebhook::SendWebhookCall($module ,$schedule,$action);
        }
    }
}