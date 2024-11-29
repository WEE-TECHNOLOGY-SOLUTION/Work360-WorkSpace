<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MedicalLabManagement\Events\CreateTestUnit;
use Workdo\Webhook\Entities\SendWebhook;

class CreateTestUnitLis
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
    public function handle(CreateTestUnit $event)
    {
        if (module_is_active('Webhook')) {
            $testUnit = $event->testUnit;

            $web_array = [
                'Test Title' => $testUnit->name,
                'Test Code' => $testUnit->code,
            ];

            $action = 'New Test Unit';
            $module = 'MedicalLabManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
