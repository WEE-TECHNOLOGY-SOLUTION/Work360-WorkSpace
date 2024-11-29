<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Entities\AgricultureActivities;
use Workdo\AgricultureManagement\Entities\AgricultureCycles;
use Workdo\AgricultureManagement\Entities\AgricultureDepartment;
use Workdo\AgricultureManagement\Entities\AgricultureOffices;
use Workdo\AgricultureManagement\Entities\AgricultureUser;
use Workdo\AgricultureManagement\Events\AssignActivityCultivation;
use Workdo\Webhook\Entities\SendWebhook;

class AssignActivityCultivationLis
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
    public function handle(AssignActivityCultivation $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agriculturecultivation = $event->agriculturecultivation;

            $farmer = AgricultureUser::find($agriculturecultivation->farmer);
            $cycle = AgricultureCycles::find($agriculturecultivation->agriculture_cycle);
            $department = AgricultureDepartment::find($agriculturecultivation->department);
            $office = AgricultureOffices::find($agriculturecultivation->office);
            $activitesArray = json_decode($agriculturecultivation->activites);
            $activity_data = AgricultureActivities::whereIn('id', $activitesArray)->get();

            $activityArray = [];

            foreach ($activity_data as $activity) {
                $attributes = [
                    'Activity Name' => $activity->name,
                    'Agriculture Date' => $activity->agriculture_date,
                    'Harvest Date' => $activity->harvest_date,
                    'Agriculture Season' => $activity->agri_season,
                ];
                $activityArray[] = $attributes;
            }

            $web_array = [
                'Agriculture Cultivation Title' => $agriculturecultivation->name,
                'Farmer' => $farmer->title,
                'Agriculture Cycle' => $cycle->name,
                'Agriculture Department' => $department->name,
                'Agriculture Office' => $office->name,
                'Agriculture Activites' => $activityArray,
            ];

            $action = 'Assign Cultivation Activity';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
