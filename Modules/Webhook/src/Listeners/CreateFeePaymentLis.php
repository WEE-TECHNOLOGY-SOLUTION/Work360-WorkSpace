<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ChildcareManagement\Entities\Child;
use Workdo\ChildcareManagement\Entities\ChildFee;
use Workdo\ChildcareManagement\Events\CreateFeePayment;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFeePaymentLis
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
    public function handle(CreateFeePayment $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $childFeePayment = $event->childFeePayment;

            $childFee = ChildFee::find($childFeePayment->childfee_id);
            $child = Child::find($childFee->child_id);

            $web_array = [
                'Child First Name' => $child->first_name,
                'Child Last Name' => $child->last_name,
                'Payment Date' => $childFeePayment->date,
                'Payment Amount' => $childFeePayment->amount,
                'Payment Method' => $childFeePayment->method,
                'Total Fee' => $childFee->amount,
                'Payment Status' => $childFeePayment->status,
                'Notes' => $childFeePayment->note
            ];

            $action = 'New Fee Payment';
            $module = 'ChildcareManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
