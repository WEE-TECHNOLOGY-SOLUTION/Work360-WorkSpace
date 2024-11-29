<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\InsuranceManagement\Events\CreatepolicyType;
use Workdo\Webhook\Entities\SendWebhook;

class CreatepolicyTypeLis
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
    public function handle(CreatepolicyType $event)
    {
        if (module_is_active('Webhook')) {
            $PolicyType = $event->PolicyType;

            $web_array = [
                'Insurance Type Title' => $PolicyType->name,
                'Insurance Type Code' => $PolicyType->code,
            ];

            $action = 'New Policy Type';
            $module = 'InsuranceManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
