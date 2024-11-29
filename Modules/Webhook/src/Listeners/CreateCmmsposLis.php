<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CMMS\Entities\Supplier;
use App\Models\User;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\CMMS\Events\CreateCmmspos;

class CreateCmmsposLis
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
    public function handle(CreateCmmspos $event)
    {
        if(module_is_active('Webhook')){
            $request = $event->request;
            $pos = $event->Pos;
            $supplier = Supplier::where('id', $request->supplier_id)->first();
            $user = User::where('id', $request->user_id)->first();

            $pos->user_name = $user->name;
            $pos->supplier_name = $supplier->name;

            $action = 'New POs';
            $module = 'CMMS';
            SendWebhook::SendWebhookCall($module ,$pos,$action);
        }
    }
}
