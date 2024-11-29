<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Events\CreateVendor;
use Workdo\Webhook\Entities\SendWebhook;

class CreateVendorLis
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
    public function handle(CreateVendor $event)
    {
        if(module_is_active('Webhook')){
            $vendor = $event->vendor;
            unset($vendor->vendor_id,$vendor->user_id,$vendor->password);
            $action = 'New Vendor';
            $module = 'Account';
            SendWebhook::SendWebhookCall($module ,$vendor,$action);
        }
    }
}
