<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\InsuranceManagement\Events\CreatePolicy;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePolicyLis
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
    public function handle(CreatePolicy $event)
    {
        if (module_is_active('Webhook')) {
            $policy = $event->policy;

            $web_array = [
                'Policy Name' => $policy->name,
                'Policy Type' => $policy->type,
                'Duration' => $policy->duration,
                'Minimum Duration' => $policy->minimum_duration,
                'Maximum Duration' => $policy->maximum_duration,
                'Policy Amount' => $policy->amount,
                'Agent Commission' => $policy->agent_commission,
                'Commission Amount' => $policy->commission_amount,
            ];

            $action = 'New Policy';
            $module = 'InsuranceManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
