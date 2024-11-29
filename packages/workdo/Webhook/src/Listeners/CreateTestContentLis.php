<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MedicalLabManagement\Events\CreateTestContent;
use Workdo\Webhook\Entities\SendWebhook;

class CreateTestContentLis
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
    public function handle(CreateTestContent $event)
    {
        if (module_is_active('Webhook')) {
            $testContent = $event->testContent;

            $web_array = [
                'Test Content Title' => $testContent->name,
                'Test Content Code' => $testContent->code
            ];

            $action = 'New Test Content';
            $module = 'MedicalLabManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
