<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Workflow\Entities\Workflowdothis;
use Workdo\Workflow\Entities\WorkflowModule;
use Workdo\Workflow\Events\CreateWorkflow;

class StoreWorkflowLis
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
    public function handle(CreateWorkflow $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $workflow = $event->Workflow;

            $taskIds = explode(',', $workflow->do_this);

            $do_this = Workflowdothis::WhereIn('id', $taskIds)->get();
            $submodules = $do_this->pluck('submodule')->toArray();
            $do_this_name = implode(', ', $submodules);

            $module_name = WorkflowModule::find($workflow->module_name);

            $workflow->event_title = $module_name->module;
            $workflow->subevent_title = $module_name->submodule;
            $workflow->do_this_title = $do_this_name;

            $action = 'New Workflow';
            $module = 'Workflow';

            SendWebhook::SendWebhookCall($module, $workflow, $action);
        }
    }
}
