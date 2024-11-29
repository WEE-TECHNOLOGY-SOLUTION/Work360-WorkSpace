<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\InsuranceManagement\Entities\Policy;
use Workdo\InsuranceManagement\Events\CreateInsurance;
use Workdo\Webhook\Entities\SendWebhook;

class CreateInsuranceLis
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
    public function handle(CreateInsurance $event)
    {
        if (module_is_active('Webhook')) {
            $insurance = $event->insurance;

            $client = User::find($insurance->client_name);
            $agent = User::find($insurance->agent_name);
            $policy = Policy::find($insurance->policy_name);

            $web_array = [
                'Client Name' => $client->name,
                'Client Email' => $client->email,
                'Policy Type' => $insurance->policy_type,
                'Policy Name' => $policy->name,
                'Agent Name' => $agent->name,
                'Agent Email' => $agent->email,
                'Duration' => $insurance->duration
            ];

            $action = 'New Insurance';
            $module = 'InsuranceManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
