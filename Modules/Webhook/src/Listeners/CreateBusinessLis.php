<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\VCard\Events\CreateBusiness;

class CreateBusinessLis
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
    public function handle(CreateBusiness $event)
    {
        if(module_is_active('Webhook')){
            $business = $event->business;
            $action = 'New business';
            $module = 'VCard';
            SendWebhook::SendWebhookCall($module ,$business,$action);
        }
    }
}
