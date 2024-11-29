<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\SupportTicket\Entities\Ticket;
use Workdo\SupportTicket\Entities\TicketCategory;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\SupportTicket\Events\ReplyTicket;

class ReplyTicketLis
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
    public function handle(ReplyTicket $event)
    {
        $ticket = $event->ticket;

         if(module_is_active('Webhook'))
         {
            $ticket_data = Ticket::where('id', $ticket->id)->first();

             $category = TicketCategory::where('id',$ticket_data->category)->first();
             $ticket['category'] =$category->name ;

             $action = 'New Ticket Reply';
             $module = 'SupportTicket';

             SendWebhook::SendWebhookCall($module,$ticket,$action);

         }
    }
}
