<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\DairyCattleManagement\Entities\Animal;
use Workdo\DairyCattleManagement\Events\CreateWeight;
use Workdo\Webhook\Entities\SendWebhook;

class CreateWeightLis
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
    public function handle(CreateWeight $event)
    {
        if (module_is_active('Webhook')) {
            $weight = $event->weight;

            $animal = Animal::find($weight->animal_id);

            $web_array = [
                'Animal Name' => $animal->name,
                'Animal Species' => $animal->species,
                'Animal Breed' => $animal->breed,
                'Birth Date' => $animal->birth_date,
                'Date' => $weight->date,
                'Age' => $weight->age,
                'Weight' => $weight->weight
            ];

            $action = 'New Weight';
            $module = 'DairyCattleManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
