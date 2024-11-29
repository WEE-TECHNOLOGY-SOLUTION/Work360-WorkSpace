<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CarDealership\Events\CreatePaymentCarSale;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePaymentCarSaleLis
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
    public function handle(CreatePaymentCarSale $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $car_sale = $event->car_sale;

            $customer = User::find($car_sale->customer_id);

            $web_array = [
                'Customer Name' => $customer->name,
                'Customer Email' => $customer->email,
                'Payment Date' => $request->date,
                'Payment Amount' => $request->amount,
                'Reference' => $request->reference,
                'Description' => $request->description,
                'Issue date' => $car_sale->issue_date,
                'Due Date' => $car_sale->due_date,
                'Send Date' => $car_sale->send_date,
            ];

            $action = 'New Car Sale Payment';
            $module = 'CarDealership';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
