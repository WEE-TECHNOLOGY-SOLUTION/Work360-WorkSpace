<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MovieShowBookingSystem\Events\CreateCastType;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCastTypeLis
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
    public function handle(CreateCastType $event)
    {
        if (module_is_active('Webhook')) {
            $casttype = $event->casttype;

            $web_array = [
                'Cast Type' => $casttype->name,
            ];

            $action = 'New Cast Type';
            $module = 'MovieShowBookingSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
