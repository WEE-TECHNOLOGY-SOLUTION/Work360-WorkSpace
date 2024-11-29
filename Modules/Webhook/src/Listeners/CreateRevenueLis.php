<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Events\CreateRevenue;
use Workdo\Webhook\Entities\SendWebhook;

class CreateRevenueLis
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
    public function handle(CreateRevenue $event)
    {
        if(module_is_active('Webhook')){
            $request = $event->request;
            $revenue = $event->revenue;
            $account = \Workdo\Account\Entities\BankAccount::where('id',$request->account_id)->first();
            if($account){
                $revenue->account_bank_name = $account->bank_name;
            }
            $customer = \Workdo\Account\Entities\Customer::find($request->customer_id);
            $categories = \Workdo\ProductService\Entities\Category::where('id', $request->category_id)->where('type', 1)->first();
            $revenue->customer_name = $customer->name;
            $revenue->category_name = $categories->name;
            unset($revenue->add_receipt, $revenue->user_id,$revenue->payment_method);
            $action = 'New Revenue';
            $module = 'Account';
            SendWebhook::SendWebhookCall($module ,$revenue,$action);
        }
    }
}
