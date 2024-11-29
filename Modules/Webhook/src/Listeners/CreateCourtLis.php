<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LegalCaseManagement\Events\CreateCourt;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCourtLis
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
    public function handle(CreateCourt $event)
    {
        if (module_is_active('Webhook')) {
            $court = $event->court;

            $web_array = [
                'Court Name' => $court->name,
                'Court Type' => $court->type,
                'Court Location' => $court->location,
                'Court Address' => $court->address,
            ];

            $action = 'New Court';
            $module = 'LegalCaseManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
