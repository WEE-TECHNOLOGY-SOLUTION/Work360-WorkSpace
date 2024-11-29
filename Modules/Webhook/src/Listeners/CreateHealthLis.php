<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\DairyCattleManagement\Entities\Animal;
use Workdo\DairyCattleManagement\Events\CreateHealth;
use Workdo\Webhook\Entities\SendWebhook;

class CreateHealthLis
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
    public function handle(CreateHealth $event)
    {
        if (module_is_active('Webhook')) {

            $health = $event->health;
            $animal = Animal::find($health->animal_id);

            $duration = '';
            if ($health->duration == 1) {
                $duration = 'Month';
            } elseif ($health->duration == 12) {
                $duration = 'Year';
            } elseif ($health->duration == 3) {
                $duration = 'Quarterly';
            } else {
                $duration = 'Half Year';
            }

            $web_array = [
                'Animal Name' => $animal->name,
                'Animal Breed' => $animal->breed,
                'Animal Date of Birth' => $animal->birth_date,
                'Veterinarian' => $health->veterinarian,
                'Duration' => $duration,
                'Checkup Date' => $health->date,
                'Next Checkup Date' => $health->checkup_date,
                'Diagnosis' => $health->diagnosis,
                'Treatment' => $health->treatment,
                'Price' => $health->price,
            ];

            $action = 'New Health';
            $module = 'DairyCattleManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
