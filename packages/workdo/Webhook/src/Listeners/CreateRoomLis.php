<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Holidayz\Events\CreateRoom;
use Workdo\Webhook\Entities\SendWebhook;

class CreateRoomLis
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
    public function handle(CreateRoom $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $room = $event->room;

            $web_array = [
                'Room Type' => $room->room_type,
                'Short Description' => strip_tags($room->short_description),
                'Adults' => $room->adults,
                'Childrens' => $room->children,
                'Total Rooms' => $room->total_room,
                'Base Price' => $room->base_price,
                'Final Price' => $room->final_price,
                'Description' => strip_tags($room->description)
            ];

            $action = 'New Room';
            $module = 'Holidayz';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
