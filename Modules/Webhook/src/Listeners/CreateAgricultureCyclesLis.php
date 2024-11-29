<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Events\CreateAgricultureCycles;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureCyclesLis
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
    public function handle(CreateAgricultureCycles $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agriculturecycles = $event->agriculturecycles;

            $web_array = [
                'Agriculture Cycle Title' => $agriculturecycles->name
            ];

            $action = 'New Agriculture Cycle';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
