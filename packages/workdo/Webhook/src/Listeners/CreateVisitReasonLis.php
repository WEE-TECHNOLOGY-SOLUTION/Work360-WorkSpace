<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\VisitorManagement\Events\CreateVisitReason;
use Workdo\Webhook\Entities\SendWebhook;

class CreateVisitReasonLis
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
    public function handle(CreateVisitReason $event)
    {
        if (module_is_active('Webhook')) {
            $visitorReason = $event->visitorReason;

            $web_array = [
                'Visior Reason' => $visitorReason->reason,
            ];
            
            $action = 'New Visit Reason';
            $module = 'VisitorManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
