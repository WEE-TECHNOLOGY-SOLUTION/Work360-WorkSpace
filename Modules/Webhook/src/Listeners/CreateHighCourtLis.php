<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LegalCaseManagement\Entities\Court;
use Workdo\LegalCaseManagement\Events\CreateHighCourt;
use Workdo\Webhook\Entities\SendWebhook;

class CreateHighCourtLis
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
    public function handle(CreateHighCourt $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $highCourt = $event->highCourt;

            $court = Court::find($highCourt->court_id);

            $web_array = [
                'High Court Title' => $highCourt->name,
                'Court Name' => $court->name
            ];

            $action = 'New High Court';
            $module = 'LegalCaseManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
