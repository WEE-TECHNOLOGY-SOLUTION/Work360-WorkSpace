<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\InsuranceManagement\Entities\Insurance;
use Workdo\InsuranceManagement\Entities\insuranceClaim;
use Workdo\InsuranceManagement\Entities\Policy;
use Workdo\InsuranceManagement\Events\CreateinsuranceClaimPayment;
use Workdo\Webhook\Entities\SendWebhook;

class CreateinsuranceClaimPaymentLis
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
    public function handle(CreateinsuranceClaimPayment $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $claimPayment = $event->claimPayment;

            $claim = insuranceClaim::find($claimPayment->claim_id);
            $insurance = Insurance::find($claim->insurance_id);
            $client = User::find($insurance->client_name);
            $agent = User::find($insurance->agent_name);
            $policy = Policy::find($insurance->policy_name);

            $web_array = [
                'Date' => $claimPayment->date,
                'Amount' => $claimPayment->amount,
                'Reference' => $claimPayment->reference,
                'Description' => $claimPayment->description,
                'Client Name' => $client->name,
                'Client Email' => $client->email,
                'Agent Name' => $agent->name,
                'Agent Email' => $agent->email,
                'Policy Name' => $policy->name,
            ];

            $action = 'New Insurance Claim Payment';
            $module = 'InsuranceManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
