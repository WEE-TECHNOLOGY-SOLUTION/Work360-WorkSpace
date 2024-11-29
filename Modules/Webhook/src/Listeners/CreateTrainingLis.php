<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Training\Events\CreateTraining;

class CreateTrainingLis
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
    public function handle(CreateTraining $event)
    {
        if(module_is_active('Webhook')){
            $training = $event->training;
            $action = 'New Training';
            $module = 'Training';
            SendWebhook::SendWebhookCall($module ,$training,$action);
        }
    }
}
