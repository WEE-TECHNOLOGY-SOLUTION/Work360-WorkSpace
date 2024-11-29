<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Taskly\Events\CreateProject;

class CreateProjectLis
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
    public function handle(CreateProject $event)
    {
        if(module_is_active('Webhook')){
            $project = $event->project;
            $request = $event->request;
            $project->users_list = User::whereIN('email', $request->users_list)->get()->pluck('name')->toArray();
            $action = 'New Project';
            $module = 'Taskly';
            $status = SendWebhook::SendWebhookCall($module ,$project,$action);
            if($status == false)
            {
                return redirect()->route('projects.index')->with('success', __('Project Created Successfully!') . ('<br> <span class="text-danger"> '.__('Webhook call failed.').'</span>'));
            }
        }
    }
}
