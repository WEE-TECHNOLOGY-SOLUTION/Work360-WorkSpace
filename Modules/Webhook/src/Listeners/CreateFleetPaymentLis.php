<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Fleet\Entities\Booking;
use Workdo\Fleet\Events\CreateFleetPayment;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFleetPaymentLis
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
    public function handle(CreateFleetPayment $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $payment = $event->Payment;

            $booking = Booking::find($payment->booking_id);
            $customer = User::find($booking->customer_name);

            $web_array = array(
                "Customer Name" => $customer->name,
                "Trip Date" => $booking->start_date,
                "Ammount Pay" => $payment['pay_amount'],
                "Description" => $payment['description'],
            );

            $module = 'Fleet';
            $action = 'New Fleet Payment';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
