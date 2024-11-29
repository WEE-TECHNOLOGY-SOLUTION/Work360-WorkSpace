<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\RepairManagementSystem\Entities\RepairOrderRequest;
use Workdo\RepairManagementSystem\Entities\RepairPart;
use Workdo\RepairManagementSystem\Events\CretaeRepairInvoice;
use Workdo\Webhook\Entities\SendWebhook;

class CretaeRepairInvoiceLis
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
    public function handle(CretaeRepairInvoice $event)
    {
        if (module_is_active('Webhook')) {
            $repair_invoice = $event->repair_invoice;

            $repair_order_request = RepairOrderRequest::find($repair_invoice->repair_id);
            $repair_parts = RepairPart::find($repair_invoice->repair_id);

            $web_array = [
                'Product Name' => $repair_order_request->product_name,
                'Product Quantity' => $repair_order_request->product_quantity,
                'Customer Name' => $repair_order_request->customer_name,
                'Customer Email' => $repair_order_request->customer_email,
                'Customer Mobile Number' => $repair_order_request->customer_mobile_no,
                'Date' => $repair_order_request->date,
                'Expire Date' => $repair_order_request->expiry_date,
                'Location' => $repair_order_request->location,
                'Repair Charge' => $repair_invoice->repair_charge,
            ];

            $action = 'New Repair Invoice';
            $module = 'RepairManagementSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
