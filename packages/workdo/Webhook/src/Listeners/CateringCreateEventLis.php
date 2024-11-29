<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CateringManagement\Events\CateringCreateEvent;
use Workdo\Webhook\Entities\SendWebhook;

class CateringCreateEventLis
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
    public function handle(CateringCreateEvent $event)
    {
        if (module_is_active('Webhook')) {
            $events = $event->events;

            $web_array = [
                'Event Title' => $events->name
            ];

            $action = 'New Event';
            $module = 'CateringManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
