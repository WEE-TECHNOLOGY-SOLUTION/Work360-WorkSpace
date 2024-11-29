<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Workflow\Events\WorkflowWebhook;

class CreateWorkflowLis
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
    public function handle(WorkflowWebhook $event)
    {
        $setting = $event->setting;
        $details = $event->details;

        SendWebhook::SendWebhookCallWorkflow($setting, $details);
    }
}
