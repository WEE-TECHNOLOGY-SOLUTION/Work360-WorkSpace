<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MovieShowBookingSystem\Events\CreateShowType;
use Workdo\Webhook\Entities\SendWebhook;

class CreateShowTypeLis
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
    public function handle(CreateShowType $event)
    {
        if (module_is_active('Webhook')) {
            $showtype = $event->showtype;

            $web_array = [
                'Show Type' => $showtype->name
            ];

            $action = 'New Show Type';
            $module = 'MovieShowBookingSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
