<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\DocumentTemplate\Events\DuplicateDocumentTemplate;
use Workdo\Webhook\Entities\SendWebhook;

class DuplicateDocumentTemplateLis
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
    public function handle(DuplicateDocumentTemplate $event)
    {
        if (module_is_active('Webhook')) {
            $duplicate = $event->duplicate['document'];

            $web_array = [
                'Document Template Subject' => $duplicate->subject,
                'Notes' => $duplicate->notes,
                'Status' => $duplicate->status,
                'Description' => $duplicate->description,
            ];

            $module = 'DocumentTemplate';
            $action = 'Duplicate Document Template';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
