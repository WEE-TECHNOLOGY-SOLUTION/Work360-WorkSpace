<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Sales\Events\CreateSalesInvoice;

class CreateSalesInvoiceLis
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
    public function handle(CreateSalesInvoice $event)
    {
        if(module_is_active('Webhook'))
        {
            $request = $event->request;
            $invoice = $event->invoice;
            $user = User::where('id',$request->user)->first();
            $salesorder =  \Workdo\Sales\Entities\SalesOrder::where('id',$request->salesorder)->first();
            $quote =  \Workdo\Sales\Entities\Quote::where('id',$request->quote)->first();
            $opportunity =  \Workdo\Sales\Entities\Opportunities::where('id',$request->opportunity)->first();
            $account =  \Workdo\Sales\Entities\SalesAccount::where('id',$request->account_id)->first();
            $billing_contact =  \Workdo\Sales\Entities\Contact::where('id',$request->billing_contact)->first();
            $shipping_contact =  \Workdo\Sales\Entities\Contact::where('id',$request->shipping_contact)->first();
            $opportunity =  \Workdo\Sales\Entities\Opportunities::where('id',$request->opportunity)->first();
            $tax = \Workdo\ProductService\Entities\Tax::whereIN('id',$request->tax)->get()->pluck('name')->toArray();
            $shipping_provider =  \Workdo\Sales\Entities\ShippingProvider::where('id',$request->shipping_provider)->first();

            $invoice->user_name             = !empty($user) ?  $user->name : '';
            $invoice->salesorder          = !empty($salesorder)?$salesorder->name:'';
            $invoice->quote               = !empty($quote)?$quote->name:'';
            $invoice->opportunity         = !empty($opportunity) ?  $opportunity->name : '';
            $invoice->status              = !empty($invoice->status) ? $invoice->status : '';
            $invoice->account             = !empty($account) ?  $account->name : '';
            $invoice->billing_contact     = !empty($billing_contact) ?  $billing_contact->name : '';
            $invoice->shipping_contact    = !empty($shipping_contact) ?  $shipping_contact->name : '';
            $invoice->tax                 = implode(',', $tax);
            $invoice->shipping_provider   = $shipping_provider->name;
            $action = 'New Sales Invoice';
            $module = 'Sales';
            SendWebhook::SendWebhookCall($module ,$invoice,$action);
        }
    }
}
