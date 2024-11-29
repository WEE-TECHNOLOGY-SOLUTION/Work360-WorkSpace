<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\InnovationCenter\Events\CreateCreativityStage;
use Workdo\Webhook\Entities\SendWebhook;

class CreateCreativityStageLis
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
    public function handle(CreateCreativityStage $event)
    {
        if (module_is_active('Webhook')) {
            $Creativitystages = $event->Creativitystages;

            $web_array = [
                'Creativity Stage Title' => $Creativitystages->name,
            ];

            $action = 'New Creativity Stage';
            $module = 'InnovationCenter';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
