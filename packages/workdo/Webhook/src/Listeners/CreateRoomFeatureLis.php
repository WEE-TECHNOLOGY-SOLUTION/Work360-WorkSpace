<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Holidayz\Events\CreateRoomFeature;
use Workdo\Webhook\Entities\SendWebhook;

class CreateRoomFeatureLis
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
    public function handle(CreateRoomFeature $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $feature = $event->feature;

            $web_array = [
                'Title' => $feature->name,
                'Icon' => $feature->icon
            ];

            $action = 'New Features';
            $module = 'Holidayz';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
