<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FixEquipment\Events\CreateDepreciation;
use Workdo\Webhook\Entities\SendWebhook;

class CreateDepreciationLis
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
    public function handle(CreateDepreciation $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $depreciation = $event->depreciation;

            $web_array = [
                'Depreciation Title' => $depreciation->title,
                'Depteciation Rate' => $depreciation->rate
            ];

            $action = 'New Depreciation';
            $module = 'FixEquipment';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
