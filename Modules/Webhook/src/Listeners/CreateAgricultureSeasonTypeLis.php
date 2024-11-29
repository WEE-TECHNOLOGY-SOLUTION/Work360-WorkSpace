<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Events\CreateAgricultureSeasonType;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureSeasonTypeLis
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
    public function handle(CreateAgricultureSeasonType $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agricultureseasontype = $event->agricultureseasontype;

            $web_array = [
                'Agriculture Season Type Title' => $agricultureseasontype->name,
            ];

            $action = 'New Agriculture Season Type';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
