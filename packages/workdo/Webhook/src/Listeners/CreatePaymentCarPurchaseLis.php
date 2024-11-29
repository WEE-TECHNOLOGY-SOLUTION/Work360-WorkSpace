<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CarDealership\Events\CreatePaymentCarPurchase;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePaymentCarPurchaseLis
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
    public function handle(CreatePaymentCarPurchase $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $car_purchase = $event->car_purchase;

            $web_array = [
                'Payment Date' => $request->date,
                'Payment Amount' => $request->amount,
                'Reference' => $request->reference,
                'Description' => $request->description,
                'Purchase Date' => $car_purchase->purchase_date,
                'Due Date' => $car_purchase->due_date,
                'Send Date' => $car_purchase->send_date,
            ];

            $action = 'New Car Purchase Payment';
            $module = 'CarDealership';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
