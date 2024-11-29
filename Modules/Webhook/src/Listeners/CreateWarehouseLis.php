<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Pos\Events\CreateWarehouse;

class CreateWarehouseLis
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
    public function handle(CreateWarehouse $event)
    {
        if(module_is_active('Webhook')){
            $warehouse = $event->warehouse;
            $action = 'New Warehouse';
            $module = 'Pos';
            SendWebhook::SendWebhookCall($module ,$warehouse,$action);
        }
    }
}
