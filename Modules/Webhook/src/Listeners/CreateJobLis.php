<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Recruitment\Events\CreateJob;

class CreateJobLis
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
    public function handle(CreateJob $event)
    {
        if(module_is_active('Webhook')){
            $job = $event->job;
            if($job->category){
                $categories = \Workdo\Recruitment\Entities\JobCategory::where('id',$job->category)->first();
                // $job->category = $categories->title;
            }
            if($job->branch){
                $branch = \Workdo\Hrm\Entities\Branch::where('id',$job->branch)->first();
                // $job->branch = $branch->name;
            }
            if($job->custom_question)
            {
                $custom_question = \Workdo\Recruitment\Entities\CustomQuestion::whereIn('id',(explode(',',$job->custom_question)))->get()->pluck('question')->toArray();
                if(count($custom_question) > 0)
                {
                    $custom_question = implode(',',$custom_question);
                }
                $job->custom_question = $custom_question;
            }
            $action = 'New Job';
            $module = 'Recruitment';
            SendWebhook::SendWebhookCall($module ,$job,$action);
        }
    }
}