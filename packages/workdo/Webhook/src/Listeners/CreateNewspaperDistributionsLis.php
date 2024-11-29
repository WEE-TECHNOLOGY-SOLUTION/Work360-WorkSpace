<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Newspaper\Events\CreateNewspaperDistributions;
use Workdo\Webhook\Entities\SendWebhook;

class CreateNewspaperDistributionsLis
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
    public function handle(CreateNewspaperDistributions $event)
    {
        if (module_is_active('Webhook')) {
            $distribution = $event->distribution;

            $web_array = [
                'Title' => $distribution->name,
                'Address' => $distribution->address
            ];

            $action = 'New Newspaper Distribution Center';
            $module = 'Newspaper';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
