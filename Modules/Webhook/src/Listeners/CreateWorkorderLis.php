<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\CMMS\Entities\Component;
use Workdo\CMMS\Events\CreateWorkorder;
use App\Models\User;

class CreateWorkorderLis
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
    public function handle(CreateWorkorder $event)
    {
        if(module_is_active('Webhook')){
            $request = $event->request;
            $workorder = $event->workorder;
            $components = Component::where('id', $request->components)->first();
            $sand_user = !empty($request->user) ? $request->user : explode(',',$workorder->created_by);
            $user = User::whereIn('id', $sand_user)->get()->pluck('name');
            $Sand_data = [];
                if (count($user) > 0) {
                    foreach ($user as $datasand) {
                        $Sand_data[] = $datasand;
                    }
                }
            $workorder->sand_to = implode(',' , $Sand_data);
            $workorder->components_name = $components->name;

            $action = 'Work Order Assigned';
            $module = 'CMMS';
            SendWebhook::SendWebhookCall($module ,$workorder,$action);
        }
    }
}
