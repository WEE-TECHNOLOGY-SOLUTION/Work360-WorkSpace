<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CourierManagement\Events\Courierbranchcreate;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCourierbranchLis
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
    public function handle(Courierbranchcreate $event)
    {
        if (module_is_active('Webhook')) {
            $branchData = $event->branchData;

            $web_array = [
                'Branch Name' => $branchData->branch_name,
                'Branch Location' => $branchData->branch_location,
                'Branch City' => $branchData->city,
                'Branch State' => $branchData->state,
                'Branch Country' => $branchData->country
            ];

            $action = 'New Courier Branch';
            $module = 'CourierManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
