<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\DairyCattleManagement\Entities\Animal;
use Workdo\DairyCattleManagement\Entities\Breeding;
use Workdo\DairyCattleManagement\Events\CreateBreeding;
use Workdo\Webhook\Entities\SendWebhook;

class CreateBreedingLis
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
    public function handle(CreateBreeding $event)
    {
        if (module_is_active('Webhook')) {
            $breeding = $event->breeding;

            $animal = Animal::find($breeding->animal_id);

            $web_array = [
                'Animal Name' => $animal->name,
                'Animal Breed' => $animal->breed,
                'Breeding Date' => $breeding->breeding_date,
                'Breeding Gestation' => $breeding->gestation,
                'Breeding Status' => Breeding::$breedingstatus[$breeding->breeding_status],
                'Due Date' => $breeding->due_date,
                'Notes' => $breeding->note,
            ];

            $action = 'New Breeding';
            $module = 'DairyCattleManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
