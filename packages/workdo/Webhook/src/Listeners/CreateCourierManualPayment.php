<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CourierManagement\Entities\CourierBranch;
use Workdo\CourierManagement\Entities\Servicetype;
use Workdo\CourierManagement\Events\Manualpaymentdatastore;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCourierManualPayment
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
    public function handle(Manualpaymentdatastore $event)
    {
        if (module_is_active('Webhook')) {
            $courierPayment = $event->courierPayment;
            $receiverDetails = $event->receiverDetails;
            $request = $event->request;

            $service_type = Servicetype::find($receiverDetails->service_type);
            $source_branch = CourierBranch::find($receiverDetails->source_branch);
            $destination_branch = CourierBranch::find($receiverDetails->destination_branch);

            $web_array = [
                'Tracking ID' => $courierPayment->tracking_id,
                'Sender Name' => $receiverDetails->sender_name,
                'Sender Mobile Number' => $receiverDetails->sender_mobileno,
                'Sender Email' => $receiverDetails->sender_email,
                'Delivery Address' => $receiverDetails->delivery_address,
                'Reciver Name' => $receiverDetails->receiver_name,
                'Reviver Mobile Number' => $receiverDetails->receiver_mobileno,
                'Courier Service Type' => $service_type->service_type,
                'Courier Source Branch' => $source_branch->branch_name,
                'Destination Branch' => $destination_branch->branch_name,
                'Payment Method' => $receiverDetails->payment_type,
                'Payment Status' => $receiverDetails->payment_status,
            ];

            $action = 'New Courier Payment';
            $module = 'CourierManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
