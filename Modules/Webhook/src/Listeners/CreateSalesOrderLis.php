<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Sales\Events\CreateSalesOrder;

class CreateSalesOrderLis
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
    public function handle(CreateSalesOrder $event)
    {
        if(module_is_active('Webhook'))
        {
            $request = $event->request;
            $salesorder = $event->salesorder;
            $user = User::where('id',$request->user)->first();
            $quote = \Workdo\Sales\Entities\Quote::where('id',$request->quote)->first();
            $opportunity = \Workdo\Sales\Entities\Opportunities::where('id',$request->opportunity)->first();
            $account = \Workdo\Sales\Entities\SalesAccount::where('id',$request->account_id)->first();
            $billing_contact = \Workdo\Sales\Entities\Contact::where('id',$request->billing_contact)->first();
            $shipping_contact = \Workdo\Sales\Entities\Contact::where('id',$request->shipping_contact)->first();
            $opportunity = \Workdo\Sales\Entities\Opportunities::where('id',$request->opportunity)->first();
            $tax = \Workdo\ProductService\Entities\Tax::whereIN('id',$request->tax)->get()->pluck('name')->toArray();
            $shipping_provider = \Workdo\Sales\Entities\ShippingProvider::where('id',$request->shipping_provider)->first();

            $salesorder->user_name             = !empty($user) ?  $user->name : '';
            $salesorder->quote               = !empty($quote)?$quote->name:'';
            $salesorder->opportunity         = !empty($opportunity) ?  $opportunity->name : '';
            $salesorder->status              = !empty($salesorder) ? $salesorder->status : '';
            $salesorder->account             = !empty($account) ?  $account->name : '';
            $salesorder->billing_contact     = !empty($billing_contact) ?  $billing_contact->name : '';
            $salesorder->shipping_contact    = !empty($shipping_contact) ?  $shipping_contact->name : '';
            $salesorder->tax                 = implode(',', $tax);
            $salesorder->shipping_provider   = $shipping_provider->name;
            $action = 'New Sales Order';
            $module = 'Sales';
            SendWebhook::SendWebhookCall($module ,$salesorder,$action);
        }
    }
}
