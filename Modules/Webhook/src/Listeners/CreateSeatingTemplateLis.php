<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MovieShowBookingSystem\Events\CreateSeatingTemplate;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSeatingTemplateLis
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
    public function handle(CreateSeatingTemplate $event)
    {
        if (module_is_active('Webhook')) {
            $seatingtemplate = $event->seatingtemplate;

            $web_array = [
                'Seating Layout Title' => $seatingtemplate->name,
            ];

            $action = 'New Seating Template';
            $module = 'MovieShowBookingSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
