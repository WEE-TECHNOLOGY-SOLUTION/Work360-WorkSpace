<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FreightManagementSystem\Entities\FreightContainer;
use Workdo\FreightManagementSystem\Events\CreateFreightShippingInvoice;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFreightShippingInvoiceLis
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
    public function handle(CreateFreightShippingInvoice $event)
    {
        if (module_is_active('Webhook')) {
            $shipping = $event->shipping;
            $invoice = $event->invoice;

            $container = FreightContainer::find($shipping->container);

            $web_array = [
                'Customer Name' => $shipping->customer_name,
                'Customer Email' => $shipping->customer_email,
                'Direction' => $shipping->direction,
                'Transport' => $shipping->transport,
                'Loading Port' => $shipping->loading_port,
                'Discharge Port' => $shipping->discharge_port,
                'Vessel' => $shipping->vessel,
                'Date' => $shipping->date,
                'Barcode' => $shipping->barcode,
                'Tracking Number' => $shipping->tracking_no,
                'Container' => $container->name,
                'Quantity' => $shipping->quantity,
                'Volume' => $shipping->volume,
                'Invoice Date' => $invoice->invoice_date,
                'Due Date' => $invoice->due_date,
                'Amount' => $invoice->amount
            ];

            $action = 'New Freight Invoice';
            $module = 'FreightManagementSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
