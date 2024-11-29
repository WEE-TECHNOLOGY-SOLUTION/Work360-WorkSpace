<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ConsignmentManagement\Entities\Consignment;
use Workdo\ConsignmentManagement\Events\CreatePurchaseOrder;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePurchaseOrderLis
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
    public function handle(CreatePurchaseOrder $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $purchaseOrder = $event->purchaseOrder;

            $consignment = Consignment::find($purchaseOrder->consignment_id);
            $vendor = User::find($purchaseOrder->vendor_id);

            $web_array = [
                'Consignment Title' => $consignment->title,
                'Vendor Name' => $vendor->name,
                'Vendor Email' => $vendor->email,
                'Vendor Mobile Number' => $vendor->mobile_no,
                'Date' => $purchaseOrder->date,
                'Sub Total' => $purchaseOrder->subtotal,
                'Commission' => $purchaseOrder->commission,
                'Total Amount' => $purchaseOrder->totalamount,
            ];

            $action = 'New Purchase Order';
            $module = 'ConsignmentManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
