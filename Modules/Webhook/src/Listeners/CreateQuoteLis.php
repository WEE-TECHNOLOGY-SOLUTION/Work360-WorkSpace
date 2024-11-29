<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Sales\Events\CreateQuote;

class CreateQuoteLis
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
    public function handle(CreateQuote $event)
    {
        if(module_is_active('Webhook'))
        {
            $quote = $event->quote;
            $request = $event->request;
            $account = \Workdo\Sales\Entities\SalesAccount::where('id',$request->account_id)->first();
            $user = User::where('id',$request->user)->first();
            $opportunity = \Workdo\Sales\Entities\Opportunities::where('id',$request->opportunity)->first();
            $billing_contact = \Workdo\Sales\Entities\Contact::where('id',$request->billing_contact)->first();
            $shipping_contact = \Workdo\Sales\Entities\Contact::where('id',$request->shipping_contact)->first();
            $opportunity = \Workdo\Sales\Entities\Opportunities::where('id',$request->opportunity)->first();
            $tax = \Workdo\ProductService\Entities\Tax::whereIN('id',$request->tax)->get()->pluck('name')->toArray();
            $shipping_provider = \Workdo\Sales\Entities\ShippingProvider::where('id',$request->shipping_provider)->first();

            $quote->user_name             = !empty($user) ?  $user->name : '';
            $quote->status              = !empty($quote) ? $quote->status : '';
            $quote->opportunity         = !empty($opportunity) ?  $opportunity->name : '';
            $quote->account             = !empty($account) ?  $account->name : '';
            $quote->billing_contact     = !empty($billing_contact) ?  $billing_contact->name : '';
            $quote->shipping_contact    = !empty($shipping_contact) ?  $shipping_contact->name : '';
            $quote->tax                 = implode(',', $tax);
            $quote->shipping_provider   = $shipping_provider->name;
            $action = 'New Quote';
            $module = 'Sales';
            SendWebhook::SendWebhookCall($module ,$quote,$action);
        }
    }
}
