<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ConsignmentManagement\Entities\Consignment;
use Workdo\ConsignmentManagement\Events\CreateSaleOrder;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSaleOrderLis
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
    public function handle(CreateSaleOrder $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $saleOrder = $event->saleOrder;

            $consignment = Consignment::find($saleOrder->consignment_id);
            $customer = User::find($saleOrder->customer_id);

            $web_array = [
                'Consignment Title' => $consignment->title,
                'Customer Name' => $customer->name,
                'Customer Email' => $customer->email,
                'Customer Mobile Number' => $customer->mobile_no,
                'Sales Order Date' => $saleOrder->date,
                'Sub Total' => $saleOrder->subtotal,
                'Commission' => $saleOrder->commission,
                'Total Amount' => $saleOrder->totalamount,
            ];
            $action = 'New Sale Order';
            $module = 'ConsignmentManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
