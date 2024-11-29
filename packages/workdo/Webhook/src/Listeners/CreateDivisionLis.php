<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LegalCaseManagement\Entities\HighCourt;
use Workdo\LegalCaseManagement\Events\CreateDivision;
use Workdo\Webhook\Entities\SendWebhook;

class CreateDivisionLis
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
    public function handle(CreateDivision $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $division = $event->division;

            $high_court = HighCourt::find($division->highcourt_id);

            $web_array = [
                'Division Name' => $division->name,
                'High Court Title' => $high_court->name,
            ];

            $action = 'New High Court';
            $module = 'LegalCaseManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
