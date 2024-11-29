<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Commission\Events\CreateCommissionPlan;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCommissionPlanLis
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
    public function handle(CreateCommissionPlan $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $commissionPlan = $event->commissionPlan;

            $userIds = explode(',', $commissionPlan->user_id);

            $users = User::WhereIn('id', $userIds)->get();
            $user_name = $users->pluck('name')->toArray();
            $user_names = implode(', ', $user_name);

            $commissionPlan->users_name = $user_names;

            $action = 'New Commission Plan';
            $module = 'Commission';
            SendWebhook::SendWebhookCall($module, $commissionPlan, $action);
        }
    }
}
