<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\CMMS\Events\CreateSupplier;

class CreateSupplierLis
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
    public function handle(CreateSupplier $event)
    {
        if(module_is_active('Webhook')){
            $request = $event->request;
            $supplier = $event->suppliers;

            $action = 'New Supplier';
            $module = 'CMMS';
            SendWebhook::SendWebhookCall($module ,$supplier,$action);
        }
    }
}
