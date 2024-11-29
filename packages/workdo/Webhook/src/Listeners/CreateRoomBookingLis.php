<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Holidayz\Entities\HotelCustomer;
use Workdo\Holidayz\Entities\Rooms;
use Workdo\Holidayz\Events\CreateRoomBooking;
use Workdo\Webhook\Entities\SendWebhook;

class CreateRoomBookingLis
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
    public function handle(CreateRoomBooking $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $booking = $event->booking;

            $user = HotelCustomer::find($booking->user_id);
            $room = Rooms::find($request->room_id);

            $web_array = [
                'user Name' => $user->name,
                'User Email' => $user->email,
                'CheckIn Date' => $request->check_in,
                'CheckOut Date' => $request->check_out,
                'Number Of Rooms' => $request->room,
                'Room Type' => $room->room_type,
                'Short Description' => strip_tags($room->short_description),
                'Description' => strip_tags($room->description),
                'Adults' => $request->adults,
                'Childrens' => $request->children,
                'Total Amount' => $request->total,
                'Payment Method' => $booking->payment_method,
                'Payment Status' => $booking->payment_status == 1 ? 'Paid' : 'Unpaid',
            ];

            $action = 'New Room Booking';
            $module = 'Holidayz';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
