<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\VCard\Events\BusinessStatus;

class StatusChangeBusinessLis
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
    public function handle($event)
    {
        if(module_is_active('Webhook')){
            $business = $event->status;
            $action = 'Business Status Updated';
            $module = 'VCard';
            SendWebhook::SendWebhookCall($module ,$business,$action);
        }
    }
}
