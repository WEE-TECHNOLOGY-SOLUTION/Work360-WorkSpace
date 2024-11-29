<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\SupportTicket\Entities\Ticket;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\SupportTicket\Events\ReplyPublicTicket;

class ReplyPublicTicketLis
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
    public function handle(ReplyPublicTicket $event)
    {

        $ticket = $event->conversion;

      $ticket = Ticket::where('id', '=', $ticket->ticket_id)->first();
      if(module_is_active('Webhook',$ticket->created_by))
      {

            $action = 'New Ticket';
            $module = 'SupportTicket';
            $company_id = $ticket->created_by;
            $workspace_id = $ticket->workspace_id;

            SendWebhook::SendWebhookCall($module,$ticket,$action,$workspace_id);
          
        }
    }
}
