<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CourierManagement\Events\Couriertrackingstatuscreate;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCourierTrackingStatusLis
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
    public function handle(Couriertrackingstatuscreate $event)
    {
        if (module_is_active('Webhook')) {
            $trackingStatus = $event->trackingStatus;

            $web_array = [
                'Status title' => $trackingStatus->status_name,
                'Status Color' => $trackingStatus->status_color,
                'Status Icon' => $trackingStatus->icon_name
            ];

            $action = 'New Courier Tracking Status';
            $module = 'CourierManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
