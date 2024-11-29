<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Contract\Events\CreateContract;

class CreateContractLis
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
    public function handle(CreateContract $event)
    {
        if (module_is_active('Webhook')) {
            $contract = $event->contract;
            $user = User::where('id', $contract->user_id)->first();
            $contract_type = \Workdo\Contract\Entities\ContractType::where('id', $contract->type)->first();
            $project = \Workdo\Taskly\Entities\Project::where('id', $contract->project_id)->first();
            // $contract->user_id     = $user->name;
            // $contract->type        = $contract_type->name;
            // $contract->project_id  = (isset($project->name) && !empty($project->name)) ? $project->name : '';
            $action = 'New Contract';
            $module = 'Contract';
            SendWebhook::SendWebhookCall($module, $contract, $action);
        }
    }
}
