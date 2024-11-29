<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GymManagement\Entities\BodyPart;
use Workdo\GymManagement\Entities\Equipment;
use Workdo\GymManagement\Events\CreateExercise;
use Workdo\Webhook\Entities\SendWebhook;

class CreateExerciseLis
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
    public function handle(CreateExercise $event)
    {
        if (module_is_active('Webhook')) {
            $exercise = $event->exercise;

            $body_part = BodyPart::find($exercise->exercise_for);
            $equipment_id = explode(',', $exercise->equipment_id);
            $equipment = Equipment::whereIn('id', $equipment_id)->get();

            $equipmentNames = collect($equipment)->pluck('name')->toArray();

            $web_array = [
                'Exercise Title' => $exercise->name,
                'Exercise For' => $body_part->name,
                'Equipments' => $equipmentNames,
            ];

            $action = 'New Exercise';
            $module = 'GymManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
