<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\CMMS\Events\CreateLocation;
use Workdo\Webhook\Entities\SendWebhook;

class CreateLocationLis
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
    public function handle(CreateLocation $event)
    {
        if (module_is_active('Webhook')) {
            $location = $event->location;

            $company = User::find($location->created_by);

            $web_array = [
                'Location' => $location['name'],
                'Address' => $location['address'],
                'Name' => $company['name'],
            ];

            $module = 'CMMS';
            $action = 'New Location';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
