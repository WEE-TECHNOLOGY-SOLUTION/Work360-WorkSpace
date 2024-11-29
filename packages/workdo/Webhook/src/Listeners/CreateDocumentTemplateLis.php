<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Documents\Entities\DocumentType;
use Workdo\DocumentTemplate\Events\CreateDocumentTemplate;
use Workdo\Webhook\Entities\SendWebhook;

class CreateDocumentTemplateLis
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
    public function handle(CreateDocumentTemplate $event)
    {
        if (module_is_active('Webhook')) {
            $template = $event->template;

            $template_type = DocumentType::find($template->type);

            $web_array = [
                'Document Template Subject' => $template->subject,
                'Document Type' => $template_type->name,
                'Notes' => $template->notes
            ];

            $module = 'DocumentTemplate';
            $action = 'New Document Template';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
