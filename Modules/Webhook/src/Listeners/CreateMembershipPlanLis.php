<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GymManagement\Events\CreateMembershipPlan;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMembershipPlanLis
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
    public function handle(CreateMembershipPlan $event)
    {
        if (module_is_active('Webhook')) {
            $membershipplan = $event->membershipplan;

            $web_array = [
                'Membership Plan Title' => $membershipplan->name,
                'Membership Fee' => $membershipplan->fee,
                'Membership Duration' => $membershipplan->duration
            ];

            $action = 'New Membership Plan';
            $module = 'GymManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
