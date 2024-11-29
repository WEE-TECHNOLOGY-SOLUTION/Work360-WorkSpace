<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Lead\Events\DealMoved;

class DealMovedLis
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
    public function handle(DealMoved $event)
    {
        if(module_is_active('Webhook'))
        {
            $deal = $event->deal;
            $newStage = \Workdo\Lead\Entities\DealStage::find($deal->stage_id);

            $deal_stages = new \Workdo\Lead\Entities\Deal;
            $deal_stages->name = $deal->name;
            $deal_stages->stage_id = $newStage->name;
            $deal_stages->order = $deal->order;
            $action = 'Deal Moved';
            $module = 'Lead';
            SendWebhook::SendWebhookCall($module ,$deal_stages,$action);
        }
    }
}


