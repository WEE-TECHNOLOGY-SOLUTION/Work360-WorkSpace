<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\TourTravelManagement\Events\CreateSeason;
use Workdo\Webhook\Entities\SendWebhook;

class CreateSeasonLis
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
    public function handle(CreateSeason $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $tourseason = $event->tourseason;

            $web_array = [
                'Season Name' => $tourseason->season_name
            ];

            $action = 'New Season';
            $module = 'TourTravelManagement';

            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
