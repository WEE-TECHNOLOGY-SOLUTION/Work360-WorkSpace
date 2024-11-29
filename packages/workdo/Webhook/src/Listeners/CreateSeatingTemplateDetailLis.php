<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MovieShowBookingSystem\Events\CreateSeatingTemplateDetail;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSeatingTemplateDetailLis
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
    public function handle(CreateSeatingTemplateDetail $event)
    {
        if (module_is_active('Webhook')) {
            $seatingtemplatedetail = $event->seatingtemplatedetail;

            $web_array = [
                'Ticket Type' => $seatingtemplatedetail->ticket_type,
                'Ticket Price' => $seatingtemplatedetail->price,
                'Ticket Max Seat' => $seatingtemplatedetail->max_seat,
            ];

            $action = 'New Seating Template Details';
            $module = 'MovieShowBookingSystem';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
