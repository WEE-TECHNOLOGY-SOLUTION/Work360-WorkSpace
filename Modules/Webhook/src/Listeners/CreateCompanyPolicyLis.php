<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Hrm\Events\CreateCompanyPolicy;

class CreateCompanyPolicyLis
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
    public function handle(CreateCompanyPolicy $event)
    {
        if(module_is_active('Webhook')){
            $request = $event->request;
            $policy = $event->policy;
            $branch = \Workdo\Hrm\Entities\Branch::where('id',$request->branch)->first();
            if(!empty($branch)){
                $policy->branch = $branch->name;
            }
            unset($policy->attachment);
            $action = 'New Company Policy';
            $module = 'Hrm';
            SendWebhook::SendWebhookCall($module ,$policy,$action);
        }
    }
}
