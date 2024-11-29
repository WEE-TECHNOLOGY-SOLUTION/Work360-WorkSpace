<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ChildcareManagement\Entities\Child;
use Workdo\ChildcareManagement\Events\CreateFee;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFeeLis
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
    public function handle(CreateFee $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $childFee = $event->childFee;

            $child = Child::find($childFee->child_id);

            $web_array = [
                'Child First Name' => $child->first_name,
                'Child Last Name' => $child->last_name,
                'Start Date' => $childFee->start_date,
                'Due Date' => $childFee->due_date,
                'Items' => json_decode($childFee->items),
                'Amount' => $childFee->amount,
                'Due Amount' => $childFee->due_amount,
                'Status' => $childFee->status,
                'Notes' => $childFee->notes
            ];

            $action = 'New Fee';
            $module = 'ChildcareManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
