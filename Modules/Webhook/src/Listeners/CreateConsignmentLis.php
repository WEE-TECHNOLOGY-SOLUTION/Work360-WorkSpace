<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ConsignmentManagement\Entities\ConsignmentProduct;
use Workdo\ConsignmentManagement\Events\CreateConsignment;
use Workdo\Webhook\Entities\SendWebhook;

class CreateConsignmentLis
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
    public function handle(CreateConsignment $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $consignment = $event->consignment;

            $items = $request->input('items', []);

            $web_array = [
                'Consignment Title' => $consignment->title,
                'Consignment Commission' => $consignment->commission,
                'Consignment Date' => $consignment->date,
                'Consignment Item' => $items,
                'Consignment Sub Total' => $consignment->subtotal,
                'Consignment Total Amount' => $consignment->totalamount,
            ];

            $action = 'New Consignment';
            $module = 'ConsignmentManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
