<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\SupportTicket\Entities\TicketCategory;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\SupportTicket\Events\CreatePublicTicket;

class CreatePublicTicketLis
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
    public function handle(CreatePublicTicket $event)
    {
        $ticket = $event->ticket;

         if(module_is_active('Webhook',$ticket->created_by))
         {
             $category = TicketCategory::where('id',$ticket->category)->first();
             $ticket->category = !empty($category)?$category->name:'' ;
             $action = 'New Ticket';
             $module = 'SupportTicket';
             $company_id =$ticket->created_by;
             $workspace_id =$ticket->workspace_id;
            SendWebhook::SendWebhookCall($module,$ticket,$action,$workspace_id);

         }
    }
}
