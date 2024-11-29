<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\DocumentTemplate\Events\ConvertDocumentTemplate;
use Workdo\Webhook\Entities\SendWebhook;

class ConvertDocumentTemplateLis
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
    public function handle(ConvertDocumentTemplate $event)
    {
        if (module_is_active('Webhook')) {
            $convert = $event->convert['document'];

            $web_array = [
                'Document Template Subject' => $convert->subject,
                'Notes' => $convert->notes,
                'Status' => $convert->status,
            ];

            $module = 'DocumentTemplate';
            $action = 'Convert Document Template';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
