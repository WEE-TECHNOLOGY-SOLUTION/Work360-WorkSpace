<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\SalesAgent\Events\SalesAgentRequestReject;
use Workdo\Webhook\Entities\SendWebhook;

class SalesAgentRequestRejectLis
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
    public function handle(SalesAgentRequestReject $event)
    {
        if (module_is_active('Webhook')) {
            $program = $event->program;
            $user_id = $event->user_id;

            $user = User::find($user_id);

            $webArray = [
                'Program Title' => $program->name,
                'Program Start Date' => $program->from_date,
                'Program End Date' => $program->to_date,
                'Description' => $program->description,
                'Sales Agent Name' => $user->name,
                'Sales Agent Email' => $user->email,
                'Request Status' => 'Reject',
            ];

            $action = 'Sales Agent Request Reject';
            $module = 'SalesAgent';

            SendWebhook::SendWebhookCall($module, $webArray, $action);
        }
    }
}
