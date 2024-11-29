<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Recruitment\Events\ConvertToEmployee;

class ConvertToEmployeeLis
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
    public function handle(ConvertToEmployee $event)
    {
        if(module_is_active('Webhook'))
        {
            $employee = $event->employee;
            $action = 'Convert To Employee';
            $module = 'Recruitment';
            SendWebhook::SendWebhookCall($module ,$employee,$action);
        }
    }
}
