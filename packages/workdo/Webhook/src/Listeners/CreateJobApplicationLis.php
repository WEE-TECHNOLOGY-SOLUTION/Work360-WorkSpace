<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Recruitment\Events\CreateJobApplication;

class CreateJobApplicationLis
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
    public function handle(CreateJobApplication $event)
    {
        if(module_is_active('Webhook'))
        {
            $job = $event->job;
            if($job->stage){
                $stage = \Workdo\Recruitment\Entities\JobStage::where('id',$job->stage)->first();
                $job->stage = $stage->title;
            }
            if($job->job){
                $jobs = \Workdo\Recruitment\Entities\Job::where('id',$job->job)->first();
                $job->job = $jobs->title;
            }
            unset($job->profile,$job->resume);
            $action = 'New Job Application';
            $module = 'Recruitment';
            SendWebhook::SendWebhookCall($module ,$job,$action,$job->workspace);
        }
    }
}
