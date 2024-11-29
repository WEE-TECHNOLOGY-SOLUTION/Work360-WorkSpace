<?php

namespace Workdo\Webhook\Listeners;

use App\Models\Proposal;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;

class StatusChangeProposalLis
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
    public function handle($event)
    {
        if(module_is_active('Webhook')){
            $proposal = $event->proposal;
            $proposal->status    = Proposal::$statues[$proposal->status];
            unset($proposal->customer_id,$proposal->issue_date,$proposal->category_id,$proposal->is_convert,$proposal->converted_invoice_id,$proposal->workspace,$proposal->created_by);
            $action = 'Proposal status updated';
            $module = 'general';
            SendWebhook::SendWebhookCall($module ,$proposal,$action);
        }
    }
}
