<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\GarageManagement\Entities\Service;
use Workdo\GarageManagement\Events\CreateJobCard;
use Workdo\Webhook\Entities\SendWebhook;

class CreateJobCardLis
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
    public function handle(CreateJobCard $event)
    {
        if (module_is_active('Webhook')) {
            $jobcard = $event->jobcard;

            $service = Service::find($jobcard->service_id);

            $web_array = [
                'Service Title' => $service->title,
                'Service Date' => $service->service_date,
                'Status' => $jobcard->status,
                'Service Items' => json_decode($jobcard->cost),
            ];

            $action = 'New Job Card';
            $module = 'GarageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
