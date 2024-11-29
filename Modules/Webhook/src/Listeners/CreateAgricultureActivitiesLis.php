<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Entities\AgricultureCrop;
use Workdo\AgricultureManagement\Events\CreateAgricultureActivities;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureActivitiesLis
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
    public function handle(CreateAgricultureActivities $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agriculture_activity = $event->agriculture_activity;

            $crop = AgricultureCrop::find($agriculture_activity->crop);

            $web_array = [
                'Agriculture Activity' => $agriculture_activity->name,
                'Agriculture Crop' => $crop->name,
                'Agriculture Season' => $agriculture_activity->agri_season,
                'Agriculture Date' => $agriculture_activity->agriculture_date,
                'Harvest Date' => $agriculture_activity->harvest_date
            ];

            $action = 'New Agriculture Activities';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
