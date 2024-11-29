<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\TourTravelManagement\Events\CreateTransportType;
use Workdo\Webhook\Entities\SendWebhook;

class CreateTransportTypeLis
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
    public function handle(CreateTransportType $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $transport_type = $event->transport_type;

            $web_array = [
                'Transport Type Title' => $transport_type->transport_type_name,
            ];

            $action = 'New Transport Type';
            $module = 'TourTravelManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
