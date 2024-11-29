<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\InnovationCenter\Events\CreateCreativityStatus;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCreativityStatusLis
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
    public function handle(CreateCreativityStatus $event)
    {
        if (module_is_active('Webhook')) {
            $Creativitystatus = $event->Creativitystatus;

            $web_array = [
                'Creativity Status' => $Creativitystatus->name,
            ];

            $action = 'New Creativity Status';
            $module = 'InnovationCenter';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
