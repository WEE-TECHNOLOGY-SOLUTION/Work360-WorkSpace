<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Holidayz\Events\CreateBookingCoupon;
use Workdo\Webhook\Entities\SendWebhook;

class CreateBookingCouponLis
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
    public function handle(CreateBookingCoupon $event)
    {
        if (module_is_active('Webhook')) {

            $request = $event->request;
            $coupon = $event->coupon;

            $web_array = [
                'Coupon Title' => $coupon->name,
                'Coupon Discount' => $coupon->discount,
                'Coupon Limit' => $coupon->limit,
                'Coupon Code' => $request->autoCode ? $request->autoCode : $request->manualCode,
            ];

            $action = 'New Booking Coupon';
            $module = 'Holidayz';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
