<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\MedicalLabManagement\Events\CreateLabTest;
use Workdo\Webhook\Entities\SendWebhook;

class CreateLabTestLis
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
    public function handle(CreateLabTest $event)
    {
        if (module_is_active('Webhook')) {
            $labTest = $event->labTest;

            $web_array = [
                'Lab Test Title' => $labTest->name,
                'Lab Test Price' => $labTest->cost,
                'Lab Tests' => json_decode($labTest->items)
            ];

            $action = 'New Lab Test';
            $module = 'MedicalLabManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
