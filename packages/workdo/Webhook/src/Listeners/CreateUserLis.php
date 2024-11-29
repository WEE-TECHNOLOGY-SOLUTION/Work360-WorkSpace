<?php

namespace Workdo\Webhook\Listeners;

use App\Events\CreateUser;
use App\Models\WorkSpace;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;

class CreateUserLis
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
    public function handle(CreateUser $event)
    {
        if(module_is_active('Webhook')){
            $user = $event->user;
            if(\Auth::user()->type != "super admin"){
                $workspace = WorkSpace::where('id',$user->active_workspace)->first();
                $user->active_workspace = $workspace->name;
            }
            $action = 'New User';
            $module = 'general';
            SendWebhook::SendWebhookCall($module ,$user,$action);
        }
    }
}
