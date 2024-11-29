<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Lead\Entities\Deal;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Lead\Events\CreateDeal;

class CreateDealLis
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
    public function handle(CreateDeal $event)
    {
        if(module_is_active('Webhook'))
        {
            $deal = $event->deal;
            $request = $event->request;
            $clients_name = User::whereIN('id', array_filter($request->clients))->get()->pluck('name')->toArray();
            
            $deal_data = Deal::find($deal->id);
            $pipeline = \Workdo\Lead\Entities\Pipeline::where('id',$deal_data->pipeline_id)->first();
            $stage = \Workdo\Lead\Entities\DealStage::where('id',$deal_data->stage_id)->first();
            $deal->pipeline_name = $pipeline->name;
            $deal->stage_name     = $stage->name;
            $deal->clients     = (count($clients_name) > 0 ) ? implode(',',$clients_name) : 'Not Found';
            $action = 'New Deal';
            $module = 'Lead';
            SendWebhook::SendWebhookCall($module ,$deal,$action);
        }
    }
}
