<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\SupportTicket\Entities\TicketCategory;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\SupportTicket\Events\CreateTicket;

class CreateTicketLis
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
    public function handle(CreateTicket $event)
    {
        $request = $event->request;
        $ticket = $event->ticket;

        if (module_is_active('Webhook')) {
            $category = TicketCategory::where('id', $request->category)->first();
            //    $ticket['category'] =$category->name ;
            $action = 'New Ticket';
            $module = 'SupportTicket';

            SendWebhook::SendWebhookCall($module, $ticket, $action);
        }
    }
}
