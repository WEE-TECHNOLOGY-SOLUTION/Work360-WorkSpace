<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FixEquipment\Events\CreateStatus;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFixEquipmentStatus
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
    public function handle(CreateStatus $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $status = $event->status;

            $web_array = [
                'Status Title' => $status->title,
            ];

            $action = 'New Status';
            $module = 'FixEquipment';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
