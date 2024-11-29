<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Entities\AgricultureCycles;
use Workdo\AgricultureManagement\Entities\AgricultureEquipment;
use Workdo\AgricultureManagement\Entities\AgricultureFleet;
use Workdo\AgricultureManagement\Entities\AgricultureProcess;
use Workdo\AgricultureManagement\Entities\AgricultureSeason;
use Workdo\AgricultureManagement\Events\CreateAgricultureCrop;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureCropLis
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
    public function handle(CreateAgricultureCrop $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agriculturecrop = $event->agriculturecrop;

            $season = AgricultureSeason::find($agriculturecrop->season);
            $cycle = AgricultureCycles::find($agriculturecrop->cycles);

            $fleetArray = json_decode($agriculturecrop->fleet);
            $equipmentArray = json_decode($agriculturecrop->equipment);
            $processArray = json_decode($agriculturecrop->process);

            $fleets = AgricultureFleet::whereIn('id', $fleetArray)->get();
            $equipments = AgricultureEquipment::whereIn('id', $equipmentArray)->get();
            $process = AgricultureProcess::whereIn('id', $processArray)->get();

            $fleetsArray = [];
            $equipmentsArray = [];
            $process_Array = [];

            foreach ($fleets as $fleet) {
                $attributes = [
                    'Name' => $fleet->name,
                    'Capacity' => $fleet->capacity,
                    'Price' => $fleet->price,
                    'Quantity' => $fleet->quantity
                ];
                $fleetsArray[] = $attributes;
            }


            foreach ($equipments as $equipment) {
                $attributes = [
                    'Name' => $equipment->name,
                    'Price' => $equipment->price,
                    'Quantity' => $equipment->quantity
                ];
                $equipmentsArray[] = $attributes;
            }

            foreach ($process as $proces) {
                $attributes = [
                    'Name' => $proces->name,
                    'Hours' => $proces->hours,
                    'Description' => $proces->description
                ];
                $process_Array[] = $attributes;
            }

            $web_array = [
                'Agriculture Corp Title' => $agriculturecrop->name,
                'Agriculture Date' => $agriculturecrop->agriculture_date,
                'Harvest Date' => $agriculturecrop->harvest_date,
                'Fleets' => $fleetsArray,
                'Equipments' => $equipmentsArray,
                'Agriculture Process' => $process_Array,
            ];

            $action = 'New Agriculture Crop';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
