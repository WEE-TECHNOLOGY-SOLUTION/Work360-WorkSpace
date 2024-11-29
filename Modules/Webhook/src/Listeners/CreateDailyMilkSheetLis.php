<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\DairyCattleManagement\Entities\Animal;
use Workdo\DairyCattleManagement\Events\CreateDailyMilkSheet;
use Workdo\Webhook\Entities\SendWebhook;

class CreateDailyMilkSheetLis
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
    public function handle(CreateDailyMilkSheet $event)
    {
        if (module_is_active('Webhook')) {
            $dailymilksheet = $event->dailymilksheet;

            $animal = Animal::find($dailymilksheet->animal_id);

            $web_array = [
                'Animal Name' => $animal->name,
                'Animal Species' => $animal->species,
                'Animal Breed' => $animal->breed,
                'Birth Date' => $animal->birth_date,
                'Start Date' => $dailymilksheet->start_date,
                'End Date' => $dailymilksheet->end_date,
                'Morning Milk Capacity' => $dailymilksheet->morning_milk,
                'Evening Milk Capacity' => $dailymilksheet->evening_milk
            ];

            $action = 'New Daily Milk Sheet';
            $module = 'DairyCattleManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
