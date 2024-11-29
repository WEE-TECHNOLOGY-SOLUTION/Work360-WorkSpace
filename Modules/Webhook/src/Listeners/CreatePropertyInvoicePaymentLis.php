<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\PropertyManagement\Entities\Property;
use Workdo\PropertyManagement\Entities\PropertyUnit;
use Workdo\PropertyManagement\Events\CreatePropertyInvoicePayment;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePropertyInvoicePaymentLis
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
    public function handle(CreatePropertyInvoicePayment $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $invoicePayment = $event->invoicePayment;

            $property = Property::find($invoicePayment->property_id);
            $propertyUnit = PropertyUnit::find($invoicePayment->unit_id);
            $user = User::find($invoicePayment->unit_id);

            $web_array = [
                'Customer Name' => $user->name,
                'Customer Email' => $user->email,
                'Customer Mobile Number' => $user->mobile_no,
                'Property Title' => $property->name,
                'Property Unit Title' => $propertyUnit->name,
                'Invoice Issue Date' => $invoicePayment->issue_date,
                'Invoice Due Date' => $invoicePayment->due_date,
                'Invoice Total Amount' => $invoicePayment->total_amount,
                'Status' => $invoicePayment->status,
            ];

            $action = 'New Property Invoice Payment';
            $module = 'PropertyManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
