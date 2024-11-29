<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\DairyCattleManagement\Entities\Animal;
use Workdo\DairyCattleManagement\Entities\DailyMilkSheet;
use Workdo\DairyCattleManagement\Events\CreateMilkInventory;
use Workdo\Webhook\Entities\SendWebhook;

class CreateMilkInventoryLis
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
    public function handle(CreateMilkInventory $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $milkinventory = $event->milkinventory;

            $idArray = explode(',', $milkinventory->daily_milksheet_id);

            $milkSheetRecords = DailyMilkSheet::whereIn('id', $idArray)->get();

            $resultArray = [];

            foreach ($milkSheetRecords as $dailyMilkSheet) {

                $animalInfo = Animal::select('name', 'breed', 'birth_date')->find($dailyMilkSheet->animal_id);

                $animalInfoArray = $animalInfo->toArray();

                $resultArray[] = [
                    'Animal Info' => $animalInfoArray,
                    'Date' => $milkinventory->date,
                    'Start Date' => $dailyMilkSheet->start_date,
                    'End Date' => $dailyMilkSheet->end_date,
                    'Morning Milk' => $dailyMilkSheet->morning_milk,
                    'Evening Milk' => $dailyMilkSheet->evening_milk,
                ];
            }
            $action = 'New Milk Inventory';
            $module = 'DairyCattleManagement';
            SendWebhook::SendWebhookCall($module, $resultArray, $action);
        }
    }
}
