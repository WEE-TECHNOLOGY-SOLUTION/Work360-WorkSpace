<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\BeverageManagement\Events\CreateCollectionCenter;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCollectionCenterLis
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
    public function handle(CreateCollectionCenter $event)
    {
        if (module_is_active('Webhook')) {
            $collection_center = $event->collection_center;

            $web_array = [
                'Location' => $collection_center->location_name,
                'Status' => $collection_center->status == 1 ? 'Active' : 'Inactive',
            ];

            $action = 'New Collection Center';
            $module = 'BeverageManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
