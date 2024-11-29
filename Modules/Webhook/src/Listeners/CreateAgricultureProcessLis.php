<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Events\CreateAgricultureProcess;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureProcessLis
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
    public function handle(CreateAgricultureProcess $event)
    {
        if (module_is_active('Webhook')) {
            $agricultureprocess = $event->agricultureprocess;
            $web_array = [
                "Process Title" => $agricultureprocess->name,
                "Process Hours" => $agricultureprocess->hours,
                "Process Description" => $agricultureprocess->description,
            ];

            $action = 'New Agriculture Process';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
