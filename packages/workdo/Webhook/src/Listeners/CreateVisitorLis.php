<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\VisitorManagement\Entities\VisitReason;
use Workdo\VisitorManagement\Events\CreateVisitor;
use Workdo\Webhook\Entities\SendWebhook;

class CreateVisitorLis
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
    public function handle(CreateVisitor $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $visitor = $event->visitor;

            $visit_resion = VisitReason::find($request->visit_reason);

            $web_array = [
                'First Name' => $visitor->first_name,
                'Last Name' => $visitor->last_name,
                'Email' => $visitor->email,
                'Phone' => $visitor->phone,
                'Visitor Reason' => $visit_resion->reason,
                'Check In' => $request->check_in,
            ];

            $action = 'New Visitor';
            $module = 'VisitorManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
