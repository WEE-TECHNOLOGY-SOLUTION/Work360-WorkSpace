<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LegalCaseManagement\Events\CreateCaseInitiator;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCaseInitiatorLis
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
    public function handle(CreateCaseInitiator $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $caseInitiator = $event->caseInitiator;

            $web_array = [
                'Case Initiator Name' => $caseInitiator->name,
                'Case Initiator Email' => $caseInitiator->email,
                'Case Initiator Type' => $caseInitiator->type,
            ];

            $action = 'New Case Initiator';
            $module = 'LegalCaseManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
