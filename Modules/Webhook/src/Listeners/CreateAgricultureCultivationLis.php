<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Entities\AgricultureCycles;
use Workdo\AgricultureManagement\Entities\AgricultureDepartment;
use Workdo\AgricultureManagement\Entities\AgricultureOffices;
use Workdo\AgricultureManagement\Entities\AgricultureUser;
use Workdo\AgricultureManagement\Events\CreateAgricultureCultivation;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureCultivationLis
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
    public function handle(CreateAgricultureCultivation $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agriculturecultivation = $event->agriculturecultivation;

            $farmer = AgricultureUser::find($agriculturecultivation->farmer);
            $cycle = AgricultureCycles::find($agriculturecultivation->agriculture_cycle);
            $department = AgricultureDepartment::find($agriculturecultivation->department);
            $office = AgricultureOffices::find($agriculturecultivation->office);

            $web_array = [
                'Cultivation Title' => $agriculturecultivation->name,
                'Farmer' => $farmer->name,
                "Agriculture Cycle" => $cycle->name,
                "Agriculture Department" => $department->name,
                "Agriculture Office" => $office->name,
                'Cultivation Area' => $agriculturecultivation->area
            ];

            $action = 'New Agriculture Cultivation';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
