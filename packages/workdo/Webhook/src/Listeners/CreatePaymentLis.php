<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Account\Events\CreatePayment;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePaymentLis
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
    public function handle(CreatePayment $event)
    {
        if(module_is_active('Webhook')){
            $request = $event->request;
            $payments = $event->payment;
            $payments                = new \Workdo\Account\Entities\Payment();
            $payments->date           = $request->date;
            $payments->amount         = $request->amount;
            $payments->reference      = $request->reference;
            $payments->description    = $request->description;
            $account = \Workdo\Account\Entities\BankAccount::where('id',$request->account_id)->first();
            if($account)
            {
                $payments->account_bank_name = $account->bank_name;
            }
            $customer = \Workdo\Account\Entities\Vender::find($request->vendor_id);
            if($customer)
            {
                $payments->vendor_name = $customer->name;
            }
            $categories = \Workdo\ProductService\Entities\Category::where('id', $request->category_id)->where('type', 2)->first();
            if($categories)
            {
                $payments->category_name = $categories->name;
            }
            $payments->vendor_name = $customer->name;
            $action = 'New Payment';
            $module = 'Account';
            SendWebhook::SendWebhookCall($module ,$payments,$action);
        }
    }
}
