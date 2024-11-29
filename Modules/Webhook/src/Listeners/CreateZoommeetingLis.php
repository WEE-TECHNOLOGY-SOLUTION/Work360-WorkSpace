<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\ZoomMeeting\Events\CreateZoommeeting;

class CreateZoommeetingLis
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
    public function handle(CreateZoommeeting $event)
    {
        $new = $event->new;

        if (module_is_active('Webhook')) {

            $action = 'New Zoom Meeting';
            $module = 'ZoomMeeting';
            SendWebhook::SendWebhookCall($module ,$new,$action);


        }
    }
}
