<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CMMS\Entities\Location;
use Workdo\CMMS\Entities\Part;
use Workdo\CMMS\Events\CreatePms;
use Workdo\Webhook\Entities\SendWebhook;

class CreatePmsLis
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
    public function handle(CreatePms $event)
    {
        if (module_is_active('Webhook')) {
            $pms = $event->pms;
            $request = $event->request;

            $location = Location::find($pms->location_id);
            $partNames = Part::whereIn('id', $request->parts)->pluck('name');
            $partNamesString = implode(', ', $partNames->toArray());

            $web_array = [
                'PMS Title' => $pms->name,
                "Location" => $location->name,
                "Parts" => $partNamesString,
                "Tags" => $pms->tags,
                "Description" => $request->description,
            ];

            $action = 'New Pms';
            $module = 'CMMS';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
