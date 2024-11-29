<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Lead\Events\LeadMoved;

class LeadMovedLis
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
    public function handle(LeadMoved $event)
    {
        if(module_is_active('Webhook'))
        {
            $lead = $event->lead;
            $newStage = \Workdo\Lead\Entities\LeadStage::find($lead->stage_id);

            $lead_stages = new \Workdo\Lead\Entities\Lead;
            $lead_stages->name = $lead->name;
            $lead_stages->stage_name = $newStage->name;
            $lead_stages->order = $lead->order;
            $action = 'Lead Moved';
            $module = 'Lead';
            SendWebhook::SendWebhookCall($module ,$lead_stages,$action);
        }
    }
}
