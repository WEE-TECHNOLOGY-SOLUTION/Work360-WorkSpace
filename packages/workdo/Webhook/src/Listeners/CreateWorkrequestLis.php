<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\CMMS\Entities\Component;
use App\Models\User;
use Workdo\CMMS\Events\CreateWorkrequest;

class CreateWorkrequestLis
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
    public function handle(CreateWorkrequest $event)
    {
        if(module_is_active('Webhook')){
            $request = $event->request;
            $workorder = $event->workorder;
            $components = Component::where('id', $request->components_id)->first();
            
            $workorder->components_name = $components->name;
            $action = 'Work Order Request';
            $module = 'CMMS';
            $workspace_id = $workorder->workspace;
            SendWebhook::SendWebhookCall($module ,$workorder,$action ,$workspace_id);
        }
    }
}
