<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\InsuranceManagement\Entities\Insurance;
use Workdo\InsuranceManagement\Entities\Policy;
use Workdo\InsuranceManagement\Events\RejectinsuranceClaim;
use Workdo\Webhook\Entities\SendWebhook;

class RejectinsuranceClaimLis
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
    public function handle(RejectinsuranceClaim $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $id = $event->id;
            $claim = $event->claim;

            $insurance = Insurance::find($id);
            $client = User::find($insurance->client_name);
            $agent = User::find($insurance->agent_name);
            $policy = Policy::find($insurance->policy_name);

            $web_array = [
                'Policy Name' => $policy->name,
                'Policy Type' => $insurance->policy_type,
                'Client Name' => $client->name,
                'Client Email' => $client->email,
                'Agent Name' => $agent->name,
                'Agent Email' => $agent->email,
                'Insurance Reject Reason' => $claim->reject_reason,
                'Action Date' => $claim->action_date,
                'Insurance Claim Status' => 'Reject'
            ];

            $action = 'Reject Insurance Claim';
            $module = 'InsuranceManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
