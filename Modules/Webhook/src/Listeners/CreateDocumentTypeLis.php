<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Documents\Events\CreateDocumentsType;
use Workdo\Webhook\Entities\SendWebhook;

class CreateDocumentTypeLis
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
    public function handle(CreateDocumentsType $event)
    {
        if (module_is_active('Webhook')) {
            $documentsType = $event->documentsType;

            $web_array = [
                'Document Type Title' => $documentsType->name,
            ];

            $module = 'Documents';
            $action = 'New Document Type';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
