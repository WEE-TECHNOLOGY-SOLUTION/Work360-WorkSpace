<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\RepairManagementSystem\Events\CreateRepairOrderRequest;
use Workdo\Webhook\Entities\SendWebhook;

class CreateRepairOrderRequestLis
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
    public function handle(CreateRepairOrderRequest $event)
    {
        if (module_is_active('Webhook')) {
            $repair_order_request = $event->repair_order_request;

            $web_array = [
                'Product Name' => $repair_order_request->product_name,
                'Product Quantity' => $repair_order_request->product_quantity,
                'Customer Name' => $repair_order_request->customer_name,
                'Customer Email' => $repair_order_request->customer_email,
                'Customer Mobile Number' => $repair_order_request->customer_mobile_no,
                'Order Date' => $repair_order_request->date,
                'Expire Date' => $repair_order_request->expiry_date,
                'Location' => $repair_order_request->location
            ];

            $action = 'New Repair Order Request';
            $module = 'RepairManagementSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
