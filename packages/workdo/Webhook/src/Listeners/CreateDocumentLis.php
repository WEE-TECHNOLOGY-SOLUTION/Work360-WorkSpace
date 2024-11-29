<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Documents\Entities\DocumentType;
use Workdo\Documents\Events\CreateDocuments;
use Workdo\Taskly\Entities\Project;
use Workdo\Webhook\Entities\SendWebhook;

class CreateDocumentLis
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
    public function handle(CreateDocuments $event)
    {
        if (module_is_active('Webhook')) {
            $documents = $event->documents;

            $document_type = DocumentType::find($documents->type);
            $user = User::find($documents->user_id);
            $project = Project::find($documents->project_id);

            $web_array = [
                'Document Subject' => $documents->subject,
                'Document Type' => $document_type->name,
                'User Name' => $user->name,
                'User Email' => $user->email,
                'Project Name' => $project->name,
                'Description' => $documents->notes
            ];

            $module = 'Documents';
            $action = 'New Document';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
