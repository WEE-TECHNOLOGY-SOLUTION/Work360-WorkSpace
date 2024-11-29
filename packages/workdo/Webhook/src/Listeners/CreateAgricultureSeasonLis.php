<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\AgricultureManagement\Entities\AgricultureSeason;
use Workdo\AgricultureManagement\Events\CreateAgricultureSeason;
use Workdo\Webhook\Entities\SendWebhook;

class CreateAgricultureSeasonLis
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
    public function handle(CreateAgricultureSeason $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $agricultureseason = $event->agricultureseason;

            $season_type = AgricultureSeason::find($agricultureseason->season);

            $web_array = [
                'Agriculture Season Title' => $agricultureseason->name,
                'Agriculture Season Type' => $season_type->name,
                'Year' => $agricultureseason->year,
            ];

            $action = 'New Agriculture Season';
            $module = 'AgricultureManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
