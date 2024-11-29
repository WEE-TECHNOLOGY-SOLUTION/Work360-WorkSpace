<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Lead\Entities\Lead;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Lead\Events\CreateLead;

class CreateLeadLis
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
    public function handle(CreateLead $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $lead = $event->lead;
            $lead_data = Lead::find($lead->id);
            $user = User::where('id', $request->user_id)->first();
            $pipeline = \Workdo\Lead\Entities\Pipeline::where('id', $lead_data->pipeline_id)->first();
            $stage = \Workdo\Lead\Entities\LeadStage::where('id', $lead_data->stage_id)->first();

            $action = 'New Lead';
            $module = 'Lead';
            SendWebhook::SendWebhookCall($module, $lead, $action);
        }
    }
}
