<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Entities\AgricultureOffices;
use Workdo\AgricultureManagement\Events\CreateAgricultureCanal;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureCanalLis
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
    public function handle(CreateAgricultureCanal $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agriculturecanal = $event->agriculturecanal;

            $office = AgricultureOffices::find($agriculturecanal->office);

            $web_array = [
                'Agriculture Canal Title' => $agriculturecanal->name,
                'Agriculture Canal Code' => $agriculturecanal->code,
                'Agriculture Office' => $office->name
            ];

            $action = 'New Agriculture Canal';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
