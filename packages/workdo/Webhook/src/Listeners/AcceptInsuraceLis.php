<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\InsuranceManagement\Entities\Policy;
use Workdo\InsuranceManagement\Entities\PolicyType;
use Workdo\InsuranceManagement\Events\SendInsuraceMail;
use Workdo\Webhook\Entities\SendWebhook;

class AcceptInsuraceLis
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
    public function handle(SendInsuraceMail $event)
    {
        if (module_is_active('Webhook')) {
            $insurance = $event->insurance;
            $uArr = $event->uArr;

            $client = User::find($insurance->client_name);
            $agent = User::find($insurance->agent_name);
            $policy_type = PolicyType::find($insurance->policy_type);
            $policy = Policy::find($insurance->policy_name);

            $web_array = [
                'Client Name' => $client->name,
                'Client Email' => $client->email,
                'Agent Name' => $agent->name,
                'Agent Email' => $agent->email,
                'Policy Name' => $policy->name,
                'Policy Type' => $policy_type->name,
                'Duration' => $insurance->duration,
                'Expiry Date' => $insurance->expiry_date,
            ];

            $action = 'New Insurance Accept';
            $module = 'InsuranceManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
