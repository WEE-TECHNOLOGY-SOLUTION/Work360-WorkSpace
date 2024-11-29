<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\PropertyManagement\Entities\Property;
use Workdo\PropertyManagement\Entities\PropertyUnit;
use Workdo\PropertyManagement\Events\CreatePropertyInvoice;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePropertyInvoiceLis
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
    public function handle(CreatePropertyInvoice $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $propertyInvoice = $event->propertyInvoice;

            $property = Property::find($propertyInvoice->property_id);
            $propertyUnit = PropertyUnit::find($propertyInvoice->unit_id);
            $user = User::find($propertyInvoice->unit_id);

            $web_array = [
                'Customer Name' => $user->name,
                'Customer Email' => $user->email,
                'Customer Mobile Number' => $user->mobile_no,
                'Property Title' => $property->name,
                'Property Unit Title' => $propertyUnit->name,
                'Invoice Issue Date' => $propertyInvoice->issue_date,
                'Invoice Due Date' => $propertyInvoice->due_date,
                'Invoice Total Amount' => $propertyInvoice->total_amount,
                'Status' => $propertyInvoice->status,
            ];
            $action = 'New Property Invoice';
            $module = 'PropertyManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
