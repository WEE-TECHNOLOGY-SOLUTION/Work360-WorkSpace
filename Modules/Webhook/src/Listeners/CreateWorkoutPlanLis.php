<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GymManagement\Events\CreateWorkoutPlan;
use Workdo\Webhook\Entities\SendWebhook;

class CreateWorkoutPlanLis
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
    public function handle(CreateWorkoutPlan $event)
    {
        if (module_is_active('Webhook')) {
            $workoutplan = $event->workoutplan;

            $web_array = [
                'Workout Plan Title' => $workoutplan->name,
                'Workout Plan Days' => $workoutplan->days
            ];

            $action = 'New Workout Plan';
            $module = 'GymManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
